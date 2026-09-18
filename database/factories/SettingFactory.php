<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $settings = [
            ['key' => 'penalty_rate', 'value' => '10', 'type' => 'integer', 'group' => 'billing', 'description' => 'Taux de pénalité de retard (%)'],
            ['key' => 'penalty_grace_days', 'value' => '5', 'type' => 'integer', 'group' => 'billing', 'description' => 'Jours de grâce avant pénalité'],
            ['key' => 'commission_rate', 'value' => '10', 'type' => 'integer', 'group' => 'billing', 'description' => 'Taux de commission agence (%)'],
            ['key' => 'currency', 'value' => 'FCFA', 'type' => 'string', 'group' => 'general', 'description' => 'Devise utilisée'],
            ['key' => 'country', 'value' => 'CI', 'type' => 'string', 'group' => 'general', 'description' => 'Pays par défaut'],
            ['key' => 'tax_rate', 'value' => '18', 'type' => 'integer', 'group' => 'billing', 'description' => 'Taux de TVA (%)'],
            ['key' => 'auto_penalty', 'value' => 'true', 'type' => 'boolean', 'group' => 'billing', 'description' => 'Calcul automatique des pénalités'],
            ['key' => 'notification_email', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifications par email activées'],
            ['key' => 'contract_expiry_days', 'value' => '30', 'type' => 'integer', 'group' => 'notifications', 'description' => 'Jours avant alerte expiration contrat'],
            ['key' => 'app_name', 'value' => 'Caron Immobilier', 'type' => 'string', 'group' => 'general', 'description' => 'Nom de l\'application'],
        ];

        $setting = fake()->randomElement($settings);

        return [
            'agency_id' => fake()->optional(0.4)->passthrough(Agency::factory()),
            'key' => $setting['key'] . '_' . fake()->unique()->numerify('###'),
            'value' => $setting['value'],
            'type' => $setting['type'],
            'description' => $setting['description'],
            'group' => $setting['group'],
        ];
    }

    /**
     * Setting global (sans agence).
     */
    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'agency_id' => null,
        ]);
    }

    /**
     * Setting pour une agence spécifique.
     */
    public function forAgency(): static
    {
        return $this->state(fn (array $attributes) => [
            'agency_id' => Agency::factory(),
        ]);
    }

    /**
     * Setting de type string.
     */
    public function string(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'string',
        ]);
    }

    /**
     * Setting de type boolean.
     */
    public function boolean(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'boolean',
            'value' => fake()->randomElement(['true', 'false']),
        ]);
    }

    /**
     * Setting de type integer.
     */
    public function integer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'integer',
            'value' => (string) fake()->numberBetween(1, 100),
        ]);
    }

    /**
     * Setting du groupe billing.
     */
    public function billing(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'billing',
        ]);
    }

    /**
     * Setting du groupe notifications.
     */
    public function notifications(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'notifications',
        ]);
    }
}
