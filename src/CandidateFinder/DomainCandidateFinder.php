<?php

namespace Inmanturbo\Delegator\CandidateFinder;

use Illuminate\Http\Request;
use Inmanturbo\Delegator\CandidateFinder\Contracts\CandidateFinder;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class DomainCandidateFinder implements CandidateFinder
{
    public function __construct(protected mixed $candidateModel)
    {
        $this->candidateModel = $candidateModel;
    }

    public function findForRequest(Request $request): ?CandidateModel
    {
        $domain = $request->getHost();

        return $this->findForDomain($domain);
    }

    public function findForDomain(string $domain): ?CandidateModel
    {
        return $this->candidateModel::where('domain', $domain)->first();
    }
}