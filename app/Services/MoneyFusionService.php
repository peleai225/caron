<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoneyFusionService
{
    private function apiUrl(): string
    {
        return Setting::get('moneyfusion_api_url')
            ?? config('services.moneyfusion.api_url')
            ?? '';
    }

    private function apiKey(): string
    {
        return Setting::get('moneyfusion_api_key')
            ?? config('services.moneyfusion.api_key')
            ?? '';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiUrl()) && ! empty($this->apiKey());
    }

    /**
     * Initier un paiement MoneyFusion.
     * Retourne ['statut' => true, 'token' => '...', 'url' => 'https://pay.moneyfusion.net/...']
     */
    public function initiatePayment(array $data): array
    {
        $apiUrl = $this->apiUrl();
        if (empty($apiUrl)) {
            return ['statut' => false, 'message' => 'MoneyFusion non configuré. Renseignez l\'URL API dans les paramètres.'];
        }

        $payload = [
            'totalPrice'    => (int) $data['amount'],
            'numeroSend'    => $data['phone'],
            'nomclient'     => $data['client_name'],
            'article'       => [['loyer' => (int) $data['amount']]],
            'personal_Info' => [[
                'payment_id'  => $data['payment_ref'] ?? null,
                'contract_id' => $data['contract_id'] ?? null,
                'agency_id'   => $data['agency_id'] ?? null,
            ]],
            'return_url'    => $data['return_url'],
            'webhook_url'   => route('moneyfusion.webhook'),
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey(),
                ])
                ->post($apiUrl, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('MoneyFusion initiate error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return ['statut' => false, 'message' => 'Erreur de connexion au service de paiement.'];
        } catch (\Exception $e) {
            Log::error('MoneyFusion exception', ['message' => $e->getMessage()]);
            return ['statut' => false, 'message' => 'Service de paiement indisponible.'];
        }
    }

    /**
     * Vérifier le statut d'un paiement via son token.
     * Statuts possibles : pending | paid | failure | no paid
     */
    public function checkPaymentStatus(string $token): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey()])
                ->get("https://www.pay.moneyfusion.net/paiementNotif/{$token}");

            if ($response->successful()) {
                return $response->json();
            }

            return ['statut' => false, 'message' => 'Impossible de vérifier le statut.'];
        } catch (\Exception $e) {
            Log::error('MoneyFusion check status exception', ['message' => $e->getMessage()]);
            return ['statut' => false, 'message' => 'Service indisponible.'];
        }
    }
}
