<?php

namespace Database\Factories;

use App\Models\LegalCase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegalCaseFactory extends Factory
{
    protected $model = LegalCase::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'case_number' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->optional()->paragraph(),
            'case_type' => $this->faker->randomElement(['civil', 'criminal', 'family', 'corporate']),
            'status_id' => 1,
            'assigned_attorney_id' => User::factory(),
            'client_id' => User::factory(),
            'open_date' => now()->toDateString(),
            'estimated_value' => $this->faker->randomFloat(2, 1000, 100000),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}