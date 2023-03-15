<?php

namespace Inmanturbo\Delegator;

use Inmanturbo\Delegator\Actions\MakeQueueTenantAwareAction;
use Inmanturbo\Delegator\CandidateFinder\CandidateFinderCollection;
use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Contracts\CandidateFinder;
use Illuminate\Contracts\Foundation\Application;
use Inmanturbo\Delegator\Models\Contracts\Tenant;

class Delegator
{
    use UsesDelegatorConfig;

    public function __construct(public Application $app)
    {
    }

    public function start(): void
    {
        $this->bindTenantModelClass()
            ->registerCandidateFinderCollection()
            ->configureRequests()
            ->configureQueue();
    }

    protected function configureQueue(): self
    {
        $this
        ->getDelegatorActionClass(
            candidateConfigKey: $this->determineWhichCandidateIsBeingUsedAsTenant(),
            actionName: 'make_queue_tenant_aware_action',
            actionClass: MakeQueueTenantAwareAction::class
        )
        ->execute();

        return $this;
    }

    protected function registerCandidateFinderCollection(): self
    {

        $this->app->singleton(CandidateFinderCollection::class, function () {
            return new CandidateFinderCollection(
                $this->getCandidateFinderClassNames()
            );
        });

        return $this;
    }

    protected function configureRequests(): self
    {
        if (! $this->app->runningInConsole()) {
            $this->determineCurrentCandidates();
        }

        return $this;
    }

    protected function determineCurrentCandidates(): void
    {
        $this->app->make(CandidateFinderCollection::class)->each(function (CandidateFinder $candidateFinder) {
            if(! in_array(get_class($candidateFinder), $this->getCandidateFinderClassNames())) {
                return;
            }

            $candidate = $candidateFinder->findForRequest($this->app['request']);

            $candidate?->makeCurrent();
        });
    }

    protected function getCandidateFinderClassNames(): array
    {
        return collect($this->app['config']['delegator']['candidates'])->pluck('candidate_finder')->toArray();
    }

    protected function bindTenantModelClass(): self
    {
        $this->app->bind(
            abstract: Tenant::class, 
            concrete: fn () => $this->app['config']['delegator']['candidates'][$this->determineWhichCandidateIsBeingUsedAsTenant()]['model'],
            shared: false
        );

        return $this;
    }
}