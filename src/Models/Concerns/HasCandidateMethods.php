<?php

namespace Inmanturbo\Delegator\Models\Concerns;

use Inmanturbo\Delegator\Actions\ForgetCurrentCandidateAction;
use Inmanturbo\Delegator\Actions\MakeCandidateCurrentAction;
use Inmanturbo\Delegator\Collections\CandidateCollection;
use Inmanturbo\Delegator\Concerns\UsesDelegatorConfig;

trait HasCandidateMethods
{
    use GetsCandidateConfigKey, UsesDelegatorConfig;

    public function makeCurrent(): static
    {
        if ($this->isCurrent()) {
            return $this;
        }

        static::forgetCurrent();

        $tasks = $this->getRawTasks();

        $this
            ->getDelegatorActionClass(
                candidateConfigKey: static::getCandidateConfigKey(),
                actionName: 'make_current_action',
                actionClass: MakeCandidateCurrentAction::class,
                tasks:  $tasks,
            )
            ->execute($this);

        return $this;
    }

    protected static function getRawTasks(): array
    {
        $candidateConfigKey = static::getCandidateConfigKey();

        return config("delegator.candidates.{$candidateConfigKey}.switch_candidate_tasks");
    }

    public static function current(): ?static
    {
        $currentCandidateContainerKey = static::getCurrentCandidateContainerKey(
            static::getCandidateConfigKey()
        );

        if (! app()->has($currentCandidateContainerKey)) {
            return null;
        }

        return app($currentCandidateContainerKey);
    }

    public function isCurrent(): bool
    {
        return static::current()?->getKey() === $this->getKey();
    }

    public static function forgetCurrent(): ?static
    {
        $current= static::current();

        if (is_null($current)) {
            return null;
        }

        $current->forget();

        return $current;
    }

    public function newCollection(array $models = []): CandidateCollection
    {
        return new CandidateCollection($models);
    }

    public function forget(): static
    {
        $this
            ->getDelegatorActionClass(
                candidateConfigKey: static::getCandidateConfigKey(),
                actionName: 'forget_current_candidate_action',
                actionClass: ForgetCurrentCandidateAction::class,
                tasks: static::getRawTasks(),
            )
            ->execute($this);

        return $this;
    }

    public function execute(callable $callable)
    {
        $originalCurrentTenant = static::current();

        $this->makeCurrent();

        return tap($callable($this), static function () use ($originalCurrentTenant) {
            $originalCurrentTenant
                ? $originalCurrentTenant->makeCurrent()
                : static::forgetCurrent();
        });
    }
}