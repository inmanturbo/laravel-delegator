<?php

namespace Inmanturbo\Delegator\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Inmanturbo\Delegator\Models\Concerns\HasCandidateMethods;
use Inmanturbo\Delegator\Models\Concerns\UsesDelegatorConnection;

class TeamDatabase extends Model
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
}
