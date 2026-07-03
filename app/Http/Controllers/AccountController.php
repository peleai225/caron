<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $agencyId = $this->requireAgencyId();
        
        $accounts = Account::where('agency_id', $agencyId)
            ->with('transactions')
            ->latest()
            ->get();

        // Statistiques
        $stats = [
            'total_balance' => Account::where('agency_id', $agencyId)
                ->where('is_active', true)
                ->sum('balance'),
            'total_accounts' => Account::where('agency_id', $agencyId)->count(),
            'active_accounts' => Account::where('agency_id', $agencyId)
                ->where('is_active', true)
                ->count(),
        ];

        return view('accounts.index', compact('accounts', 'stats'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile_money',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255|required_if:type,bank',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['agency_id'] = $this->requireAgencyId();
        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Compte créé avec succès.');
    }

    public function show(Account $account)
    {
        abort_if($account->agency_id !== $this->getAgencyId(), 403);

        $account->load(['transactions.payment', 'agency']);
        $transactions = $account->transactions()->latest('transaction_date')->paginate(20);

        return view('accounts.show', compact('account', 'transactions'));
    }

    public function edit(Account $account)
    {
        abort_if($account->agency_id !== $this->getAgencyId(), 403);

        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        abort_if($account->agency_id !== $this->getAgencyId(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile_money',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255|required_if:type,bank',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Compte mis à jour avec succès.');
    }

    public function destroy(Account $account)
    {
        abort_if($account->agency_id !== $this->getAgencyId(), 403);

        if ($account->transactions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer un compte avec des transactions.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Compte supprimé avec succès.');
    }
}

