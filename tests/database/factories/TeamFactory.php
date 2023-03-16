<?php

namespace Inmanturbo\Delegator\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inmanturbo\Delegator\Tests\TestClasses\Team;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'personal_team' => false,
            'team_database_id' => TeamDatabase::factory()->create()->id,
        ];
    }
}
