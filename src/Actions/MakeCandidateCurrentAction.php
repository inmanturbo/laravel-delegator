<?php

namespace Inmanturbo\Delegator\Actions;

use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Events\MadeCandidateCurrentEvent;
use Inmanturbo\Delegator\Events\MakingCandidateCurrentEvent;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;
use Inmanturbo\Delegator\Tasks\TasksCollection;

class MakeCandidateCurrentAction
{
    use UsesDelegatorConfig;

    protected TasksCollection $tasksCollection;

    public function __construct(
        $tasks
    ) {
        $this->tasksCollection = new TasksCollection($tasks);
    }

    public function execute(CandidateModel $candidate)
    {
        event(new MakingCandidateCurrentEvent($candidate));
            

        $this
            ->performTasksToMakeCandidateCurrent($candidate)
            ->bindAsCurrentCandidate($candidate);

        event(new MadeCandidateCurrentEvent($candidate));

        return $this;
    }

    protected function performTasksToMakeCandidateCurrent(CandidateModel $candidate): self
    {
        $this->tasksCollection->each(fn (SwitchCandidateTask $task) => $task->makeCurrent($candidate));

        return $this;
    }

    protected function bindAsCurrentCandidate(CandidateModel $candidate): self
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        $containerKey = config("delegator.candidates.{$candidateConfigKey}.current_candidate_container_key");

        app()->forgetInstance($containerKey);

        app()->instance($containerKey, $candidate);

        return $this;
    }
}