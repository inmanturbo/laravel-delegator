<?php

namespace Inmanturbo\Delegator\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Inmanturbo\Delegator\Models\Concerns\HasCandidateMethods;

class TeamDatabase extends Model
{
    use HasFactory, HasCandidateMethods;

    protected $gaurded = [];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
