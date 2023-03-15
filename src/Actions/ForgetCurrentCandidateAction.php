<?php

namespace Inmanturbo\Delegator\Actions;

use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Tasks\TasksCollection;

class ForgetCurrentCandidateAction
{
    protected TasksCollection $tasksCollection;

    public function __construct(
        $tasks
    ) {
        $this->tasksCollection = new TasksCollection($tasks);
    }

    public function execute($candidate)
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        $this
            ->performTaskToForgetCurrentCandidate($candidateConfigKey)
            ->clearBoundCurrentCandidate($candidate);
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