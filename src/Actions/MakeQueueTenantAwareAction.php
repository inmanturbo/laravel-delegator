<?php

namespace Inmanturbo\Delegator\Actions;

use Inmanturbo\Delegator\Exceptions\CurrentTenantCouldNotBeDeterminedInTenantAwareJob;
use Inmanturbo\Delegator\Jobs\NotTenantAware;
use Inmanturbo\Delegator\Jobs\TenantAware;
use Inmanturbo\Delegator\Models\Concerns\UsesTenantModel;
use Inmanturbo\Delegator\Models\Contracts\Tenant;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Support\Arr;

class MakeQueueTenantAwareAction
{
    use UsesTenantModel;

    public function execute(): void
    {
        $this
            ->listenForJobsBeingQueued()
            ->listenForJobsBeingProcessed()
            ->listenForJobsRetryRequested();
    }

    protected function listenForJobsBeingQueued(): static
    {
        app('queue')->createPayloadUsing(function ($connectionName, $queue, $payload) {
            $queueable = $payload['data']['command'];

            if (! $this->isTenantAware($queueable)) {
                return [];
            }

            return ['tenantId' => $this->getTenantModel()->current()?->id];
        });

        return $this;
    }

    protected function listenForJobsBeingProcessed(): static
    {
        app('events')->listen(JobProcessing::class, function (JobProcessing $event) {
            $this->getTenantModel()::forgetCurrent();

            if (array_key_exists('tenantId', $event->job->payload())) {
                $this->findTenant($event)->makeCurrent();
            }
        });

        return $this;
    }

    protected function listenForJobsRetryRequested(): static
    {
        app('events')->listen(JobRetryRequested::class, function (JobRetryRequested $event) {
            $this->getTenantModel()::forgetCurrent();

            if (array_key_exists('tenantId', $event->payload())) {
                $this->findTenant($event)->makeCurrent();
            }
        });

        return $this;
    }

    protected function isTenantAware(object $queueable): bool
    {
        $reflection = new \ReflectionClass($this->getJobFromQueueable($queueable));

        if ($reflection->implementsInterface(TenantAware::class)) {
            return true;
        }

        if ($reflection->implementsInterface(NotTenantAware::class)) {
            return false;
        }

        $candidateConfigKey = $this->getTenantModel()->getCandidateConfigKey();

        if (in_array($reflection->name, config("delegator.candidates.{$candidateConfigKey}.tenant_aware_jobs"))) {
            return true;
        }

        if (in_array($reflection->name, config("delegator.candidates.{$candidateConfigKey}.not_tenant_aware_jobs"))) {
            return false;
        }

        return config("delegator.candidates.{$candidateConfigKey}.queueable_to_job") === null;
    }

    protected function getEventPayload($event): ?array
    {
        return match (true) {
            $event instanceof JobProcessing => $event->job->payload(),
            $event instanceof JobRetryRequested => $event->payload(),
            default => null,
        };
    }

    protected function findTenant(JobProcessing|JobRetryRequested $event): Tenant
    {
        $tenantId = $this->getEventPayload($event)['tenantId'] ?? null;

        if (! $tenantId) {
            $event->job->delete();

            throw CurrentTenantCouldNotBeDeterminedInTenantAwareJob::noIdSet($event);
        }


        /** @var \App\Models\Contracts\Tenant $tenant */
        if (! $tenant = $this->getTenantModel()::find($tenantId)) {
            $event->job->delete();

            throw CurrentTenantCouldNotBeDeterminedInTenantAwareJob::noTenantFound($event);
        }

        return $tenant;
    }

    protected function getJobFromQueueable(object $queueable)
    {
        $candidateConfigKey = $this->getTenantModel()->getCandidateConfigKey();
        
        $job = Arr::get(config("delegator.candidates.{$candidateConfigKey}.queueable_to_job"), $queueable::class);

        if (! $job) {
            return $queueable;
        }

        if (method_exists($queueable, $job)) {
            return $queueable->{$job}();
        }

        return $queueable->$job;
    }
}