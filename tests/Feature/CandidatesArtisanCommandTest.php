<?php

use Illuminate\Support\Facades\Schema;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;

beforeEach(function () {
    setupDatabaseInfrastructure($this);
    
    $this->tenant->makeCurrent();
    Schema::connection('team_mysql')->dropIfExists('migrations');

    $this->anotherTenant->makeCurrent();
    Schema::connection('team_mysql')->dropIfExists('migrations');

    TeamDatabase::forgetCurrent();
});

afterEach(function () {
    tearDownDatabaseInfrastructure($this);
});

it('can migrate all candidate databases', function () {
    $this
        ->artisan('candidates:artisan migrate team_database')
        ->assertExitCode(0);

    assertCandidateDatabaseHasTable($this->tenant, 'migrations');
    assertCandidateDatabaseHasTable($this->anotherTenant, 'migrations');
});

it('can migrate a specific candidate', function () {
    $this->artisan('candidates:artisan migrate team_database --candidates="' . $this->anotherTenant->id . '"')->assertExitCode(0);

    assertCandidateDatabaseDoesNotHaveTable($this->tenant, 'migrations');
    assertCandidateDatabaseHasTable($this->anotherTenant, 'migrations');
});