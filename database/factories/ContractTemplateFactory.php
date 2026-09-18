<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\ContractTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractTemplate>
 */
class ContractTemplateFactory extends Factory
{
    protected $model = ContractTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $templateNames = [
            'Bail d\'habitation vide - Standard CI',
            'Bail meublé - Standard CI',
            'Bail commercial - Standard CI',
            'Bail professionnel - Standard CI',
            'Contrat de location saisonnière',
            'Bail mixte habitation/commercial',
        ];

        return [
            'agency_id' => fake()->optional(0.7)->passthrough(Agency::factory()),
            'name' => fake()->randomElement($templateNames),
            'country' => 'CI',
            'content' => $this->generateTemplateContent(),
            'is_default' => false,
            'is_active' => true,
        ];
    }

    /**
     * Template par défaut.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Template inactif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Template système (global, sans agence).
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'agency_id' => null,
            'is_default' => true,
        ]);
    }

    /**
     * Génère un contenu de template réaliste.
     */
    private function generateTemplateContent(): string
    {
        return <<<'TEMPLATE'
CONTRAT DE BAIL D'HABITATION

Entre les soussignés :

Le Bailleur : {{owner_name}}, demeurant à {{owner_address}}
Ci-après dénommé "le Bailleur"

Et

Le Preneur : {{tenant_name}}, titulaire de la CNI n° {{tenant_cni}}
Ci-après dénommé "le Preneur"

IL A ÉTÉ CONVENU CE QUI SUIT :

Article 1 - OBJET
Le Bailleur donne en location au Preneur le bien situé à : {{property_address}}
Type : {{property_type}}

Article 2 - DURÉE
Le présent bail est consenti pour une durée de {{contract_duration}} mois, à compter du {{start_date}} jusqu'au {{end_date}}.

Article 3 - LOYER
Le loyer mensuel est fixé à {{rent_amount}} FCFA, payable le {{payment_day}} de chaque mois.

Article 4 - DÉPÔT DE GARANTIE
Le Preneur verse un dépôt de garantie de {{deposit}} FCFA.

Article 5 - CHARGES
Les charges locatives sont à la charge du Preneur.

Fait à {{city}}, le {{signed_date}}

Le Bailleur                    Le Preneur
TEMPLATE;
    }
}
