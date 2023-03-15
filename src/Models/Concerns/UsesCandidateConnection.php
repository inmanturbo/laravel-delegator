<?php

namespace Inmanturbo\Delegator\Models\Concerns;

use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;

trait UsesCandidateConnection
{
    use UsesDelegatorConfig;
    use GetsCandidateConfigKey;

    public function getConnectionName()
    {
        return $this->getCandidateDatabaseConnectionName($this->getCandidateConfigKey());
    }
}