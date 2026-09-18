<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['bank', 'wave', 'orange_money', 'mtn_money', 'cash']);

        $names = [
            'bank' => ['Compte courant SGBCI', 'Compte BICICI', 'Compte Ecobank', 'Compte SIB', 'Compte BACI', 'Compte BOA'],
            'wave' => ['Compte Wave Agence', 'Wave Principal'],
            'orange_money' => ['Compte Orange Money', 'OM Encaissements'],
            'mtn_money' => ['Compte MTN Money', 'MTN MoMo Agence'],
            'cash' => ['Caisse principale', 'Caisse secondaire', 'Petite caisse'],
        ];

        $bankNames = [
            'bank' => fake()->randomElement(['SGBCI', 'BICICI', 'Ecobank CI', 'SIB', 'BACI', 'BOA CI', 'NSIA Banque', 'Banque Atlantique']),
            'wave' => 'Wave CI',
            'orange_money' => 'Orange CI',
            'mtn_money' => 'MTN CI',
            'cash' => null,
        ];

        return [
            'agency_id' => Agency::factory(),
            'name' => fake()->randomElement($names[$type]),
            'type' => $type,
            'account_number' => $type === 'bank' ? 'CI' . fake()->numerify('## #### #### ####') : ($type === 'cash' ? null : '+225 ' . fake()->randomElement(['07', '05', '01']) . fake()->numerify('########')),
            'bank_name' => $bankNames[$type],
            'balance' => round(fake()->numberBetween(0, 15000000) / 1000) * 1000,
            'is_active' => true,
        ];
    }

    /**
     * Compte bancaire.
     */
    public function bank(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'bank',
            'name' => fake()->randomElement(['Compte courant SGBCI', 'Compte BICICI', 'Compte Ecobank', 'Compte SIB']),
            'bank_name' => fake()->randomElement(['SGBCI', 'BICICI', 'Ecobank CI', 'SIB', 'BACI']),
            'account_number' => 'CI' . fake()->numerify('## #### #### ####'),
        ]);
    }

    /**
     * Caisse (espèces).
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'cash',
            'name' => fake()->randomElement(['Caisse principale', 'Caisse secondaire', 'Petite caisse']),
            'bank_name' => null,
            'account_number' => null,
        ]);
    }

    /**
     * Compte Wave.
     */
    public function wave(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'wave',
            'name' => 'Compte Wave Agence',
            'bank_name' => 'Wave CI',
            'account_number' => '+225 07' . fake()->numerify('########'),
        ]);
    }

    /**
     * Compte Orange Money.
     */
    public function orangeMoney(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'orange_money',
            'name' => 'Compte Orange Money',
            'bank_name' => 'Orange CI',
            'account_number' => '+225 07' . fake()->numerify('########'),
        ]);
    }

    /**
     * Compte MTN Money.
     */
    public function mtnMoney(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'mtn_money',
            'name' => 'Compte MTN Money',
            'bank_name' => 'MTN CI',
            'account_number' => '+225 05' . fake()->numerify('########'),
        ]);
    }

    /**
     * Compte inactif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Compte avec solde zéro.
     */
    public function empty(): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => 0,
        ]);
    }
}
