<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'slug' => $this->faker->slug(),
            'logo' => null,
            'primary_color' => $this->faker->hexColor(),
            'contact_email' => $this->faker->email(),
            'contact_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'GSTIN' => $this->faker->regexify('[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}'),
            'PAN' => $this->faker->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}'),
            'subscription_plan' => $this->faker->randomElement(['basic', 'professional', 'enterprise']),
            'subscription_status' => 'active',
            'subscription_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'max_users' => $this->faker->numberBetween(1, 100),
            'max_cases' => $this->faker->numberBetween(10, 1000),
            'max_storage_mb' => $this->faker->numberBetween(100, 10000),
            'is_active' => true,
        ];
    }
}