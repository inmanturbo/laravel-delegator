<?php

namespace Inmanturbo\Delegator\Commands\Concerns;

use Illuminate\Support\Arr;
use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait CandidateAware
{
    use UsesDelegatorConfig;

    protected $candidateConfigKey;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $candidateConfigKeys = $this->argument('candidateConfigKey') ? 
            Arr::wrap($this->argument('candidateConfigKey')) : 
            array_keys(config('delegator.candidates'));
        
        $candidates = Arr::wrap($this->option('candidates'));

        return collect($candidateConfigKeys)
            ->map(fn ($candidateConfigKey) => $this->executeForCandidate($candidateConfigKey, $candidates))
            ->sum();
    }

    protected function executeForCandidate($candidateConfigKey, $candidates)
    {
        $this->candidateConfigKey = $candidateConfigKey;

        $candidateQuery = $this->getCandidateModel($candidateConfigKey)::query()
            ->when(! blank($candidates), function ($query) use ($candidates, $candidateConfigKey) {
                collect($this->getCandidateArtisanSearchFields($candidateConfigKey))
                    ->each(fn ($field) => $query->orWhereIn($field, $candidates));
            });

        if ($candidateQuery->count() === 0) {
            $this->error('No candidates(s) found.');

            return -1;
        }

        return $candidateQuery
            ->cursor()
            ->map(fn ($candidate) => $candidate->execute(fn () => (int) $this->laravel->call([$this, 'handle'])))
            ->sum();
    }

    protected function getCandidateModel($candidateConfigKey): string
    {
        return config("delegator.candidates.{$candidateConfigKey}.model");
    }
}