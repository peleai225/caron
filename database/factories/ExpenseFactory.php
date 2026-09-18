<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Expense;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $descriptions = [
            'maintenance' => [
                'Réparation plomberie',
                'Remplacement serrure porte entrée',
                'Peinture appartement',
                'Réparation fuite toiture',
                'Entretien climatisation',
                'Réparation électrique',
                'Débouchage canalisation',
            ],
            'tax' => [
                'Taxe foncière annuelle',
                'Impôt sur le revenu locatif',
                'Taxe d\'habitation',
                'Contribution foncière',
            ],
            'insurance' => [
                'Assurance multirisque immeuble',
                'Assurance propriétaire non occupant',
                'Assurance responsabilité civile',
            ],
            'utilities' => [
                'Facture CIE (électricité)',
                'Facture SODECI (eau)',
                'Abonnement internet immeuble',
                'Facture gardiennage',
            ],
            'other' => [
                'Frais de notaire',
                'Honoraires avocat',
                'Frais de déplacement',
                'Fournitures bureau agence',
                'Commission agent',
            ],
        ];

        $type = fake()->randomElement(['maintenance', 'tax', 'insurance', 'utilities', 'other']);

        $amounts = [
            'maintenance' => fake()->numberBetween(15000, 500000),
            'tax' => fake()->numberBetween(50000, 300000),
            'insurance' => fake()->numberBetween(100000, 500000),
            'utilities' => fake()->numberBetween(10000, 150000),
            'other' => fake()->numberBetween(10000, 200000),
        ];

        return [
            'agency_id' => Agency::factory(),
            'property_id' => fake()->optional(0.7)->passthrough(Property::factory()),
            'type' => $type,
            'amount' => round($amounts[$type] / 1000) * 1000,
            'description' => fake()->randomElement($descriptions[$type]),
            'expense_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'receipt_path' => null,
        ];
    }

    /**
     * Dépense de maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'maintenance',
            'amount' => round(fake()->numberBetween(15000, 500000) / 1000) * 1000,
        ]);
    }

    /**
     * Dépense fiscale.
     */
    public function tax(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'tax',
            'amount' => round(fake()->numberBetween(50000, 300000) / 1000) * 1000,
        ]);
    }

    /**
     * Dépense assurance.
     */
    public function insurance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'insurance',
            'amount' => round(fake()->numberBetween(100000, 500000) / 1000) * 1000,
        ]);
    }

    /**
     * Dépense charges (eau, électricité).
     */
    public function utilities(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'utilities',
            'amount' => round(fake()->numberBetween(10000, 150000) / 1000) * 1000,
        ]);
    }
}
