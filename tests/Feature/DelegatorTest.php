<?php

use Inmanturbo\Delegator\Delegator;
use Inmanturbo\Delegator\Tests\TestClasses\Team;

beforeEach(function () {
    $this->tenant = Team::factory()->create();
});

it('will execute a callable as landlord and then restore the previous tenant', function () {
    $this->tenant->makeCurrent();

    $response = Delegator::execute(fn () => Team::current());

    expect($response)->toBeNull();

    expect($this->tenant->id)->toEqual(Team::current()->id);
});