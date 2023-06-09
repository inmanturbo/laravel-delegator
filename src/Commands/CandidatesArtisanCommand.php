<?php

namespace Inmanturbo\Delegator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Inmanturbo\Delegator\Commands\Concerns\CandidateAware;

class CandidatesArtisanCommand extends Command
{
    use CandidateAware;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:artisan {artisanCommand} {candidateConfigKey?} {--candidate=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! $artisanCommand = $this->argument('artisanCommand')) {
            $artisanCommand = $this->ask('Which artisan command do you want to run for all candidates?');
        }

        $artisanCommand = addslashes($artisanCommand);

        $candidateModel = config("delegator.candidates.{$this->candidateConfigKey}.model");

        $candidate = $candidateModel::current();

        $this->line('');
        $this->info("Running command for candidate `{$candidate->name}` (id: {$candidate->getKey()})...");
        $this->line('---------------------------------------------------------');

        Artisan::call($artisanCommand, [], $this->output);
    }
}
