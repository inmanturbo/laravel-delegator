<?php

namespace Inmanturbo\Delegator\Tests\TestClasses;

use Illuminate\Console\Command;
use Inmanturbo\Delegator\Commands\Concerns\CandidateAware;

class CandidateNoopCommand extends Command
{
    use CandidateAware;

    protected $signature = 'candidate:noop {candidateConfigKey=team_database} {--candidate=*}';

    protected $description = 'Execute noop for candidate(s)';

    public function handle()
    {
        $this->line('Candidate ID is '. TeamDatabase::current()->id);
    }
}