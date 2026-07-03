<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiKey;
    protected ?string $apiUrl;
    protected ?string $phoneNumberId;

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key') ?? env('WHATSAPP_API_KEY', '');
        $this->apiUrl = config('services.whatsapp.api_url') ?? env('WHATSAPP_API_URL', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id') ?? env('WHATSAPP_PHONE_NUMBER_ID', '');
    }

    /**
     * Envoie un message WhatsApp
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            Log::warning('WhatsApp Service non configuré');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/messages', [
                'to' => $this->formatPhone($phone),
                'type' => 'text',
                'text' => [
                    'body' => $message,
                ],
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message envoyé', ['phone' => $phone]);
                return true;
            }

            Log::error('Erreur envoi WhatsApp', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Exception WhatsApp Service', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Envoie un rappel de paiement via WhatsApp
     */
    public function sendPaymentReminder(string $phone, array $data): bool
    {
        $message = "🔔 *Rappel de Paiement*\n\n";
        $message .= "Bonjour,\n\n";
        $message .= "Votre loyer de *{$data['amount']} FCFA* est dû le *{$data['due_date']}*.\n";
        $message .= "Contrat: *{$data['contract_number']}*\n\n";
        $message .= "Merci de régulariser votre situation.\n\n";
        $message .= "_Caron - Gestion Immobilière_";
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Envoie une confirmation de paiement via WhatsApp
     */
    public function sendPaymentConfirmation(string $phone, array $data): bool
    {
        $message = "✅ *Paiement Confirmé*\n\n";
        $message .= "Votre paiement de *{$data['amount']} FCFA* a été reçu avec succès.\n\n";
        $message .= "Référence: *{$data['reference']}*\n";
        $message .= "Date: *{$data['date']}*\n\n";
        $message .= "Merci pour votre confiance !\n\n";
        $message .= "_Caron - Gestion Immobilière_";
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Formate le numéro de téléphone
     */
    protected function formatPhone(string $phone): string
    {
        // Enlever les espaces et caractères spéciaux
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ajouter l'indicatif si nécessaire
        if (!str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '0')) {
                $phone = '225' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '225')) {
                $phone = '225' . $phone;
            }
        } else {
            $phone = substr($phone, 1); // Enlever le +
        }
        
        return $phone;
    }
}

