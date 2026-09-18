<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Penalty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penalty>
 */
class PenaltyFactory extends Factory
{
    protected $model = Penalty::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $daysLate = fake()->numberBetween(1, 90);
        $rate = fake()->randomElement([2, 5, 10]);

        return [
            'payment_schedule_id' => PaymentSchedule::factory(),
            'payment_id' => null,
            'amount' => round(fake()->numberBetween(5000, 100000) / 1000) * 1000,
            'rate' => $rate,
            'days_late' => $daysLate,
            'calculated_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'paid_at' => null,
        ];
    }

    /**
     * Pénalité payée.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_at' => fake()->dateTimeBetween(
                $attributes['calculated_at'] ?? '-3 months',
                'now'
            ),
        ]);
    }

    /**
     * Pénalité impayée.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_at' => null,
        ]);
    }

    /**
     * Pénalité faible (< 30 jours de retard).
     */
    public function minor(): static
    {
        return $this->state(fn (array $attributes) => [
            'days_late' => fake()->numberBetween(1, 15),
            'rate' => 2,
            'amount' => round(fake()->numberBetween(2000, 15000) / 1000) * 1000,
        ]);
    }

    /**
     * Pénalité importante (> 60 jours de retard).
     */
    public function major(): static
    {
        return $this->state(fn (array $attributes) => [
            'days_late' => fake()->numberBetween(60, 180),
            'rate' => 10,
            'amount' => round(fake()->numberBetween(50000, 200000) / 1000) * 1000,
        ]);
    }
}
