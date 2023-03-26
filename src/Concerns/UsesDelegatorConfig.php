<?php

namespace Inmanturbo\Delegator\Concerns;

use Illuminate\Support\Arr;
use Inmanturbo\Delegator\Exceptions\InvalidConfiguration;

trait UsesDelegatorConfig
{
    public function delegatorDatabaseConnectionName(): ?string
    {
        return config('delegator.delegator_database_connection_name') ?? config('database.default');
    }

    public function candidateDatabaseConnectionName(string $candidateConfigKey): ?string
    {
        return config("delegator.candidates.{$candidateConfigKey}.candidate_database_connection_name") ?? config('database.default');
    }

    public static function currentCandidateContainerKey($candidateConfigKey): string
    {
        return config("delegator.candidates.{$candidateConfigKey}.current_candidate_container_key");
    }

    public function getDelegatorActionClass(string $candidateConfigKey, string $actionName, string $actionClass, ...$params)
    {
        $configuredClass = config("delegator.candidates.{$candidateConfigKey}.actions.{$actionName}") ?? $actionClass;

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfiguration::invalidAction(
                actionName: $actionName,
                configuredClass: $configuredClass ?? '',
                actionClass: $actionClass,
                candidateConfigKey: $candidateConfigKey
            );
        }

        if (count($params) > 0) {
            return app()->makeWith($configuredClass, $params);
        }

        return app($configuredClass);
    }

    public function getCandidateArtisanSearchFields($candidateConfigKey): array
    {
        return Arr::wrap(config("delegator.candidates.{$candidateConfigKey}.candidate_artisan_search_fields"));
    }
}
