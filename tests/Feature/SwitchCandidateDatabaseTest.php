<?php

use Illuminate\Support\Facades\DB;
use Inmanturbo\Delegator\Exceptions\InvalidConfiguration;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;
use Inmanturbo\Delegator\Tests\TestClasses\User;

beforeEach(function(){
    setupDatabaseInfrastructure($this);
});

afterEach(function(){
    tearDownDatabaseInfrastructure($this);
});

test('switch fails if candidate database connection name equals to delegator connection name', function () {
    config()->set('delegator.candidates.team_database.candidate_database_connection_name', 'delegator');

    $this->tenant->makeCurrent();
})->throws(InvalidConfiguration::class);

test('when making a candidate current it will perform the tasks', function () {
    expect(DB::connection('team_mysql'))->getDatabaseName()->toBeNull();

    $this->tenant->makeCurrent();

    expect('laravel_mt_tenant_1')
        ->toEqual(DB::connection('team_mysql')->getDatabaseName())
        ->toEqual(app(User::class)->getConnection()->getDatabaseName());

    $this->anotherTenant->makeCurrent();

    expect('laravel_mt_tenant_2')
        ->toEqual(DB::connection('team_mysql')->getDatabaseName())
        ->toEqual(app(User::class)->getConnection()->getDatabaseName());

    TeamDatabase::forgetCurrent();
    expect(DB::connection('team_mysql'))->getDatabaseName()->toBeNull();
});