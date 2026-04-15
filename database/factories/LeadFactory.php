<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->company(),
            'description' => $this->faker->optional()->paragraph(),
            'assigned_to' => User::factory(),
            'source' => $this->faker->randomElement(['website', 'referral', 'advertisement', 'cold_call']),
            'status' => $this->faker->randomElement(['new', 'contacted', 'qualified', 'converted', 'lost']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'estimated_value' => $this->faker->randomFloat(2, 1000, 100000),
            'next_follow_up' => $this->faker->dateTimeBetween('now', '+30 days'),
        ];
    }
}