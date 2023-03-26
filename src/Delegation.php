<?php

namespace Inmanturbo\Delegator;

use Illuminate\Contracts\Foundation\Application;
use Inmanturbo\Delegator\CandidateFinder\CandidateFinderCollection;
use Inmanturbo\Delegator\CandidateFinder\Contracts\CandidateFinder;
use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class Delegation
{
    use UsesDelegatorConfig;

    public function __construct(public Application $app)
    {
    }

    public function start(): void
    {
        $this->registerCandidateFinderCollection()
            ->configureRequests();
    }

    public function end(): void
    {
        collect($this->app['config']['delegator']['candidates'])
            ->pluck('model')
            ->each(fn (CandidateModel $candidate) => $candidate::forgetCurrent());
    }

    protected function registerCandidateFinderCollection(): self
    {
        $this->app->singleton(CandidateFinderCollection::class, function () {
            return new CandidateFinderCollection(
                $this->getCandidateFinderClassNames()
            );
        });

        return $this;
    }

    protected function configureRequests(): self
    {
        if (! $this->app->runningInConsole()) {
            $this->determineCurrentCandidates();
        }

        return $this;
    }

    protected function determineCurrentCandidates(): void
    {
        $this->app->make(CandidateFinderCollection::class)->each(function (CandidateFinder $candidateFinder) {
            if (! in_array(get_class($candidateFinder), $this->getCandidateFinderClassNames())) {
                return;
            }

            $candidate = $candidateFinder->findForRequest($this->app['request']);

            $candidate?->makeCurrent();
        });
    }

    protected function getCandidateFinderClassNames(): array
    {
        return collect($this->app['config']['delegator']['candidates'])->pluck('candidate_finder')->toArray();
    }
}
