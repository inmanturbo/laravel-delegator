<?php

use Illuminate\Support\Facades\Event;
use Inmanturbo\Delegator\Events\ForgettingCurrentCandidateEvent;
use Inmanturbo\Delegator\Events\ForgettingCurrentTenantEvent;
use Inmanturbo\Delegator\Events\ForgotCurrentCandidateEvent;
use Inmanturbo\Delegator\Events\ForgotCurrentTenantEvent;
use Inmanturbo\Delegator\Events\MadeCandidateCurrentEvent;
use Inmanturbo\Delegator\Events\MadeTenantCurrentEvent;
use Inmanturbo\Delegator\Events\MakingCandidateCurrentEvent;
use Inmanturbo\Delegator\Events\MakingTenantCurrentEvent;
use Inmanturbo\Delegator\Models\Contracts\Tenant;
use Inmanturbo\Delegator\Tests\TestClasses\Team;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;

beforeEach(function () {
    $this->tenant = Team::factory()->create();

    $this->candidate = $this->tenant->teamDatabase;

    $this->candidateConfigKey = $this->candidate->getCandidateConfigKey();

    $this->containerKey = config("delegator.candidates.{$this->candidateConfigKey}.current_candidate_container_key");
});

it('can get a current candidate', function () {
    expect(TeamDatabase::current())->toBeNull();

    $this->candidate->makeCurrent();

    expect(TeamDatabase::current())->toBe($this->candidate);
});

it('can get the current tenant', function () {
    expect($this->tenant)->toBeInstanceOf(Tenant::class);

    expect($this->candidate)->not()->toBeInstanceOf(Tenant::class);

    expect(app(Tenant::class)->current())->toBeNull();

    $this->tenant->makeCurrent();

    $this->candidate->makeCurrent();

    expect(app(Tenant::class)->current())->toBe($this->tenant);
    expect(app(Tenant::class)->current())->not()->toBe($this->candidate);
});

it('will bind a current candidate to the container', function () {

    expect(app()->has($this->containerKey))->toBeFalse();

    $this->candidate->makeCurrent();

    expect(app()->has($this->containerKey))->toBeTrue();
    expect(app($this->containerKey))->toBe($this->candidate);
});

it('can forget a current candidate', function () {
    $this->candidate->makeCurrent();

    expect(TeamDatabase::current())->toBe($this->candidate);
    expect(app()->has($this->containerKey))->toBeTrue();

    $this->candidate->forgetCurrent();

    expect(TeamDatabase::current())->toBeNull();
    expect(app()->has($this->containerKey))->toBeFalse();
});

it('can check if a current candidate has been set', function () {
    expect($this->candidate->checkCurrent())->toBeFalse();

    $this->candidate->makeCurrent();

    expect($this->candidate->checkCurrent())->toBeTrue();

    $this->candidate->forgetCurrent();

    expect($this->candidate->checkCurrent())->toBeFalse();
});

it('can check if a particular candidate is the current one', function () {
    /** @var \Inmanturbo\Delegator\Models\Contracts\CandidateModel $candidate */
    $candidate = TeamDatabase::factory()->create();

     /** @var \Inmanturbo\Delegator\Models\Contracts\CandidateModel $anotherCandidate */
    $anotherCandidate = TeamDatabase::factory()->create();

    expect($candidate->isCurrent())->toBeFalse()
        ->and($anotherCandidate->isCurrent())->toBeFalse();

    $candidate->makeCurrent();
    expect($candidate->isCurrent())->toBeTrue()
        ->and($anotherCandidate->isCurrent())->toBeFalse();

    $anotherCandidate->makeCurrent();
    expect($candidate->isCurrent())->toBeFalse()
        ->and($anotherCandidate->isCurrent())->toBeTrue();

    TeamDatabase::forgetCurrent();
    expect($candidate->isCurrent())->toBeFalse()
        ->and($anotherCandidate->isCurrent())->toBeFalse();
});

it('will fire off events when making a candidate current', function () {
    Event::fake();

    Event::assertNotDispatched(MakingCandidateCurrentEvent::class);
    Event::assertNotDispatched(MadeCandidateCurrentEvent::class);

    $this->candidate->makeCurrent();

    Event::assertDispatched(MakingCandidateCurrentEvent::class);
    Event::assertDispatched(MadeCandidateCurrentEvent::class);
});

it('will fire off events when making a tenant current', function () {
    Event::fake();

    Event::assertNotDispatched(MakingTenantCurrentEvent::class);
    Event::assertNotDispatched(MadeTenantCurrentEvent::class);

    $this->tenant->makeCurrent();

    Event::assertDispatched(MakingTenantCurrentEvent::class);
    Event::assertDispatched(MadeTenantCurrentEvent::class);
});

it('will fire off events when forgetting the current candidate', function() {
    Event::fake();

    $this->candidate->makeCurrent();

    Event::assertNotDispatched(ForgettingCurrentCandidateEvent::class);
    Event::assertNotDispatched(ForgotCurrentCandidateEvent::class);

    TeamDatabase::forgetCurrent();

    Event::assertDispatched(ForgettingCurrentCandidateEvent::class);
    Event::assertDispatched(ForgotCurrentCandidateEvent::class);
});

it('will fire off events when forgetting the current tenant', function() {
    Event::fake();

    $this->tenant->makeCurrent();

    Event::assertNotDispatched(ForgettingCurrentTenantEvent::class);
    Event::assertNotDispatched(ForgotCurrentTenantEvent::class);

    Team::forgetCurrent();

    Event::assertDispatched(ForgettingCurrentTenantEvent::class);
    Event::assertDispatched(ForgotCurrentTenantEvent::class);
});

it('will not fire off events when forgetting the current tenant when not current tenant is set', function() {
    Event::fake();

    app(Tenant::class)->forgetCurrent();

    Event::assertNotDispatched(ForgettingCurrentTenantEvent::class);
    Event::assertNotDispatched(ForgotCurrentTenantEvent::class);
});

it('will not fire off events when forgetting a current candidate when not current candidate is set', function() {
    Event::fake();

    TeamDatabase::forgetCurrent();

    Event::assertNotDispatched(ForgettingCurrentCandidateEvent::class);
    Event::assertNotDispatched(ForgotCurrentCandidateEvent::class);
});

it('will execute a callable and then restore the previous state', function () {
    TeamDatabase::forgetCurrent();

    expect(TeamDatabase::current())->toBeNull();

    $response = $this->candidate->execute(function ($candidate) {
        expect(TeamDatabase::current()->id)->toEqual($candidate->id);

        return $candidate->id;
    });

    expect(TeamDatabase::current())->toBeNull();

    expect($this->candidate->id)->toEqual($response);
});

it('will execute a delayed callback in tenant context', function () {
    $tenant = app(Tenant::class);

    $tenant->forgetCurrent();

    expect($tenant->current())->toBeNull();

    $callback = $this->tenant->callback(function (Tenant $tenant) {
        expect($tenant->current()->id)->toEqual($tenant->id);

        return $tenant->id;
    });
    expect($tenant->current())->toBeNull();

    $response = $callback();

    expect($tenant->current())->toBeNull();

    expect($this->tenant->id)->toBe($response);
});