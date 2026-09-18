<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'payment_due' => [
                'title' => 'Echéance de paiement',
                'message' => 'Le loyer du mois est dû. Montant : %s FCFA.',
            ],
            'payment_received' => [
                'title' => 'Paiement reçu',
                'message' => 'Paiement de %s FCFA reçu avec succès.',
            ],
            'contract_expiring' => [
                'title' => 'Contrat bientôt expiré',
                'message' => 'Le contrat %s expire dans %d jours.',
            ],
            'payment_overdue' => [
                'title' => 'Paiement en retard',
                'message' => 'Le paiement de %s FCFA est en retard de %d jours.',
            ],
            'contract_created' => [
                'title' => 'Nouveau contrat',
                'message' => 'Un nouveau contrat a été créé : %s.',
            ],
            'maintenance_request' => [
                'title' => 'Demande de maintenance',
                'message' => 'Une demande de maintenance a été soumise pour %s.',
            ],
        ];

        $typeKey = fake()->randomElement(array_keys($types));
        $type = $types[$typeKey];
        $amount = number_format(fake()->numberBetween(50000, 500000), 0, '', ' ');

        return [
            'user_id' => User::factory(),
            'type' => $typeKey,
            'title' => $type['title'],
            'message' => sprintf($type['message'], $amount, fake()->numberBetween(1, 30)),
            'is_read' => false,
            'read_at' => null,
            'data' => fake()->optional(0.5)->passthrough([
                'amount' => fake()->numberBetween(50000, 500000),
                'contract_id' => fake()->numberBetween(1, 100),
            ]),
        ];
    }

    /**
     * Notification non lue.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Notification lue.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Notification de paiement dû.
     */
    public function paymentDue(): static
    {
        $amount = number_format(fake()->numberBetween(50000, 500000), 0, '', ' ');

        return $this->state(fn (array $attributes) => [
            'type' => 'payment_due',
            'title' => 'Echéance de paiement',
            'message' => "Le loyer du mois est dû. Montant : {$amount} FCFA.",
        ]);
    }

    /**
     * Notification de paiement reçu.
     */
    public function paymentReceived(): static
    {
        $amount = number_format(fake()->numberBetween(50000, 500000), 0, '', ' ');

        return $this->state(fn (array $attributes) => [
            'type' => 'payment_received',
            'title' => 'Paiement reçu',
            'message' => "Paiement de {$amount} FCFA reçu avec succès.",
        ]);
    }

    /**
     * Notification de contrat expirant.
     */
    public function contractExpiring(): static
    {
        $days = fake()->numberBetween(1, 30);
        $contractNumber = 'CTR-' . fake()->numerify('######');

        return $this->state(fn (array $attributes) => [
            'type' => 'contract_expiring',
            'title' => 'Contrat bientôt expiré',
            'message' => "Le contrat {$contractNumber} expire dans {$days} jours.",
        ]);
    }
}
