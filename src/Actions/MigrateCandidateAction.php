<?php

namespace Inmanturbo\Delegator\Actions;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCandidateAction
{
    protected bool $fresh = false;

    protected bool $seed = false;

    protected OutputInterface $output;

    public function fresh(bool $fresh = true): self
    {
        $this->fresh = $fresh;

        return $this;
    }

    public function seed(bool $seed = true): self
    {
        $this->seed = $seed;

        return $this;
    }

    public function output(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function execute($candidate): self
    {
        $candidate->execute(function () {
            $migrationCommand = $this->fresh ? 'migrate:fresh' : 'migrate';
            $outputBuffer = $this->output ?? null;

            Artisan::call($migrationCommand, $this->getOptions(), $outputBuffer);
        });

        return $this;
    }

    protected function getOptions(): array
    {
        $options = ['--force' => true];

        if ($this->seed) {
            $options['--seed'] = true;
        }

        return $options;
    }
}