<?php

namespace Inmanturbo\Delegator\Actions;

use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Tasks\TasksCollection;

class MakeCandidateCurrentAction
{

    protected TasksCollection $tasksCollection;

    public function __construct(
        $tasks
    ) {
        $this->tasksCollection = new TasksCollection($tasks);
    }

    public function execute($candidate)
    {

        $this
            ->performTasksToMakeCandidateCurrent($candidate)
            ->bindAsCurrentCandidate($candidate);

        return $this;
    }

    protected function performTasksToMakeCandidateCurrent($candidate): self
    {
        $this->tasksCollection->each(fn (SwitchCandidateTask $task) => $task->makeCurrent($candidate));

        return $this;
    }

    protected function bindAsCurrentCandidate($candidate): self
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        $containerKey = config("delegator.candidates.{$candidateConfigKey}.current_candidate_container_key");

        app()->forgetInstance($containerKey);

        app()->instance($containerKey, $candidate);

        return $this;
    }
}