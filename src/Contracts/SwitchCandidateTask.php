<?php

namespace Inmanturbo\Delegator\Contracts;

use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

interface SwitchCandidateTask
{
    public function makeCurrent(CandidateModel $model): void;

    public function forgetCurrent($candidateConfigKey): void;
}