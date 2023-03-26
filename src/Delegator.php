<?php

namespace Inmanturbo\Delegator;

class Delegator
{
    public static function execute(callable $callable)
    {
        $candidateModels = collect(config('delegator.candidates'))->pluck('model')->map(fn ($candidate) => $candidate::current());

        $candidateModels->each(fn ($candidate) => $candidate?->forgetCurrent());

        $result = $callable();

        $candidateModels->each(fn ($candidate) => $candidate?->makeCurrent());

        return $result;
    }
}
