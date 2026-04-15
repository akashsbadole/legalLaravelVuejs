<?php

namespace Database\Factories;

use App\Models\CaseStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseStatusFactory extends Factory
{
    protected $model = CaseStatus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Open', 'Closed', 'Pending', 'In Progress']),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
        ];
    }
}