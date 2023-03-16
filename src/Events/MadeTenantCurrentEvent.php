<?php

namespace Inmanturbo\Delegator\Events;

class MadeTenantCurrentEvent
{
    public function __construct(
        public $tenant,
    ){
    }
}
