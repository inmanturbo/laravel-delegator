<?php

use Inmanturbo\Delegator\Tests\TestClasses\Team;

beforeEach(function () {
    Team::factory()->count(3)->create();

    $this->tenants = Team::get();
});

it('can make each tenant current', function () {
    $this->tenants->eachCurrent(function ($tenant) {
        expect($tenant->id)->toEqual(Team::current()->id);
    });
});

test('after making each tenant current, the original current tenant is made current again', function () {
    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants->eachCurrent(function (Team $tenant) {
    });

    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants[1]->makeCurrent();

    $this->tenants->eachCurrent(function (Team $tenant) {
    });

    expect($this->tenants[1]->isCurrent())->toBeTrue();
});

it('can map while making each tenant current', function () {
    $tenantIds = $this->tenants
        ->mapCurrent(function (Team $tenant) {
            expect($tenant->id)->toEqual(Team::current()->id);

            return $tenant->id;
        })
        ->toArray();

    expect([1, 2, 3])->toMatchArray($tenantIds);
});

test('after mapping each current tenant the original current tenant is made current again', function () {
    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants->mapCurrent(function (Team $tenant) {
    });

    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants[1]->makeCurrent();

    $this->tenants->mapCurrent(function (Team $tenant) {
    });

    expect($this->tenants[1]->isCurrent())->toBeTrue();
});

it('can filter while making each tenant current', function () {
    $tenantIds = $this->tenants
        ->filterCurrent(function (Team $tenant) {
            expect($tenant->id)->toEqual(Team::current()->id);

            return $tenant->id != 2;
        })
        ->pluck('id')
        ->toArray();

    expect([1, 3])->toMatchArray($tenantIds);
});

test('after filtering each current tenant the original current tenant is made current again', function () {
    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants->filterCurrent(function (Team $tenant) {
    });

    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants[1]->makeCurrent();

    $this->tenants->filterCurrent(function (Team $tenant) {
    });

    expect($this->tenants[1]->isCurrent())->toBeTrue();
});

it('can reject while making each tenant current', function () {
    $tenantIds = $this->tenants
        ->rejectCurrent(function (Team $tenant) {
            expect($tenant->id)->toEqual(Team::current()->id);

            return $tenant->id == 2;
        })
        ->pluck('id')
        ->toArray();

    expect([1, 3])->toMatchArray($tenantIds);
});

test('after rejecting each current tenant the original current tenant is made current again', function () {
    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants->rejectCurrent(function (Team $tenant) {
    });

    expect(Team::checkCurrent())->toBeFalse();

    $this->tenants[1]->makeCurrent();

    $this->tenants->rejectCurrent(function (Team $tenant) {
    });

    expect($this->tenants[1]->isCurrent())->toBeTrue();
});
