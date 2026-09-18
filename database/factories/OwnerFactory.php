<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Owner>
 */
class OwnerFactory extends Factory
{
    protected $model = Owner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Abidjan', 'Bouaké', 'Yamoussoukro', 'San Pedro', 'Daloa', 'Korhogo'];
        $ivorianFirstNames = ['Kouadio', 'Konan', 'Aka', 'Yao', 'Koffi', 'Aya', 'Amani', 'Adjoua', 'Brou', 'Kouassi'];
        $ivorianLastNames = ['Kouamé', 'Koné', 'Traoré', 'Coulibaly', 'Diallo', 'N\'Guessan', 'Ouattara', 'Bamba', 'Touré', 'Kacou'];

        return [
            'agency_id' => Agency::factory(),
            'name' => fake()->randomElement($ivorianFirstNames) . ' ' . fake()->randomElement($ivorianLastNames),
            'email' => fake()->optional(0.7)->unique()->safeEmail(),
            'phone' => '+225 ' . fake()->randomElement(['01', '05', '07']) . fake()->numerify('########'),
            'address' => fake()->streetAddress() . ', ' . fake()->randomElement($cities),
            'identification_number' => 'CI' . fake()->numerify('###########'),
            'notes' => fake()->optional(0.3)->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Propriétaire inactif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Propriétaire entreprise.
     */
    public function entreprise(): static
    {
        $suffixes = ['SARL', 'SA', 'SCI', 'SASU'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->company() . ' ' . fake()->randomElement($suffixes),
            'identification_number' => 'RCCM-CI-ABJ-' . fake()->numerify('####-B-####'),
        ]);
    }

    /**
     * Propriétaire particulier.
     */
    public function particulier(): static
    {
        return $this->state(fn (array $attributes) => [
            'identification_number' => 'CI' . fake()->numerify('###########'),
        ]);
    }
}
