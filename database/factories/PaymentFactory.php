<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentDate = fake()->dateTimeBetween('-1 year', 'now');
        $amount = round(fake()->numberBetween(50000, 500000) / 5000) * 5000;

        return [
            'contract_id' => Contract::factory(),
            'payment_schedule_id' => null,
            'amount' => $amount,
            'penalty_amount' => 0,
            'charges_amount' => 0,
            'depense_travaux' => null,
            'commission_percent' => null,
            'payment_date' => $paymentDate,
            'period' => date('Y-m', $paymentDate->getTimestamp()),
            'payment_type' => 'loyer',
            'payment_method' => fake()->randomElement(['cash', 'moneyfusion', 'wave', 'orange_money', 'mtn_money', 'bank_transfer', 'check']),
            'reference' => fake()->optional(0.6)->bothify('PAY-####-??##'),
            'status' => 'completed',
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Paiement complété.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Paiement en attente.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Paiement échoué.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Paiement remboursé.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
        ]);
    }

    /**
     * Paiement en espèces.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
            'reference' => null,
        ]);
    }

    /**
     * Paiement par MoneyFusion.
     */
    public function moneyfusion(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'moneyfusion',
            'reference' => 'MF-' . fake()->numerify('########'),
        ]);
    }

    /**
     * Paiement par virement bancaire.
     */
    public function virement(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bank_transfer',
            'reference' => 'VIR-' . fake()->numerify('########'),
        ]);
    }

    /**
     * Paiement par chèque.
     */
    public function cheque(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'check',
            'reference' => 'CHQ-' . fake()->numerify('######'),
        ]);
    }

    /**
     * Paiement Wave.
     */
    public function wave(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'wave',
            'reference' => 'WAVE-' . fake()->numerify('########'),
        ]);
    }

    /**
     * Paiement Orange Money.
     */
    public function orangeMoney(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'orange_money',
            'reference' => 'OM-' . fake()->numerify('########'),
        ]);
    }

    /**
     * Paiement avec pénalité.
     */
    public function withPenalty(): static
    {
        return $this->state(fn (array $attributes) => [
            'penalty_amount' => round(fake()->numberBetween(5000, 50000) / 1000) * 1000,
        ]);
    }

    /**
     * Paiement avec charges.
     */
    public function withCharges(): static
    {
        return $this->state(fn (array $attributes) => [
            'charges_amount' => round(fake()->numberBetween(5000, 30000) / 1000) * 1000,
            'payment_type' => 'charges_locatives',
        ]);
    }

    /**
     * Paiement type commission.
     */
    public function commission(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'commission',
            'commission_percent' => fake()->randomElement([5, 8, 10, 12, 15]),
        ]);
    }
}
