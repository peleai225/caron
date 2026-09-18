<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Contract;
use App\Models\Litige;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Litige>
 */
class LitigeFactory extends Factory
{
    protected $model = Litige::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $naturesLitige = array_keys(Litige::naturesLitige());
        $typesContrat = array_keys(Litige::typesContrat());
        $startDate = fake()->dateTimeBetween('-1 year', 'now');

        $cities = ['Abidjan', 'Bouaké', 'Yamoussoukro', 'San Pedro', 'Daloa', 'Korhogo'];

        return [
            'agency_id' => Agency::factory(),
            'contract_id' => fake()->optional(0.7)->passthrough(Contract::factory()),
            'tenant_id' => fake()->optional(0.8)->passthrough(Tenant::factory()),
            'property_id' => fake()->optional(0.7)->passthrough(Property::factory()),
            'owner_id' => fake()->optional(0.5)->passthrough(Owner::factory()),
            'reference' => 'LIT-' . fake()->unique()->numerify('######'),
            'personnes_concernées' => fake()->name() . ', ' . fake()->name(),
            'lieu_intervention' => fake()->randomElement($cities) . ' - ' . fake()->streetAddress(),
            'type_contrat' => fake()->randomElement($typesContrat),
            'nature_litige' => fake()->randomElement($naturesLitige),
            'description' => fake()->paragraph(3),
            'couts_engages' => [
                'huissier' => fake()->optional(0.4)->numberBetween(50000, 300000),
                'avocat' => fake()->optional(0.3)->numberBetween(100000, 500000),
                'reparation' => fake()->optional(0.3)->numberBetween(50000, 1000000),
                'transport' => fake()->optional(0.2)->numberBetween(5000, 50000),
            ],
            'pertes_financieres' => [
                'loyer_impaye' => fake()->optional(0.6)->numberBetween(50000, 500000),
                'charges_non_recouvrees' => fake()->optional(0.3)->numberBetween(10000, 100000),
            ],
            'suivi_commentaires' => fake()->optional(0.5)->paragraph(),
            'statut' => fake()->randomElement(['en_cours', 'regle', 'cloture']),
            'date_debut' => $startDate,
            'date_fin' => fake()->optional(0.3)->dateTimeBetween($startDate, 'now'),
        ];
    }

    /**
     * Litige en cours.
     */
    public function enCours(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_cours',
            'date_fin' => null,
        ]);
    }

    /**
     * Litige réglé.
     */
    public function regle(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'regle',
            'date_fin' => fake()->dateTimeBetween($attributes['date_debut'] ?? '-6 months', 'now'),
        ]);
    }

    /**
     * Litige clôturé.
     */
    public function cloture(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'cloture',
            'date_fin' => fake()->dateTimeBetween($attributes['date_debut'] ?? '-6 months', 'now'),
        ]);
    }

    /**
     * Litige pour retard de paiement.
     */
    public function retardPaiement(): static
    {
        return $this->state(fn (array $attributes) => [
            'nature_litige' => 'retard_paiement',
            'description' => 'Retard de paiement du loyer. ' . fake()->paragraph(),
        ]);
    }

    /**
     * Litige pour non-paiement.
     */
    public function nonPaiement(): static
    {
        return $this->state(fn (array $attributes) => [
            'nature_litige' => 'non_paiement',
            'description' => 'Non-paiement du loyer depuis plusieurs mois. ' . fake()->paragraph(),
            'pertes_financieres' => [
                'loyer_impaye' => fake()->numberBetween(200000, 1500000),
                'charges_non_recouvrees' => fake()->numberBetween(30000, 200000),
            ],
        ]);
    }

    /**
     * Litige pour dégradations.
     */
    public function degradations(): static
    {
        return $this->state(fn (array $attributes) => [
            'nature_litige' => 'degradations',
            'description' => 'Dégradations constatées dans le logement. ' . fake()->paragraph(),
            'couts_engages' => [
                'reparation' => fake()->numberBetween(100000, 2000000),
            ],
        ]);
    }
}
