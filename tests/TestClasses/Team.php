<?php

namespace Inmanturbo\Delegator\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Inmanturbo\Delegator\Models\Concerns\HasCandidateMethods;
use Inmanturbo\Delegator\Models\Concerns\UsesDelegatorConnection;
use Inmanturbo\Delegator\Models\Contracts\CandidateModel;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam implements CandidateModel
{
    use HasFactory, HasCandidateMethods, UsesDelegatorConnection;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    protected $gaurded = [];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    public function getDatabaseName(): string
    {
        return $this->teamDatabase->name;
    }

    public function teamDatabase()
    {
        return $this->belongsTo(TeamDatabase::class);
    }

    public static function find(int $id): ?self
    {
        return parent::find($id);
    }

    protected static function newFactory()
    {
        return \Inmanturbo\Delegator\Tests\Database\Factories\TeamFactory::new();
    }
}
