<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;
use Inmanturbo\Delegator\Tasks\SwitchCandidateDatabaseTask;
use Inmanturbo\Delegator\Tests\TestCase;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;
use Spatie\Docker\DockerContainer;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class)->in(__DIR__);

function tempFile(string $fileName): string
{
    return __DIR__ . "/temp/{$fileName}";
}

function setupDatabaseInfrastructure($test){
    if (! `which mysql`) {
        $test->fail('MySQL client is not installed');
    }

    if (! `which docker`) {
        $test->fail('Docker is not installed');
    }

    $test->containerInstance = DockerContainer::create('mariadb:latest')
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

    $test->tenant = TeamDatabase::factory()->create(['name' => 'laravel_mt_tenant_1']);

    $test->anotherTenant = TeamDatabase::factory()->create(['name' => 'laravel_mt_tenant_2']);

    config()->set('delegator.candidates.team_database.switch_candidate_tasks', [SwitchCandidateDatabaseTask::class]);

    config()->set('database.default', 'team_mysql');
}

function tearDownDatabaseInfrastructure($test){
    $test->containerInstance->stop();
}

function candidateHasDatabaseTable(CandidateModel $tenant, string $tableName): bool
{
    $tenant->makeCurrent();

    $tenantHasDatabaseTable = Schema::connection('team_mysql')->hasTable($tableName);

    TeamDatabase::forgetCurrent();

    return $tenantHasDatabaseTable;
}

function assertCandidateDatabaseHasTable(CandidateModel $tenant, string $tableName): void
{
    $tenantHasDatabaseTable = candidateHasDatabaseTable($tenant, $tableName);

    assertTrue(
        $tenantHasDatabaseTable,
        "Tenant database does not have table `{$tableName}`"
    );
}

function assertCandidateDatabaseDoesNotHaveTable(CandidateModel $tenant, string $tableName): void
{
    $tenantHasDatabaseTable = candidateHasDatabaseTable($tenant, $tableName);

    assertFalse(
        $tenantHasDatabaseTable,
        "Tenant database has unexpected table  `{$tableName}`"
    );
}
