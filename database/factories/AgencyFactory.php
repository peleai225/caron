<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agency>
 */
class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Abidjan', 'Bouaké', 'Yamoussoukro', 'San Pedro', 'Daloa', 'Korhogo', 'Man', 'Gagnoa'];
        $suffixes = ['Immobilier', 'Immo', 'Gestion', 'Patrimoine', 'Habitat', 'Résidences'];
        $city = fake()->randomElement($cities);

        return [
            'name' => fake()->lastName() . ' ' . fake()->randomElement($suffixes),
            'email' => fake()->unique()->companyEmail(),
            'phone' => '+225 ' . fake()->randomElement(['01', '05', '07']) . fake()->numerify('########'),
            'address' => fake()->streetAddress() . ', ' . $city,
            'city' => $city,
            'country' => 'CI',
            'tax_id' => 'CI-' . fake()->numerify('######'),
            'is_active' => true,
            'logo_path' => null,
            'favicon_path' => null,
            'website' => fake()->optional(0.5)->url(),
            'description' => fake()->optional(0.6)->sentence(10),
        ];
    }

    /**
     * Agence inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Agence basée à Abidjan.
     */
    public function abidjan(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'Abidjan',
            'address' => fake()->randomElement([
                'Cocody Riviera 3, Abidjan',
                'Plateau, Rue du Commerce, Abidjan',
                'Marcory Zone 4, Abidjan',
                'Yopougon Toits Rouges, Abidjan',
                'Koumassi Remblais, Abidjan',
            ]),
        ]);
    }
}
