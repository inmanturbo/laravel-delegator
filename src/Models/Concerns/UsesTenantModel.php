<?php

namespace Inmanturbo\Delegator\Models\Concerns;

use Inmanturbo\Delegator\Models\Contracts\Tenant;

trait UsesTenantModel
{
    public function getTenantModel(): Tenant
    {
        return app(Tenant::class);
    }
}