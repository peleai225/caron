<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\EtatDesLieux;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EtatDesLieux>
 */
class EtatDesLieuxFactory extends Factory
{
    protected $model = EtatDesLieux::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $observations = [
            'Murs en bon état, peinture blanche uniforme.',
            'Sol carrelé sans fissures. Joints propres.',
            'Menuiseries aluminium en bon état. Fermeture correcte.',
            'Installation électrique conforme. Prises fonctionnelles.',
            'Robinetterie en bon état. Pas de fuite.',
            'Plafond propre, pas de traces d\'humidité.',
            'Climatisation split fonctionnelle, télécommande présente.',
            'Quelques traces d\'usure sur la peinture du salon.',
            'Porte d\'entrée en bon état, serrure fonctionnelle. 2 clés remises.',
            'Cuisine équipée, évier inox en bon état.',
        ];

        return [
            'property_id' => Property::factory(),
            'contract_id' => fake()->optional(0.8)->passthrough(Contract::factory()),
            'type' => fake()->randomElement(['entree', 'sortie']),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'observations' => implode("\n", fake()->randomElements($observations, fake()->numberBetween(3, 6))),
            'pdf_path' => null,
        ];
    }

    /**
     * Etat des lieux d'entrée.
     */
    public function entree(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'entree',
            'observations' => implode("\n", [
                'Murs en bon état, peinture blanche uniforme.',
                'Sol carrelé sans fissures.',
                'Installation électrique conforme.',
                'Robinetterie en bon état, pas de fuite.',
                'Porte d\'entrée en bon état. ' . fake()->numberBetween(2, 3) . ' clés remises.',
            ]),
        ]);
    }

    /**
     * Etat des lieux de sortie.
     */
    public function sortie(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sortie',
            'observations' => implode("\n", [
                'Traces d\'usure normale sur peinture.',
                'Sol carrelé correct, nettoyage à prévoir.',
                'Quelques prises électriques à vérifier.',
                'Robinet cuisine à remplacer (fuite légère).',
                'Clés restituées (' . fake()->numberBetween(1, 3) . ').',
            ]),
        ]);
    }
}
