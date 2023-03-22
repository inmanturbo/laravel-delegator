<?php

namespace Inmanturbo\Delegator\Actions;

use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Events\ForgettingCurrentCandidateEvent;
use Inmanturbo\Delegator\Events\ForgotCurrentCandidateEvent;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;
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

    public function execute(CandidateModel $candidate)
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        event(new ForgettingCurrentCandidateEvent($candidate));

        $this
            ->performTaskToForgetCurrentCandidate($candidateConfigKey)
            ->clearBoundCurrentCandidate($candidate);

        event(new ForgotCurrentCandidateEvent($candidate));
    }

    protected function performTaskToForgetCurrentCandidate($candidateConfigKey): self
    {
        $this->tasksCollection->each(fn (SwitchCandidateTask $task) => $task->forgetCurrent($candidateConfigKey));

        return $this;
    }

    protected function clearBoundCurrentCandidate(CandidateModel $candidate)
    {
        $candidateConfigKey = $candidate::getCandidateConfigKey();

        $containerKey = config("delegator.candidates.{$candidateConfigKey}.current_candidate_container_key");

        app()->forgetInstance($containerKey);
    }
}