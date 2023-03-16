<?php

namespace Inmanturbo\Delegator\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inmanturbo\Delegator\Tests\TestClasses\TeamDatabase;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TeamDatabaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string
     */
    protected $model = TeamDatabase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => str($this->faker->name)->slug('_'),
            'uuid' => $this->faker->uuid,
            'driver' => 'sqlite',
            'user_id' => null,
        ];
    }
}
