<?php

namespace Inmanturbo\Delegator\Events;

class ForgettingCurrentTenantEvent
{
    public function __construct(
        public $tenant,
    ){
    }
}
