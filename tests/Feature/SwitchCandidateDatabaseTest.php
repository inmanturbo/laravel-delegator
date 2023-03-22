<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Inmanturbo\Delegator\Exceptions\InvalidConfiguration;
use Inmanturbo\Delegator\Tasks\SwitchCandidateDatabaseTask;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;
use Inmanturbo\Delegator\Tests\TestClasses\User;
use Spatie\Docker\DockerContainer;

beforeEach(function () {
    if (! `which mysql`) {
        $this->fail('MySQL client is not installed');
    }

    if (! `which docker`) {
        $this->fail('Docker is not installed');
    }

    $this->containerInstance = DockerContainer::create('mariadb:latest')
        ->setEnvironmentVariable('MARIADB_ROOT_PASSWORD', 'root')
        ->setEnvironmentVariable('MARIADB_DATABASE', 'candidates_test')
        ->name('candidates_test')
        ->mapPort(10001, 3306)
        ->start();

    $i = 0;

    while ($i < 50) {
        $process = Process::run('mysql -u root -proot -P 10001 -h 127.0.0.1  candidates_test -e "show tables;"');
        if ($process->successful()) {
            break;
        }
        sleep(.5);
    }

    config(['database.connections.team_mysql' => array_merge(config('database.connections.mysql'), [
        'database' => null,
        'host' => '127.0.0.1',
        'port' => '10001',
        'username' => 'root',
        'password' => 'root',
    ])]);

    DB::connection('team_mysql')->statement('CREATE DATABASE IF NOT EXISTS laravel_mt_tenant_1');

    DB::connection('team_mysql')->statement('CREATE DATABASE IF NOT EXISTS laravel_mt_tenant_2');

    $this->tenant = TeamDatabase::factory()->create(['name' => 'laravel_mt_tenant_1']);

    $this->anotherTenant = TeamDatabase::factory()->create(['name' => 'laravel_mt_tenant_2']);

    config()->set('delegator.candidates.team_database.switch_candidate_tasks', [SwitchCandidateDatabaseTask::class]);

    config()->set('database.default', 'team_mysql');
});

afterEach(function () {
    $this->containerInstance->stop();
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