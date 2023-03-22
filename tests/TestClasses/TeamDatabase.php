<?php

namespace Inmanturbo\Delegator\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Inmanturbo\Delegator\Models\Concerns\HasCandidateMethods;
use Inmanturbo\Delegator\Models\Concerns\UsesDelegatorConnection;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;

class TeamDatabase extends Model implements CandidateModel
{
    use HasFactory, HasCandidateMethods, UsesDelegatorConnection;

    protected $gaurded = [];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    protected static function newFactory()
    {
        return \Inmanturbo\Delegator\Tests\Database\Factories\TeamDatabaseFactory::new();
    }

    public function getDatabaseName(): ?string
    {
        return $this->name;
    }
}
