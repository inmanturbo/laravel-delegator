<?php

namespace Inmanturbo\Delegator\Events;

use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class MakingCandidateCurrentEvent
{
    public function __construct(
        public CandidateModel $candidate,
    ){
    }
}
