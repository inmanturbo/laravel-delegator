<?php

namespace Inmanturbo\Delegator\Events;

class ForgotCurrentCandidateEvent
{
    public function __construct(
        public $candidate,
    ){
    }
}
