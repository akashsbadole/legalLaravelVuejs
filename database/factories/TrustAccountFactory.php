<?php

namespace Database\Factories;

use App\Models\LegalCase;
use App\Models\TrustAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrustAccountFactory extends Factory
{
    protected $model = TrustAccount::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'case_id' => LegalCase::factory(),
            'created_by' => User::factory(),
            'transaction_type' => $this->faker->randomElement(['deposit', 'withdrawal', 'transfer']),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'balance' => $this->faker->randomFloat(2, 0, 50000),
            'description' => $this->faker->optional()->sentence(),
            'reference_number' => 'TR-' . rand(10000, 99999),
            'transaction_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => 'active',
        ];
    }

    public function deposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'deposit',
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'withdrawal',
        ]);
    }
}