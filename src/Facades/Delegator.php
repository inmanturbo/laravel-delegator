<?php

namespace Inmanturbo\Delegator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Inmanturbo\Delegator\Delegator
 */
class Delegator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Inmanturbo\Delegator\Delegator::class;
    }
}
