<?php

namespace Inmanturbo\Delegator\Contracts;

use Inmanturbo\Delegator\Models\Contracts\CandidateModel;
use Illuminate\Http\Request;

interface CandidateFinder
{
    public function findForRequest(Request $request): ?CandidateModel;
}