<?php

namespace Inmanturbo\Delegator\CandidateFinder;

use Illuminate\Support\Collection;
use Inmanturbo\Delegator\CandidateFinder\Contracts\CandidateFinder;

class CandidateFinderCollection extends Collection
{
    public function __construct($candidateFinderClassNames)
    {
        $candidateFinders = collect($candidateFinderClassNames)
        ->map(function ($candidateParameters, $candidateClass) {
            if (is_array($candidateParameters) && is_numeric($candidateClass)) {
                $candidateClass = array_key_first($candidateParameters);
                $candidateParameters = $candidateParameters[$candidateClass];
            }

            if (is_numeric($candidateClass)) {
                $candidateClass = $candidateParameters;
                $candidateParameters = [];
            }

            $instance = app()->makeWith($candidateClass, $candidateParameters);

            $reflection = new \ReflectionClass($instance);

            // make sure the class implements the CandidateFinder interface
            if (! $reflection->implementsInterface(CandidateFinder::class)) {
                throw new \Exception("Class {$candidateClass} does not implement the CandidateFinder interface");
            }

            return $instance;
        })
        ->toArray();

        parent::__construct($candidateFinders);
    }
}
