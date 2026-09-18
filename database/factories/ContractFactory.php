<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 years', 'now');
        $endDate = (clone $startDate)->modify('+' . fake()->randomElement([12, 24, 36]) . ' months');
        $rent = round(fake()->numberBetween(50000, 500000) / 5000) * 5000;

        return [
            'agency_id' => Agency::factory(),
            'tenant_id' => Tenant::factory(),
            'property_id' => Property::factory(),
            'owner_id' => Owner::factory(),
            'contract_number' => 'CTR-' . fake()->unique()->numerify('######'),
            'type_contrat' => fake()->randomElement([
                'bail_habitation_vide',
                'bail_meuble',
                'bail_commercial',
                'bail_professionnel',
            ]),
            'rent_amount' => $rent,
            'deposit' => $rent * fake()->randomElement([1, 2, 3]),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_frequency' => 'monthly',
            'payment_day' => fake()->numberBetween(1, 5),
            'status' => 'active',
            'template_id' => null,
            'notes' => fake()->optional(0.3)->sentence(),
            'signed_at' => fake()->optional(0.7)->dateTimeBetween($startDate, $startDate),
            'pdf_path' => null,
        ];
    }

    /**
     * Contrat actif.
     */
    public function active(): static
    {
        $startDate = fake()->dateTimeBetween('-1 year', '-1 month');
        $endDate = (clone $startDate)->modify('+24 months');

        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'signed_at' => $startDate,
        ]);
    }

    /**
     * Contrat expiré.
     */
    public function expired(): static
    {
        $startDate = fake()->dateTimeBetween('-3 years', '-13 months');
        $endDate = (clone $startDate)->modify('+12 months');

        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'signed_at' => $startDate,
        ]);
    }

    /**
     * Contrat résilié.
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terminated',
        ]);
    }

    /**
     * Contrat brouillon.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'signed_at' => null,
        ]);
    }

    /**
     * Bail habitation vide.
     */
    public function bailHabitation(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_contrat' => 'bail_habitation_vide',
        ]);
    }

    /**
     * Bail commercial.
     */
    public function bailCommercial(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_contrat' => 'bail_commercial',
            'rent_amount' => round(fake()->numberBetween(150000, 800000) / 5000) * 5000,
        ]);
    }

    /**
     * Contrat qui expire bientôt (dans les 30 jours).
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subMonths(11),
            'end_date' => now()->addDays(fake()->numberBetween(1, 30)),
            'signed_at' => now()->subMonths(11),
        ]);
    }

    /**
     * Paiement trimestriel.
     */
    public function trimestriel(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_frequency' => 'quarterly',
        ]);
    }
}
