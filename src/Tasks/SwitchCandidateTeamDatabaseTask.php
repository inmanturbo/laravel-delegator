<?php

namespace Inmanturbo\Delegator\Tasks;

use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class SwitchCandidateTeamDatabaseTask implements SwitchCandidateTask
{
    public function makeCurrent(CandidateModel $model): void
    {
        $model->teamDatabase->makeCurrent();
    }

    public function forgetCurrent($candidateConfigKey): void
    {
        $model = config("delegator.candidates.{$candidateConfigKey}.model");

        (new $model)->teamDatabase()->forgetCurrent();
    }
}