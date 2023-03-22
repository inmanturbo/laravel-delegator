<?php

use Inmanturbo\Delegator\Delegator;
use Inmanturbo\Delegator\Tests\TestClasses\Team;

beforeEach(function () {
    $this->candidate = Team::factory()->create();
});

it('will execute a callable as delegator and then restore the previous candidates', function () {
    $this->candidate->makeCurrent();

    $response = Delegator::execute(fn () => Team::current());

    expect($response)->toBeNull();

    expect($this->candidate->id)->toEqual(Team::current()->id);
});