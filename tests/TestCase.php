<?php

namespace Inmanturbo\Delegator\Tests;

use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Inmanturbo\Delegator\DelegatorServiceProvider;
use Inmanturbo\Delegator\Tests\TestClasses\CandidateNoopCommand;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Inmanturbo\\Delegator\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->migrateDb();
    }

    protected function getPackageProviders($app)
    {
        $this->bootCommands();

        return [
            DelegatorServiceProvider::class,
        ];
    }

    protected function bootCommands(): self
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands([
                CandidateNoopCommand::class,
            ]);
        });

        return $this;
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'team_sqlite');

        config()->set('database.connections.team_sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => 'team_',
        ]);

        config()->set('database.connections.delegator', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => 'delegator_',
        ]);

        config()->set('queue.default', 'database');

        config()->set('queue.connections.database', [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'connection' => 'delegator',
        ]);

        config()->set('delegator.delegator_database_connection_name', 'delegator');

        config()->set('delegator.candidates', [

            'team' => [
                'candidate_finder' => null,
                'candidate_artisan_search_fields' => [
                    'id',
                ],
                'switch_candidate_tasks' => [
                    // \Inmanturbo\Delegator\Tasks\SwitchCandidateDatabaseTask::class,
                ],
                'model' => \Inmanturbo\Delegator\Tests\TestClasses\Team::class,
                'queues_are_tenant_aware_by_default' => false,
                'candidate_database_connection_name' => null,
                'current_candidate_container_key' => 'currentTeam',
                'actions' => [
                    'make_current_action' => \Inmanturbo\Delegator\Actions\MakeCandidateCurrentAction::class,
                    'forget_current_action' => \Inmanturbo\Delegator\Actions\ForgetCandidateCurrentAction::class,
                    // 'migrate_action' => \Inmanturbo\Delegator\Actions\MigrateCandidateAction::class,
                    'make_queue_tenant_aware_action' => \Inmanturbo\Delegator\Actions\MakeQueueTenantAwareAction::class,
                ],
                'queueable_to_job' => [
                    \Illuminate\Mail\SendQueuedMailable::class => 'mailable',
                    \Illuminate\Notifications\SendQueuedNotifications::class => 'notification',
                    \Illuminate\Events\CallQueuedListener::class => 'class',
                    \Illuminate\Broadcasting\BroadcastEvent::class => 'event',
                ],
            ],
            'team_database' => [
                'candidate_finder' => null,
                'candidate_artisan_search_fields' => [
                    'id',
                ],
                'switch_candidate_tasks' => [
                    // \Inmanturbo\Delegator\Tasks\SwitchCandidateTeamDatabaseTask::class,
                ],
                'model' => \Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase::class,
                'candidate_database_connection_name' => 'team_mysql',
                'current_candidate_container_key' => 'currentTeamDatabase',
                'actions' => [
                    'make_current_action' => \Inmanturbo\Delegator\Actions\MakeCandidateCurrentAction::class,
                    'forget_current_action' => \Inmanturbo\Delegator\Actions\ForgetCandidateCurrentAction::class,
                    'migrate_action' => \Inmanturbo\Delegator\Actions\MigrateCandidateAction::class,
                    // 'make_queue_tenant_aware_action' => \Inmanturbo\Delegator\Actions\MakeQueueTenantAwareAction::class,
                ],
                'queueable_to_job' => [
                    // \Illuminate\Mail\SendQueuedMailable::class => 'mailable',
                    // \Illuminate\Notifications\SendQueuedNotifications::class => 'notification',
                    // \Illuminate\Events\CallQueuedListener::class => 'class',
                    // \Illuminate\Broadcasting\BroadcastEvent::class => 'event',
                ],
            ],
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-delegator_table.php.stub';
        $migration->up();
        */
    }

    protected function migrateDb(): self
    {
        $delegatorMigrationsPath = realpath(__DIR__.'/database/migrations/delegator');

        $this
            ->artisan("migrate --database=delegator --path={$delegatorMigrationsPath} --realpath")
            ->assertExitCode(0);

        return $this;
    }
}
