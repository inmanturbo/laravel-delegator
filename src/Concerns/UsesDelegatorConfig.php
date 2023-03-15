<?php

namespace Inmanturbo\Delegator\Concerns;

use Inmanturbo\Delegator\Exceptions\InvalidConfiguration;
use Illuminate\Support\Arr;

trait UsesDelegatorConfig
{
    public function delegatorDatabaseConnectionName(): ?string
    {
        return config('delegator.delegator_database_connection_name') ?? config('database.default');
    }

    public function getCandidateDatabaseConnectionName(string $candidateConfigKey): ?string
    {
        return 'sqlite3';
        return config("delegator.candidates.{$candidateConfigKey}.candidate_database_connection_name") ?? config('database.default');
    }

    public static function getCurrentCandidateContainerKey($candidateConfigKey): string
    {
        return config("delegator.candidates.{$candidateConfigKey}.current_candidate_container_key");
    }

    public function getDelegatorActionClass(string $candidateConfigKey, string $actionName, string $actionClass, ... $params)
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

    protected function determineWhichCandidateIsBeingUsedAsTenant()
    {
        if($tenant = config('delegator.tenant')) {
            if(!array_key_exists(config('delegator.candidates'), $tenant)) {
                throw InvalidConfiguration::tenantSetToUnconfiguredCandidate($tenant);
            }
            return $tenant;
        }

        return Arr::first(
            array_keys(config('delegator.candidates')
        ));
    }

}