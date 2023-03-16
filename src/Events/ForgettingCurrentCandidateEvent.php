<?php

namespace Inmanturbo\Delegator\Events;

class ForgettingCurrentCandidateEvent
{
    public function __construct(
        public $candidate,
    ){
    }
}
