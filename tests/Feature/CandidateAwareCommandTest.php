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

it('fails with a non-existing candidate')
    ->artisan('candidate:noop team_database --candidates=1000')
    ->assertExitCode(-1)
    ->expectsOutput('No candidates(s) found.');

    it('works with no candidate parameters', function () {
        $this
            ->artisan('candidate:noop')
            ->assertExitCode(0)
            ->expectsOutput('Candidate ID is ' . $this->tenant->id)
            ->expectsOutput('Candidate ID is ' . $this->anotherTenant->id);
    });