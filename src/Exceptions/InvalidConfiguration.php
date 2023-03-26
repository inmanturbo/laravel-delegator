<?php

namespace Inmanturbo\Delegator\Exceptions;

use Exception;

final class InvalidConfiguration extends Exception
{
    public static function candidateConnectionDoesNotExist(string $expectedConnectionName): self
    {
        return new self("Could not find a tenant connection named `{$expectedConnectionName}`. Make sure to create a connection with that name in the `connections` key of the `database` config file.");
    }

    public static function candidateConnectionIsEmptyOrEqualsToDelegatorConnection($candidateConfigKey): self
    {
        return new self("`SwitchCandidateDatabaseTask` fails because the `delegator.candidates.{$candidateConfigKey}.candidate_database_connection_name` key in the `delegator` config file is empty or equals to the `delegator.delegator_database_connection_name` key in the `delegator` config file.");
    }

    public static function invalidAction(string $actionName, string $configuredClass, string $actionClass, string $candidateConfigKey): self
    {
        return new self("The class currently specified in the `delegator.candidates.{$candidateConfigKey}.actions.{$actionName}` key in the `delegator` config file (`{$configuredClass}`) should be or extend `{$actionClass}`.");
    }

    public static function tenantSetToUnconfiguredCandidate(string $candidateConfigKey): self
    {
        return new self("Tenant {$candidateConfigKey} is not a valid candidate or is not configured. Make sure to add it to the `candidates` array in the `delegator` config file.");
    }
}
