<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\DocumentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentTemplate>
 */
class DocumentTemplateFactory extends Factory
{
    protected $model = DocumentTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $templates = [
            ['name' => 'Bail d\'habitation vide', 'category' => 'contract'],
            ['name' => 'Bail meublé', 'category' => 'contract'],
            ['name' => 'Bail commercial', 'category' => 'contract'],
            ['name' => 'Résiliation de bail', 'category' => 'termination'],
            ['name' => 'Avenant au contrat', 'category' => 'amendment'],
            ['name' => 'Mise en demeure', 'category' => 'notification'],
            ['name' => 'Quittance de loyer', 'category' => 'receipt'],
            ['name' => 'Etat des lieux', 'category' => 'legal'],
            ['name' => 'Promesse de vente', 'category' => 'sale'],
            ['name' => 'Mandat de gestion', 'category' => 'management'],
            ['name' => 'Attestation de domicile', 'category' => 'other'],
        ];

        $template = fake()->randomElement($templates);
        $name = $template['name'];

        return [
            'agency_id' => fake()->optional(0.6)->passthrough(Agency::factory()),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'category' => $template['category'],
            'country' => 'CI',
            'description' => 'Modèle de ' . strtolower($name) . ' conforme au droit ivoirien.',
            'file_path' => 'templates/' . Str::slug($name) . '.docx',
            'variables' => [
                'owner_name', 'owner_address', 'tenant_name', 'tenant_cni',
                'property_address', 'rent_amount', 'start_date', 'end_date',
            ],
            'is_system' => false,
            'is_default' => false,
            'is_active' => true,
            'version' => 1,
            'previous_version_id' => null,
            'usage_count' => fake()->numberBetween(0, 50),
        ];
    }

    /**
     * Template système.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => true,
            'agency_id' => null,
        ]);
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
     * Template de type contrat.
     */
    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'contract',
            'name' => 'Bail d\'habitation - ' . fake()->numerify('V##'),
        ]);
    }

    /**
     * Template de type résiliation.
     */
    public function termination(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'termination',
            'name' => 'Résiliation de bail - ' . fake()->numerify('V##'),
        ]);
    }

    /**
     * Template de type quittance.
     */
    public function receipt(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'receipt',
            'name' => 'Quittance de loyer - ' . fake()->numerify('V##'),
        ]);
    }

    /**
     * Template personnalisé (non système).
     */
    public function custom(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => false,
            'agency_id' => Agency::factory(),
        ]);
    }
}
