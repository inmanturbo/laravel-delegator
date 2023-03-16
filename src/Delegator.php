<?php

namespace Inmanturbo\Delegator;

use Inmanturbo\Delegator\Models\Contracts\Tenant;

class Delegator
{

    public static function execute(callable $callable)
    {
        $tenantService = app(Tenant::class);

        $originalCurrentTenant = $tenantService->current();

        $tenantService->forgetCurrent();

        $result = $callable();

        $originalCurrentTenant?->makeCurrent();

        return $result;
    } 
}