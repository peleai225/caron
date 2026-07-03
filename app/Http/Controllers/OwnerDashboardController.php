<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Owner;
use App\Models\PaymentSchedule;
use App\Models\Expense;
use App\Models\EtatDesLieux;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // Trouver le propriétaire lié à l'utilisateur via l'email
        $owner = Owner::where('email', auth()->user()->email)->first();
        
        if (!$owner) {
            return view('owner.dashboard', [
                'owner' => null,
                'properties' => collect(),
                'activeContracts' => collect(),
                'recentPayments' => collect(),
                'upcomingPayments' => collect(),
                'pendingPayments' => collect(),
                'overduePayments' => collect(),
                'expenses' => collect(),
                'etatDesLieux' => collect(),
                'stats' => [
                    'total_properties' => 0,
                    'active_contracts' => 0,
                    'monthly_revenue' => 0,
                    'total_revenue' => 0,
                    'pending_payments' => 0,
                    'overdue_payments' => 0,
                ]
            ])->with('warning', 'Aucun compte propriétaire trouvé pour votre email.');
        }

        $properties = Property::where('owner_id', $owner->id)
            ->with(['contracts', 'contracts.tenant'])
            ->get();

        $activeContracts = Contract::whereHas('property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->where('status', 'active')
        ->with(['property', 'tenant', 'payments'])
        ->get();

        $monthlyPayments = Payment::whereHas('contract.property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->where('period', now()->format('Y-m'))
        ->where('status', 'completed')
        ->get();
        $monthlyRevenue = $monthlyPayments->sum(fn ($p) => (float) $p->amount + (float) ($p->charges_amount ?? 0));

        $totalPayments = Payment::whereHas('contract.property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->where('status', 'completed')
        ->get();
        $totalRevenue = $totalPayments->sum(fn ($p) => (float) $p->amount + (float) ($p->charges_amount ?? 0));

        $pendingPayments = Payment::whereHas('contract.property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->where('status', 'pending')
        ->with(['contract.property', 'contract.tenant'])
        ->latest('payment_date')
        ->limit(10)
        ->get();

        $overduePayments = PaymentSchedule::whereHas('contract.property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->overdue()
        ->with(['contract.property', 'contract.tenant'])
        ->get();

        $recentPayments = Payment::whereHas('contract.property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->where('status', 'completed')
        ->with(['contract.property', 'contract.tenant'])
        ->latest('payment_date')
        ->limit(10)
        ->get();

        $upcomingPayments = PaymentSchedule::whereHas('contract.property', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->where('status', 'pending')
        ->where('due_date', '>=', now())
        ->where('due_date', '<=', now()->addDays(30))
        ->with(['contract.property', 'contract.tenant'])
        ->orderBy('due_date')
        ->limit(10)
        ->get();

        $propertyIds = $properties->pluck('id');
        $expenses = Expense::whereIn('property_id', $propertyIds)
            ->with('property')
            ->latest('expense_date')
            ->limit(15)
            ->get();

        $etatDesLieux = EtatDesLieux::whereIn('property_id', $propertyIds)
            ->with(['property', 'contract.tenant'])
            ->latest('date')
            ->limit(15)
            ->get();

        $stats = [
            'total_properties' => $properties->count(),
            'active_contracts' => $activeContracts->count(),
            'monthly_revenue' => $monthlyRevenue,
            'total_revenue' => $totalRevenue,
            'pending_payments' => $pendingPayments->sum(fn ($p) => (float) $p->amount + (float) ($p->charges_amount ?? 0)),
            'overdue_payments' => $overduePayments->sum('amount'),
        ];

        return view('owner.dashboard', compact(
            'owner',
            'properties',
            'activeContracts',
            'recentPayments',
            'upcomingPayments',
            'pendingPayments',
            'overduePayments',
            'expenses',
            'etatDesLieux',
            'stats'
        ));
    }
}
