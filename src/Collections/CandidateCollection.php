<?php

namespace Inmanturbo\Delegator\Collections;

use Illuminate\Database\Eloquent\Collection;

class CandidateCollection extends Collection
{
    public function eachCurrent(callable $callable): self
    {
        return $this->performCollectionMethodWhileMakingCandidatesCurrent(
            operation: 'each',
            callable: $callable
        );
    }

    public function filterCurrent(callable $callable): self
    {
        return $this->performCollectionMethodWhileMakingCandidatesCurrent(
            operation: 'filter',
            callable: $callable
        );
    }

    public function mapCurrent(callable $callable): self
    {
        return $this->performCollectionMethodWhileMakingCandidatesCurrent(
            operation: 'map',
            callable: $callable
        );
    }

    public function rejectCurrent(callable $callable): self
    {
        return $this->performCollectionMethodWhileMakingCandidatesCurrent(
            operation: 'reject',
            callable: $callable
        );
    }

    protected function performCollectionMethodWhileMakingCandidatesCurrent(string $operation, callable $callable): self
    {
        $collection = $this->$operation(fn ($candidate) => $candidate->execute($callable));

        return new static($collection->items);
    }
}
