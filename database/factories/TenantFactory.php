<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ivorianFirstNames = ['Kouadio', 'Konan', 'Aka', 'Yao', 'Koffi', 'Aya', 'Amani', 'Adjoua', 'Brou', 'Kouassi', 'Affoué', 'Amenan', 'Ahou', 'Tanoh'];
        $ivorianLastNames = ['Kouamé', 'Koné', 'Traoré', 'Coulibaly', 'Diallo', 'N\'Guessan', 'Ouattara', 'Bamba', 'Touré', 'Kacou', 'Yapi', 'Assi', 'Aka'];

        return [
            'agency_id' => Agency::factory(),
            'user_id' => null,
            'first_name' => fake()->randomElement($ivorianFirstNames),
            'last_name' => fake()->randomElement($ivorianLastNames),
            'email' => fake()->optional(0.7)->unique()->safeEmail(),
            'phone' => '+225 ' . fake()->randomElement(['01', '05', '07']) . fake()->numerify('########'),
            'cni_number' => 'CI' . fake()->numerify('###########'),
            'cni_path' => null,
            'status' => 'actif',
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Locataire actif.
     */
    public function actif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'actif',
        ]);
    }

    /**
     * Locataire en retard de paiement.
     */
    public function enRetard(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_retard',
        ]);
    }

    /**
     * Locataire résilié.
     */
    public function resilie(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resilie',
        ]);
    }
}
