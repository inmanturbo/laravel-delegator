<?php

namespace Inmanturbo\Delegator\Events;

use Inmanturbo\Delegator\Models\Contracts\Tenant;

class ForgettingCurrentTenantEvent
{
    public function __construct(
        public Tenant $tenant,
    ){
    }
}
