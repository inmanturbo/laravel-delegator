<?php

use Illuminate\Http\Request;
use Inmanturbo\Delegator\CandidateFinder\DomainCandidateFinder;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;

beforeEach(function () {
    $this->candidateFinder = new DomainCandidateFinder(TeamDatabase::class);
});

it('can find a candidate for the current domain', function () {
    $tenant = TeamDatabase::factory()->create(['domain' => 'my-domain.com']);

    $request = Request::create('https://my-domain.com');

    expect($tenant->id)->toEqual($this->candidateFinder->findForRequest($request)->id);
});

it('will return null if there are no candidate', function () {
    $request = Request::create('https://my-domain.com');

    expect($this->candidateFinder->findForRequest($request))->toBeNull();
});

it('will return null if no candidate can be found the current domain', function () {
    TeamDatabase::factory()->create(['domain' => 'my-domain.com']);

    $request = Request::create('https://another-domain.com');

    expect($this->candidateFinder->findForRequest($request))->toBeNull();
});
