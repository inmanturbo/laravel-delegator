<?php

namespace Inmanturbo\Delegator\Events;

class ForgotCurrentTenantEvent
{
    public function __construct(
        public $tenant,
    ){
    }
}
