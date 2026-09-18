<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-6 months', 'now');
        $dueDate = (clone $issueDate)->modify('+30 days');
        $amount = round(fake()->numberBetween(50000, 500000) / 1000) * 1000;

        return [
            'contract_id' => Contract::factory(),
            'payment_id' => null,
            'invoice_number' => 'FAC-' . fake()->unique()->numerify('######'),
            'invoice_type' => fake()->randomElement(['commission', 'rent', 'other']),
            'amount' => $amount,
            'tax_amount' => fake()->randomElement([0, round($amount * 0.18)]),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'description' => fake()->optional(0.5)->sentence(),
            'status' => 'draft',
            'pdf_path' => null,
        ];
    }

    /**
     * Facture brouillon.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Facture envoyée.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    /**
     * Facture payée.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Facture en retard.
     */
    public function overdue(): static
    {
        $issueDate = fake()->dateTimeBetween('-3 months', '-2 months');

        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'issue_date' => $issueDate,
            'due_date' => (clone $issueDate)->modify('+30 days'),
        ]);
    }

    /**
     * Facture annulée.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Facture de commission.
     */
    public function commission(): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_type' => 'commission',
        ]);
    }

    /**
     * Facture de loyer.
     */
    public function rent(): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_type' => 'rent',
        ]);
    }

    /**
     * Facture avec TVA (18%).
     */
    public function withTax(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? 100000;
            return [
                'tax_amount' => round($amount * 0.18),
            ];
        });
    }

    /**
     * Facture sans TVA.
     */
    public function withoutTax(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_amount' => 0,
        ]);
    }
}
