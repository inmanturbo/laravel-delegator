<?php

namespace Inmanturbo\Delegator\Models\Concerns;

trait GetsCandidateConfigKey
{

    public static function getCandidateConfigKey(): string
    {
        $candidateModelClass = (new static)->getCandidateModelClass();

        return (new $candidateModelClass)->table ? str((new $candidateModelClass)->table)->singular() : str(class_basename($candidateModelClass))->snake();
    }

    public function getCandidateModelClass(): string
    {
        return $this->candidateModelClass ?? static::class;
    }
}