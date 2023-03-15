<?php

namespace Inmanturbo\Delegator\Models\Contracts;

use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

interface Tenant extends CandidateModel
{
    public function getDatabaseName(): string;

    public static function find(int $id): ?self;
}