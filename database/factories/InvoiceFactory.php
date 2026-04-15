<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'invoice_number' => 'INV-' . date('Ym') . '-' . rand(1000, 9999),
            'client_id' => User::factory(),
            'created_by' => User::factory(),
            'subtotal' => $this->faker->randomFloat(2, 100, 10000),
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'overdue']),
            'issue_date' => now()->subDays(rand(1, 30)),
            'due_date' => now()->addDays(rand(15, 60)),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_date' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}