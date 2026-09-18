<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\PaymentSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentSchedule>
 */
class PaymentScheduleFactory extends Factory
{
    protected $model = PaymentSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contract_id' => Contract::factory(),
            'due_date' => fake()->dateTimeBetween('-6 months', '+6 months'),
            'amount' => round(fake()->numberBetween(50000, 500000) / 5000) * 5000,
            'status' => 'pending',
            'paid_at' => null,
        ];
    }

    /**
     * Echéance en attente.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
            'due_date' => fake()->dateTimeBetween('now', '+3 months'),
        ]);
    }

    /**
     * Echéance payée.
     */
    public function paid(): static
    {
        $dueDate = fake()->dateTimeBetween('-6 months', '-1 day');

        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'due_date' => $dueDate,
            'paid_at' => fake()->dateTimeBetween($dueDate, 'now'),
        ]);
    }

    /**
     * Echéance en retard.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => fake()->dateTimeBetween('-6 months', '-1 day'),
            'paid_at' => null,
        ]);
    }

    /**
     * Echéance annulée.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'paid_at' => null,
        ]);
    }
}
