<?php

namespace Inmanturbo\Delegator\Events;

class MadeCandidateCurrentEvent
{
    public function __construct(
        public $candidate,
    ){
    }
}
