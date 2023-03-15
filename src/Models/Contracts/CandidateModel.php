<?php

namespace Inmanturbo\Delegator\Models\Contracts;

use Inmanturbo\Delegator\Collections\CandidateCollection;

interface CandidateModel
{
    public function getDelegatorActionClass(string $candidateConfigKey, string $actionName, string $actionClass, ... $params);

    public static function getCandidateConfigKey(): string;

    public function makeCurrent(): static;

    public static function current(): ?static;

    public function isCurrent(): bool;

    public static function forgetCurrent(): ?static;

    public function newCollection(array $models = []): CandidateCollection;

    public function forget(): static;

    public function execute(callable $callable);
}