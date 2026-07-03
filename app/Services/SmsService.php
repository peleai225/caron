<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?string $apiKey;
    protected ?string $apiUrl;
    protected string $senderId;

    public function __construct()
    {
        $this->apiKey = config('services.sms.api_key') ?? env('SMS_API_KEY', '');
        $this->apiUrl = config('services.sms.api_url') ?? env('SMS_API_URL', '');
        $this->senderId = config('services.sms.sender_id') ?? env('SMS_SENDER_ID', 'CARON') ?? 'CARON';
    }

    /**
     * Envoie un SMS
     */
    public function sendSms(string $phone, string $message): bool
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            Log::warning('SMS Service non configuré');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/send', [
                'phone' => $this->formatPhone($phone),
                'message' => $message,
                'sender_id' => $this->senderId,
            ]);

            if ($response->successful()) {
                Log::info('SMS envoyé avec succès', ['phone' => $phone]);
                return true;
            }

            Log::error('Erreur envoi SMS', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Exception SMS Service', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Envoie un SMS de rappel de paiement
     */
    public function sendPaymentReminder(string $phone, array $data): bool
    {
        $message = "Bonjour, rappel: votre loyer de {$data['amount']} FCFA est dû le {$data['due_date']}. Contrat: {$data['contract_number']}";
        
        return $this->sendSms($phone, $message);
    }

    /**
     * Envoie un SMS de confirmation de paiement
     */
    public function sendPaymentConfirmation(string $phone, array $data): bool
    {
        $message = "Paiement confirmé: {$data['amount']} FCFA reçu. Réf: {$data['reference']}. Merci!";
        
        return $this->sendSms($phone, $message);
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
                $phone = '+225' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '225')) {
                $phone = '+225' . $phone;
            } else {
                $phone = '+' . $phone;
            }
        }
        
        return $phone;
    }
}

