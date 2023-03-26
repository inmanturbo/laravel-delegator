<?php

namespace Inmanturbo\Delegator\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;
use Inmanturbo\Delegator\Tasks\Contracts\SwitchCandidateTask;
use Inmanturbo\Delegator\Exceptions\InvalidConfiguration;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class SwitchCandidateDatabaseTask implements SwitchCandidateTask
{

    use UsesDelegatorConfig;

    public function makeCurrent(CandidateModel $model): void
    {
        $this->setCandidateConnectionDatabaseName($model->getDatabaseName(), $model->getCandidateConfigKey());
    }

    public function forgetCurrent($candidateConfigKey): void
    {
        $this->setCandidateConnectionDatabaseName(null, $candidateConfigKey);
    }

    protected function setCandidateConnectionDatabaseName(?string $databaseName, string $candidateConfigKey)
    {
        $candidateConnectionName = $this->candidateDatabaseConnectionName($candidateConfigKey);

        if ($candidateConnectionName === $this->delegatorDatabaseConnectionName()) {
            throw InvalidConfiguration::candidateConnectionIsEmptyOrEqualsToDelegatorConnection($candidateConfigKey);
        }

        if (is_null(config("database.connections.{$candidateConnectionName}"))) {
            throw InvalidConfiguration::candidateConnectionDoesNotExist($candidateConnectionName);
        }

        config([
            "database.connections.{$candidateConnectionName}.database" => $databaseName,
        ]);

        app('db')->extend($candidateConnectionName, function ($config, $name) use ($databaseName) {
            $config['database'] = $databaseName;

            return app('db.factory')->make($config, $name);
        });

        DB::purge($candidateConnectionName);

        // Octane will have an old `db` instance in the Model::$resolver.
        Model::setConnectionResolver(app('db'));
    }
}