<?php

namespace Inmanturbo\Delegator\Exceptions;

use Exception;
use Illuminate\Queue\Events\JobProcessing;

final class CurrentTenantCouldNotBeDeterminedInTenantAwareJob extends Exception
{
    public static function noIdSet(JobProcessing $event)
    {
        return new self('The current tenant could not be determined in a job named `'.$event->job->getName().'`. No `tenantId` was set in the payload.');
    }

    public static function noTenantFound(JobProcessing $event): self
    {
        return new self('The current tenant could not be determined in a job named `'.$event->job->getName().'`. The tenant finder could not find a tenant.');
    }
}
