<?php

namespace Inmanturbo\Delegator\Actions;

use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Events\ForgettingCurrentCandidateEvent;
use Inmanturbo\Delegator\Events\ForgettingCurrentTenantEvent;
use Inmanturbo\Delegator\Events\ForgotCurrentCandidateEvent;
use Inmanturbo\Delegator\Events\ForgotCurrentTenantEvent;
use Inmanturbo\Delegator\Tasks\TasksCollection;

class ForgetCurrentCandidateAction
{
    use UsesDelegatorConfig;

    protected TasksCollection $tasksCollection;

    public function __construct(
        $tasks
    ) {
        $this->tasksCollection = new TasksCollection($tasks);
    }

    public function execute($candidate)
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        $candidate->getCandidateConfigKey() === $this->determineWhichCandidateIsBeingUsedAsTenant()
            ? event(new ForgettingCurrentTenantEvent($candidate))
            : event(new ForgettingCurrentCandidateEvent($candidate));

        $this
            ->performTaskToForgetCurrentCandidate($candidateConfigKey)
            ->clearBoundCurrentCandidate($candidate);

        $candidate->getCandidateConfigKey() === $this->determineWhichCandidateIsBeingUsedAsTenant()
            ? event(new ForgotCurrentTenantEvent($candidate))
            : event(new ForgotCurrentCandidateEvent($candidate));
    }

    protected function performTaskToForgetCurrentCandidate($candidateConfigKey): self
    {
        $this->tasksCollection->each(fn (SwitchCandidateTask $task) => $task->forgetCurrent($candidateConfigKey));

        return $this;
    }

    protected function clearBoundCurrentCandidate($candidate)
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        $containerKey = config("delegator.candidates.{$candidateConfigKey}.current_candidate_container_key");

        app()->forgetInstance($containerKey);
    }
}