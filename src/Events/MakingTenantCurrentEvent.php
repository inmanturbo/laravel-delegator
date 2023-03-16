<?php

namespace Inmanturbo\Delegator\Events;

class MakingTenantCurrentEvent
{
    public function __construct(
        public $tenant,
    ){
    }
}
