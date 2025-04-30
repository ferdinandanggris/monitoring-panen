<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Machine::class;

    public function definition(): array
    {
        return [
            'name' => 'Mesin ' . $this->faker->unique()->word(),
            'notes' => $this->faker->optional()->sentence(),
            'current_driver_id' => $this->faker->randomElement([1, 2, 3]), // Assuming driver IDs are 1, 2, and 3
        ];
    }
}
