<?php

namespace Inmanturbo\Delegator\Models\Concerns;

use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;

trait UsesDelegatorConnection
{
    use UsesDelegatorConfig;

    public function getConnectionName()
    {
        return $this->delegatorDatabaseConnectionName();
    }
}