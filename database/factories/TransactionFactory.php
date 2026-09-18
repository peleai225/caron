<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);
        $descriptions = [
            'income' => [
                'Encaissement loyer',
                'Paiement loyer reçu',
                'Commission sur gestion',
                'Dépôt de garantie reçu',
                'Régularisation charges',
            ],
            'expense' => [
                'Réparation plomberie',
                'Frais de maintenance',
                'Paiement propriétaire',
                'Frais d\'agence',
                'Charges communes',
                'Facture CIE',
                'Facture SODECI',
            ],
        ];

        return [
            'account_id' => Account::factory(),
            'payment_id' => null,
            'type' => $type,
            'amount' => round(fake()->numberBetween(10000, 1000000) / 1000) * 1000,
            'reference' => 'TXN-' . fake()->unique()->numerify('########'),
            'description' => fake()->randomElement($descriptions[$type]),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'transaction_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Transaction de revenu.
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'description' => fake()->randomElement(['Encaissement loyer', 'Commission sur gestion', 'Dépôt de garantie reçu']),
        ]);
    }

    /**
     * Transaction de dépense.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'description' => fake()->randomElement(['Réparation plomberie', 'Frais de maintenance', 'Paiement propriétaire']),
        ]);
    }

    /**
     * Transaction complétée.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Transaction en attente.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Transaction échouée.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Transaction annulée.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
