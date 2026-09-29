<?php

namespace Database\Factories;

use App\Models\ModuleVisit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModuleVisit>
 */
class ModuleVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'module_ref' => 'filament.admin.pages.'.fake()->unique()->slug(2),
            'visit_count' => fake()->numberBetween(1, 20),
            'last_visited_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }
}
