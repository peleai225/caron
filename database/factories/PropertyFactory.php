<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Abidjan', 'Bouaké', 'Yamoussoukro', 'San Pedro', 'Daloa', 'Korhogo'];
        $neighborhoods = [
            'Abidjan' => ['Cocody', 'Plateau', 'Marcory', 'Koumassi', 'Yopougon', 'Treichville', 'Abobo', 'Adjamé', 'Riviera Faya', 'Angré'],
            'Bouaké' => ['Commerce', 'Koko', 'Air France', 'Dar es Salam'],
            'Yamoussoukro' => ['Habitat', 'Dioulakro', 'Morofé', 'N\'Zuessi'],
            'San Pedro' => ['Bardot', 'Cité', 'Lac', 'Zimbabwe'],
            'Daloa' => ['Commerce', 'Tazibouo', 'Lobia', 'Orly'],
            'Korhogo' => ['Petit Paris', 'Kassirimé', 'Soba', 'Koko'],
        ];

        $city = fake()->randomElement($cities);
        $neighborhood = fake()->randomElement($neighborhoods[$city]);
        $type = fake()->randomElement(['maison', 'immeuble', 'boutique', 'terrain']);

        $rents = [
            'maison' => fake()->numberBetween(75000, 500000),
            'immeuble' => fake()->numberBetween(200000, 1500000),
            'boutique' => fake()->numberBetween(50000, 300000),
            'terrain' => null,
        ];

        $surfaces = [
            'maison' => fake()->numberBetween(60, 400),
            'immeuble' => fake()->numberBetween(200, 2000),
            'boutique' => fake()->numberBetween(15, 120),
            'terrain' => fake()->numberBetween(200, 5000),
        ];

        return [
            'agency_id' => Agency::factory(),
            'owner_id' => Owner::factory(),
            'parent_id' => null,
            'type' => $type,
            'unit_type' => null,
            'status' => fake()->randomElement(['libre', 'occupe', 'maintenance']),
            'address' => $neighborhood . ', ' . $city,
            'designation' => null,
            'city' => $city,
            'neighborhood' => $neighborhood,
            'bedrooms' => $type === 'maison' ? fake()->numberBetween(1, 6) : null,
            'bathrooms' => $type === 'maison' ? fake()->numberBetween(1, 3) : null,
            'surface' => $surfaces[$type],
            'description' => fake()->optional(0.5)->sentence(12),
            'monthly_rent' => $rents[$type] ? round($rents[$type] / 5000) * 5000 : null,
            'is_active' => true,
        ];
    }

    /**
     * Maison.
     */
    public function maison(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'maison',
            'unit_type' => null,
            'bedrooms' => fake()->numberBetween(2, 5),
            'bathrooms' => fake()->numberBetween(1, 3),
            'surface' => fake()->numberBetween(80, 350),
            'monthly_rent' => round(fake()->numberBetween(75000, 400000) / 5000) * 5000,
        ]);
    }

    /**
     * Immeuble (bien parent).
     */
    public function immeuble(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'immeuble',
            'unit_type' => null,
            'parent_id' => null,
            'bedrooms' => null,
            'bathrooms' => null,
            'surface' => fake()->numberBetween(300, 2000),
            'monthly_rent' => round(fake()->numberBetween(500000, 2000000) / 10000) * 10000,
        ]);
    }

    /**
     * Unité dans un immeuble (studio, appartement, etc.).
     */
    public function unite(): static
    {
        $unitTypes = ['studio', 'deux_pieces', 'trois_pieces', 'quatre_pieces', 'appartement', 'chambre', 'bureau'];
        $unitType = fake()->randomElement($unitTypes);

        $unitRents = [
            'studio' => fake()->numberBetween(35000, 80000),
            'deux_pieces' => fake()->numberBetween(50000, 120000),
            'trois_pieces' => fake()->numberBetween(75000, 200000),
            'quatre_pieces' => fake()->numberBetween(100000, 300000),
            'appartement' => fake()->numberBetween(80000, 250000),
            'chambre' => fake()->numberBetween(20000, 50000),
            'bureau' => fake()->numberBetween(50000, 200000),
        ];

        return $this->state(fn (array $attributes) => [
            'type' => 'immeuble',
            'unit_type' => $unitType,
            'designation' => match ($unitType) {
                'studio' => 'Studio ' . fake()->randomElement(['A', 'B', 'C', 'D']) . fake()->numberBetween(1, 9),
                'chambre' => 'Chambre ' . fake()->numberBetween(1, 20),
                'bureau' => 'Bureau ' . fake()->numberBetween(1, 10),
                default => 'Apt ' . fake()->numberBetween(1, 30),
            },
            'bedrooms' => in_array($unitType, ['studio', 'chambre']) ? 1 : fake()->numberBetween(2, 4),
            'bathrooms' => $unitType === 'chambre' ? 1 : fake()->numberBetween(1, 2),
            'surface' => fake()->numberBetween(18, 150),
            'monthly_rent' => round($unitRents[$unitType] / 5000) * 5000,
        ]);
    }

    /**
     * Boutique.
     */
    public function boutique(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'boutique',
            'unit_type' => null,
            'bedrooms' => null,
            'bathrooms' => fake()->numberBetween(0, 1),
            'surface' => fake()->numberBetween(15, 100),
            'monthly_rent' => round(fake()->numberBetween(50000, 300000) / 5000) * 5000,
        ]);
    }

    /**
     * Terrain.
     */
    public function terrain(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'terrain',
            'unit_type' => null,
            'bedrooms' => null,
            'bathrooms' => null,
            'surface' => fake()->numberBetween(300, 5000),
            'monthly_rent' => null,
        ]);
    }

    /**
     * Bien libre.
     */
    public function libre(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'libre',
        ]);
    }

    /**
     * Bien occupé.
     */
    public function occupe(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupe',
        ]);
    }

    /**
     * Bien en maintenance.
     */
    public function enMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }

    /**
     * Bien inactif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
