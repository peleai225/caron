<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractService
{
    /**
     * Génère un numéro de contrat unique
     */
    public function generateContractNumber(int $agencyId): string
    {
        $year = now()->year;
        $count = Contract::where('agency_id', $agencyId)
            ->whereYear('created_at', $year)
            ->count() + 1;
        
        return 'CTR-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Crée un contrat avec génération automatique
     */
    public function createContract(array $data): Contract
    {
        $data['contract_number'] = $this->generateContractNumber($data['agency_id']);
        
        $contract = Contract::create($data);
        
        // Générer les échéances de paiement
        app(PaymentService::class)->generatePaymentSchedules($contract);
        
        // Générer le PDF du contrat
        $this->generateContractPDF($contract);
        
        return $contract;
    }

    /**
     * Génère le PDF du contrat
     */
    public function generateContractPDF(Contract $contract): string
    {
        $contract->load(['tenant', 'property', 'owner', 'template']);
        
        $template = $contract->template ?? ContractTemplate::where('agency_id', $contract->agency_id)
            ->where('is_default', true)
            ->first();

        $content = $template && $template->content
            ? $this->replaceTemplateVariables($template->content, $contract)
            : '';
        
        $pdf = Pdf::loadView('contracts.pdf', [
            'contract' => $contract,
            'content' => $content,
        ]);
        
        $filename = 'contracts/' . $contract->contract_number . '.pdf';
        $path = storage_path('app/public/' . $filename);
        
        // Créer le dossier si nécessaire
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        $pdf->save($path);
        
        $contract->update(['pdf_path' => $filename]);
        
        return $filename;
    }

    /**
     * Remplace les variables du template
     */
    private function replaceTemplateVariables(string $content, Contract $contract): string
    {
        $variables = [
            '{{CONTRACT_NUMBER}}' => $contract->contract_number,
            '{{TENANT_NAME}}' => $contract->tenant->full_name,
            '{{TENANT_PHONE}}' => $contract->tenant->phone,
            '{{PROPERTY_ADDRESS}}' => $contract->property->address,
            '{{RENT_AMOUNT}}' => number_format($contract->rent_amount, 0, ',', ' ') . ' FCFA',
            '{{DEPOSIT}}' => number_format($contract->deposit ?? 0, 0, ',', ' ') . ' FCFA',
            '{{START_DATE}}' => $contract->start_date->format('d/m/Y'),
            '{{END_DATE}}' => $contract->end_date->format('d/m/Y'),
            '{{PAYMENT_DAY}}' => $contract->payment_day,
        ];
        
        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    /**
     * Vérifie les contrats expirant bientôt
     */
    public function checkExpiringContracts(int $days = 30): array
    {
        return Contract::expiring($days)->get()->toArray();
    }
}

