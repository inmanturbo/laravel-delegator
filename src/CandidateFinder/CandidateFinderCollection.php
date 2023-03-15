<?php

namespace Inmanturbo\Delegator\CandidateFinder;

use Illuminate\Support\Collection;

class CandidateFinderCollection extends Collection
{
    public function __construct($candidateFinderClassNames)
    {
        $candidateFinders = collect($candidateFinderClassNames)
            ->map(function ($candidateFinderClassName){ 
              $instance = app()->make($candidateFinderClassName);

              $reflection = new \ReflectionClass($instance);

              // make sure the class implements the CandidateFinder interface
                if (!$reflection->implementsInterface(CandidateFinder::class)) {
                    throw new \Exception("Class {$candidateFinderClassName} does not implement the CandidateFinder interface");
                }

                return $instance;
            })
            ->toArray();

        parent::__construct($candidateFinders);
    }
}