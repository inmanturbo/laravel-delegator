<?php

namespace Inmanturbo\Delegator\Events;

class MakingCandidateCurrentEvent
{
    public function __construct(
        public $candidate,
    ){
    }
}
