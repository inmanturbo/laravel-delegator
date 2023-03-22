<?php

namespace Inmanturbo\Delegator\Tests\TestClasses;

use Illuminate\Cache\Repository;
use Inmanturbo\Delegator\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class DummyTask implements SwitchCandidateTask
{
    public Repository $config;

    public int $a;

    public int $b;

    public bool $madeCurrentCalled = false;

    public bool $forgotCurrentCalled = false;

    public function __construct(Repository $config, int $a = 0, int $b = 0)
    {
        $this->config = $config;
        $this->a = $a;
        $this->b = $b;
    }

    public function makeCurrent(CandidateModel $candidateModel): void
    {
        $this->madeCurrentCalled = true;
    }

    public function forgetCurrent($candidateConfigKey): void
    {
        $this->forgotCurrentCalled = false;
    }
}