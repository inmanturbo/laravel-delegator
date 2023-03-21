<?php

namespace Inmanturbo\Delegator\Events;

use Inmanturbo\Delegator\Models\Contracts\Tenant;

class ForgotCurrentTenantEvent
{
    public function __construct(
        public Tenant $tenant,
    ){
    }
}
