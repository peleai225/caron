<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Owner;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('proprietaire')) {
            return redirect()->route('owner.dashboard');
        }

        if ($user->hasRole('locataire')) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Les comptes locataires ont été désactivés.');
        }

        if ($user->hasRole('comptable')) {
            return redirect()->route('accountant.dashboard');
        }

        $agencyId = $user->agency_id;

        // Dashboard dédié super_admin — vue globale toutes agences
        if ($user->hasRole('super_admin') && !$agencyId) {
            return $this->superAdminDashboard();
        }

        if (!$agencyId) {
            $stats = [
                'total_owners' => 0,
                'total_properties' => 0,
                'active_tenants' => 0,
                'total_expenses' => 0,
                'available_properties' => 0,
                'monthly_revenue' => 0,
                'overdue_count' => 0,
                'overdue_amount' => 0,
                'agency_commissions' => 0,
            ];
            $recentPayments = collect();
            $expiringContracts = collect();
            $activeProperties = collect();
            $availableProperties = collect();

            return view('dashboard', compact('stats', 'recentPayments', 'expiringContracts', 'activeProperties', 'availableProperties'))
                ->with('warning', 'Vous n\'êtes pas encore associé à une agence. Contactez un administrateur.');
        }

        $cacheKey = 'dashboard_agency_' . $agencyId;
        $stats = Cache::remember($cacheKey . '_stats', 60, function () use ($agencyId) {
            return [
                'total_owners' => Owner::where('agency_id', $agencyId)->count(),
                'total_properties' => Property::where('agency_id', $agencyId)->count(),
                'active_tenants' => Tenant::where('agency_id', $agencyId)->where('status', 'actif')->count(),
                'total_expenses' => Expense::where('agency_id', $agencyId)
                    ->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year)
                    ->sum('amount'),
                'available_properties' => Property::where('agency_id', $agencyId)->where('status', 'libre')->count(),
                'monthly_revenue' => Payment::whereHas('contract', function ($q) use ($agencyId) {
                    $q->where('agency_id', $agencyId);
                })
                    ->where('period', now()->format('Y-m'))
                    ->where('status', 'completed')
                    ->sum('amount'),
                'overdue_count' => PaymentSchedule::whereHas('contract', function ($q) use ($agencyId) {
                    $q->where('agency_id', $agencyId);
                })->overdue()->count(),
                'overdue_amount' => PaymentSchedule::whereHas('contract', function ($q) use ($agencyId) {
                    $q->where('agency_id', $agencyId);
                })->overdue()->sum('amount'),
                'agency_commissions' => 0,
            ];
        });

        $recentPayments = Cache::remember($cacheKey . '_recent_payments', 60, function () use ($agencyId) {
            return Payment::whereHas('contract', function ($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            })
                ->with(['contract.tenant', 'contract.property'])
                ->latest('payment_date')
                ->limit(5)
                ->get();
        });

        $expiringContracts = Cache::remember($cacheKey . '_expiring_contracts', 60, function () use ($agencyId) {
            return Contract::where('agency_id', $agencyId)
                ->where('status', 'active')
                ->whereBetween('end_date', [now(), now()->addDays(30)])
                ->with(['tenant', 'property'])
                ->limit(5)
                ->get();
        });

        $activeProperties = Cache::remember($cacheKey . '_active_properties', 60, function () use ($agencyId) {
            return Property::where('agency_id', $agencyId)
                ->where('status', 'occupe')
                ->with('owner')
                ->limit(5)
                ->get();
        });

        $availableProperties = Cache::remember($cacheKey . '_available_properties', 60, function () use ($agencyId) {
            return Property::where('agency_id', $agencyId)
                ->where('status', 'libre')
                ->with('owner')
                ->limit(5)
                ->get();
        });

        $contracts = Contract::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->with(['tenant', 'property'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('stats', 'recentPayments', 'expiringContracts', 'activeProperties', 'availableProperties', 'contracts'));
    }

    private function superAdminDashboard()
    {
        $agencies = Agency::withCount(['properties', 'tenants', 'contracts'])
            ->with('users')
            ->orderBy('name')
            ->get();

        $global = Cache::remember('super_admin_dashboard', 60, function () {
            return [
                'total_agencies'    => Agency::count(),
                'active_agencies'   => Agency::where('is_active', true)->count(),
                'total_properties'  => Property::count(),
                'total_tenants'     => Tenant::count(),
                'total_contracts'   => Contract::where('status', 'active')->count(),
                'monthly_revenue'   => Payment::where('period', now()->format('Y-m'))
                    ->where('status', 'completed')
                    ->sum('amount'),
                'overdue_count'     => PaymentSchedule::overdue()->count(),
                'overdue_amount'    => PaymentSchedule::overdue()->sum('amount'),
            ];
        });

        $recentPayments = Payment::with(['contract.tenant', 'contract.property', 'contract.agency'])
            ->latest('payment_date')
            ->limit(8)
            ->get();

        return view('dashboard-superadmin', compact('agencies', 'global', 'recentPayments'));
    }
}
