<?php

use Inmanturbo\Delegator\CandidateFinder\CandidateFinderCollection;
use Inmanturbo\Delegator\CandidateFinder\DomainCandidateFinder;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;

it('will instantiate all class names', function () {
    $finderCollection = new CandidateFinderCollection([
        DomainCandidateFinder::class => ['candidateModel' => TeamDatabase::class],
    ]);

    expect($finderCollection->first())->toBeInstanceOf(DomainCandidateFinder::class);
});
