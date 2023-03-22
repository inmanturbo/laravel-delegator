<?php

namespace Inmanturbo\Delegator;

use Inmanturbo\Delegator\CandidateFinder\CandidateFinderCollection;
use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Contracts\CandidateFinder;
use Illuminate\Contracts\Foundation\Application;

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
            if(! in_array(get_class($candidateFinder), $this->getCandidateFinderClassNames())) {
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