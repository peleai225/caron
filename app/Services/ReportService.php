<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Property;
use App\Models\PaymentSchedule;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Génère un rapport financier
     * @param int|null $ownerId Si fourni (propriétaire), filtre par les biens de ce propriétaire uniquement
     */
    public function generateFinancialReport(?int $agencyId, Carbon $startDate, Carbon $endDate, ?int $ownerId = null): array
    {
        if (!$agencyId && !$ownerId) {
            return $this->emptyReport($startDate, $endDate);
        }

        $baseContract = function ($q) use ($agencyId, $ownerId) {
            if ($agencyId) {
                $q->where('agency_id', $agencyId);
            }
            if ($ownerId) {
                $q->whereHas('property', fn ($pq) => $pq->where('owner_id', $ownerId));
            }
        };

        $payments = Payment::whereHas('contract', $baseContract)
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalRevenue = $payments->sum(fn ($p) => (float) $p->amount + (float) ($p->charges_amount ?? 0));
        $totalPenalties = $payments->sum('penalty_amount');
        $totalPayments = $payments->count();

        $overdueAmount = PaymentSchedule::whereHas('contract', $baseContract)
            ->overdue()
            ->sum('amount');

        $expenseQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);
        if ($ownerId) {
            $expenseQuery->whereHas('property', fn ($q) => $q->where('owner_id', $ownerId));
        } else {
            $expenseQuery->where('agency_id', $agencyId);
        }
        $totalExpenses = $expenseQuery->sum('amount');

        $completedPayments = Payment::whereHas('contract', $baseContract)
            ->where('status', 'completed')
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->count();

        $pendingPayments = Payment::whereHas('contract', $baseContract)
            ->where('status', 'pending')
            ->count();

        $overduePaymentsCount = PaymentSchedule::whereHas('contract', $baseContract)
            ->overdue()
            ->count();

        $propertyQuery = $ownerId
            ? Property::where('owner_id', $ownerId)
            : Property::where('agency_id', $agencyId);
        $properties = $propertyQuery->get();
        $occupied = $properties->where('status', 'occupe')->count();
        $vacant = $properties->where('status', 'libre')->count();
        $maintenance = $properties->where('status', 'maintenance')->count();

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'total_revenue' => $totalRevenue,
            'total_penalties' => $totalPenalties,
            'total_payments' => $totalPayments,
            'overdue_amount' => $overdueAmount,
            'total_overdue' => $overdueAmount,
            'total_expenses' => $totalExpenses,
            'completed_payments' => $completedPayments,
            'pending_payments' => $pendingPayments,
            'overdue_payments' => $overduePaymentsCount,
            'occupied_properties' => $occupied,
            'vacant_properties' => $vacant,
            'maintenance_properties' => $maintenance,
            'recovery_rate' => $this->calculateRecoveryRate($agencyId, $startDate, $endDate, $ownerId),
            'by_property' => $this->getRevenueByProperty($agencyId, $startDate, $endDate, $ownerId),
            'by_payment_method' => $this->getRevenueByPaymentMethod($agencyId, $startDate, $endDate, $ownerId),
        ];
    }

    private function emptyReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'period' => ['start' => $startDate->format('Y-m-d'), 'end' => $endDate->format('Y-m-d')],
            'total_revenue' => 0,
            'total_penalties' => 0,
            'total_payments' => 0,
            'overdue_amount' => 0,
            'total_overdue' => 0,
            'total_expenses' => 0,
            'completed_payments' => 0,
            'pending_payments' => 0,
            'overdue_payments' => 0,
            'occupied_properties' => 0,
            'vacant_properties' => 0,
            'maintenance_properties' => 0,
            'recovery_rate' => 0,
            'by_property' => [],
            'by_payment_method' => [],
        ];
    }

    /**
     * Calcule le taux de recouvrement
     */
    private function calculateRecoveryRate(?int $agencyId, Carbon $startDate, Carbon $endDate, ?int $ownerId = null): float
    {
        $contractFilter = function ($query) use ($agencyId, $ownerId) {
            if ($agencyId) $query->where('agency_id', $agencyId);
            if ($ownerId) $query->whereHas('property', fn ($pq) => $pq->where('owner_id', $ownerId));
        };
        $expected = PaymentSchedule::whereHas('contract', $contractFilter)
        ->whereBetween('due_date', [$startDate, $endDate])
        ->sum('amount');

        $received = Payment::whereHas('contract', $contractFilter)
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->where('status', 'completed')
        ->sum('amount');

        if ($expected == 0) {
            return 0;
        }

        return ($received / $expected) * 100;
    }

    /**
     * Revenus par bien
     */
    private function getRevenueByProperty(?int $agencyId, Carbon $startDate, Carbon $endDate, ?int $ownerId = null): array
    {
        $query = Payment::join('contracts', 'payments.contract_id', '=', 'contracts.id')
            ->whereNotNull('payments.payment_date')
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->where('payments.status', 'completed');

        if ($agencyId) {
            $query->where('contracts.agency_id', $agencyId);
        }
        if ($ownerId) {
            $query->join('properties', 'contracts.property_id', '=', 'properties.id')
                  ->where('properties.owner_id', $ownerId);
        }

        return $query
            ->select('contracts.property_id', DB::raw('SUM(payments.amount + COALESCE(payments.charges_amount, 0)) as revenue'))
            ->groupBy('contracts.property_id')
            ->get()
            ->map(function ($item) {
                $property = Property::find($item->property_id);
                return [
                    'property_id' => $item->property_id,
                    'property_address' => $property ? $property->address : 'N/A',
                    'revenue' => $item->revenue,
                ];
            })
            ->toArray();
    }

    /**
     * Revenus par méthode de paiement
     */
    private function getRevenueByPaymentMethod(?int $agencyId, Carbon $startDate, Carbon $endDate, ?int $ownerId = null): array
    {
        $contractFilter = function ($query) use ($agencyId, $ownerId) {
            if ($agencyId) $query->where('agency_id', $agencyId);
            if ($ownerId) $query->whereHas('property', fn ($pq) => $pq->where('owner_id', $ownerId));
        };
        return Payment::whereHas('contract', $contractFilter)
        ->whereNotNull('payment_date')
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->where('status', 'completed')
        ->select('payment_method', DB::raw('SUM(amount + COALESCE(charges_amount, 0)) as revenue'), DB::raw('COUNT(*) as count'))
        ->groupBy('payment_method')
        ->get()
        ->toArray();
    }

    /**
     * Export vers Excel avec PhpSpreadsheet
     */
    public function exportToExcel(array $data, string $filename): string
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-tête
            $sheet->setCellValue('A1', 'Rapport Financier');
            $sheet->mergeCells('A1:D1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

            // Informations générales
            $row = 3;
            $sheet->setCellValue('A' . $row, 'Période:');
            $sheet->setCellValue('B' . $row, $data['period']['start'] . ' - ' . $data['period']['end']);
            $row++;
            $sheet->setCellValue('A' . $row, 'Revenus totaux:');
            $sheet->setCellValue('B' . $row, number_format($data['total_revenue'], 0, ',', ' ') . ' FCFA');
            $row++;
            $sheet->setCellValue('A' . $row, 'Pénalités:');
            $sheet->setCellValue('B' . $row, number_format($data['total_penalties'], 0, ',', ' ') . ' FCFA');
            $row++;
            $sheet->setCellValue('A' . $row, 'Nombre de paiements:');
            $sheet->setCellValue('B' . $row, $data['total_payments']);
            $row++;
            $sheet->setCellValue('A' . $row, 'Dépenses:');
            $sheet->setCellValue('B' . $row, number_format($data['total_expenses'] ?? 0, 0, ',', ' ') . ' FCFA');
            $row++;
            $sheet->setCellValue('A' . $row, 'Montant impayé:');
            $sheet->setCellValue('B' . $row, number_format($data['overdue_amount'] ?? 0, 0, ',', ' ') . ' FCFA');
            $row++;
            $sheet->setCellValue('A' . $row, 'Taux de recouvrement:');
            $sheet->setCellValue('B' . $row, number_format($data['recovery_rate'] ?? 0, 2) . '%');

            // Revenus par bien
            $row += 2;
            $sheet->setCellValue('A' . $row, 'Revenus par bien');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            $sheet->setCellValue('A' . $row, 'Bien');
            $sheet->setCellValue('B' . $row, 'Revenus');
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row++;
            
            foreach ($data['by_property'] as $property) {
                $sheet->setCellValue('A' . $row, $property['property_address']);
                $sheet->setCellValue('B' . $row, number_format($property['revenue'], 0, ',', ' ') . ' FCFA');
                $row++;
            }

            // Revenus par méthode de paiement
            $row += 1;
            $sheet->setCellValue('A' . $row, 'Revenus par méthode de paiement');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            $sheet->setCellValue('A' . $row, 'Méthode');
            $sheet->setCellValue('B' . $row, 'Revenus');
            $sheet->setCellValue('C' . $row, 'Nombre');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;
            
            foreach ($data['by_payment_method'] as $method) {
                $methodName = $method['payment_method'] ?? 'non_specifie';
                $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $methodName)));
                $sheet->setCellValue('B' . $row, number_format($method['revenue'], 0, ',', ' ') . ' FCFA');
                $sheet->setCellValue('C' . $row, $method['count']);
                $row++;
            }

            // Ajuster la largeur des colonnes
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(15);

            // Sauvegarder
            $path = storage_path('app/public/reports/' . $filename);
            $directory = dirname($path);
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($path);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('Erreur export Excel: ' . $e->getMessage());
            
            // Fallback vers CSV
            return $this->exportToCSV($data, str_replace('.xlsx', '.csv', $filename));
        }
    }

    /**
     * Export vers CSV (fallback)
     */
    protected function exportToCSV(array $data, string $filename): string
    {
        $csvContent = "Rapport Financier\n";
        $csvContent .= "Période," . $data['period']['start'] . " - " . $data['period']['end'] . "\n";
        $csvContent .= "Revenus totaux," . number_format($data['total_revenue'] ?? 0, 0, ',', ' ') . " FCFA\n";
        $csvContent .= "Dépenses," . number_format($data['total_expenses'] ?? 0, 0, ',', ' ') . " FCFA\n";
        $csvContent .= "Pénalités," . number_format($data['total_penalties'] ?? 0, 0, ',', ' ') . " FCFA\n";
        $csvContent .= "Nombre de paiements," . ($data['total_payments'] ?? 0) . "\n";
        $csvContent .= "Montant impayé," . number_format($data['overdue_amount'] ?? 0, 0, ',', ' ') . " FCFA\n";
        $csvContent .= "Taux de recouvrement," . number_format($data['recovery_rate'], 2) . "%\n\n";
        
        $csvContent .= "Revenus par bien\n";
        $csvContent .= "Bien,Revenus\n";
        foreach ($data['by_property'] as $property) {
            $csvContent .= $property['property_address'] . "," . number_format($property['revenue'], 0, ',', ' ') . " FCFA\n";
        }
        
        $csvContent .= "\nRevenus par méthode de paiement\n";
        $csvContent .= "Méthode,Revenus,Nombre\n";
        foreach ($data['by_payment_method'] as $method) {
            $methodName = $method['payment_method'] ?? 'non_specifie';
            $csvContent .= ucfirst(str_replace('_', ' ', $methodName)) . "," . number_format($method['revenue'], 0, ',', ' ') . " FCFA," . ($method['count'] ?? 0) . "\n";
        }

        $path = storage_path('app/public/reports/' . $filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $csvContent);

        return $filename;
    }

    /**
     * Export vers PDF
     */
    public function exportToPDF(array $data, string $filename): string
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', ['data' => $data])
            ->setPaper('a4', 'landscape');
        $path = storage_path('app/public/reports/' . $filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        $pdf->save($path);
        
        return $filename;
    }
}

