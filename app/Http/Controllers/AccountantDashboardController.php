<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Account;
use App\Models\Invoice;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountantDashboardController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $agencyId = $this->requireAgencyId();

        // Statistiques financières
        $totalRevenue = Payment::whereHas('contract', function($q) use ($agencyId) {
            $q->where('agency_id', $agencyId);
        })
        ->where('status', 'completed')
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->sum('amount');

        $totalExpenses = Expense::where('agency_id', $agencyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        $pendingPayments = Payment::whereHas('contract', function($q) use ($agencyId) {
            $q->where('agency_id', $agencyId);
        })
        ->where('status', 'pending')
        ->sum('amount');

        // Transactions via les comptes de l'agence
        $agencyAccountIds = Account::where('agency_id', $agencyId)->pluck('id');

        $totalTransactions = Transaction::whereIn('account_id', $agencyAccountIds)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();

        // Transactions récentes
        $recentTransactions = Transaction::whereIn('account_id', $agencyAccountIds)
            ->with(['account'])
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        // Dépenses par type (le champ correct est 'type', pas 'category')
        $expensesByCategory = Expense::where('agency_id', $agencyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('type as category', DB::raw('sum(amount) as total'))
            ->groupBy('type')
            ->get();

        // Comptes
        $accounts = Account::where('agency_id', $agencyId)
            ->with(['transactions' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }])
            ->get();

        // Factures via les contrats de l'agence
        $recentInvoices = Invoice::whereHas('contract', function($q) use ($agencyId) {
            $q->where('agency_id', $agencyId);
        })
        ->with(['contract.tenant', 'contract.property'])
        ->latest()
        ->limit(10)
        ->get();

        // Rapport financier
        $report = $this->reportService->generateFinancialReport(
            $agencyId,
            $startDate,
            $endDate
        );

        $totalInvoices = Invoice::whereHas('contract', function($q) use ($agencyId) {
            $q->where('agency_id', $agencyId);
        })->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,
            'pending_payments' => $pendingPayments,
            'total_transactions' => $totalTransactions,
            'total_accounts' => $accounts->count(),
            'total_invoices' => $totalInvoices,
        ];

        return view('accountant.dashboard', compact(
            'stats',
            'recentTransactions',
            'expensesByCategory',
            'accounts',
            'recentInvoices',
            'report',
            'startDate',
            'endDate'
        ));
    }
}
