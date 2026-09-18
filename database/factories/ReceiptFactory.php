<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receipt>
 */
class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'receipt_number' => 'REC-' . fake()->unique()->numerify('######'),
            'amount' => round(fake()->numberBetween(50000, 500000) / 5000) * 5000,
            'issue_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'pdf_path' => null,
        ];
    }

    /**
     * Reçu avec PDF généré.
     */
    public function withPdf(): static
    {
        return $this->state(function (array $attributes) {
            $number = $attributes['receipt_number'] ?? 'REC-000000';
            return [
                'pdf_path' => 'receipts/' . $number . '.pdf',
            ];
        });
    }
}
