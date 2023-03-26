<?php

namespace Inmanturbo\Delegator\CandidateFinder\Contracts;

use Illuminate\Http\Request;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

interface CandidateFinder
{
    public function findForRequest(Request $request): ?CandidateModel;
}
