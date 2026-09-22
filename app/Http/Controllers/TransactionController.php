<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Transaction::with(['account', 'payment'])
            ->when($agencyId, fn ($q) => $q->whereHas('account', fn ($aq) => $aq->where('agency_id', $agencyId)));

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->latest('transaction_date')->paginate(20);
        $accounts = Account::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->get();

        $baseQuery = fn () => Transaction::when($agencyId, fn ($q) => $q->whereHas('account', fn ($aq) => $aq->where('agency_id', $agencyId)));
        $stats = [
            'total_income'  => $baseQuery()->where('type', 'income')->where('status', 'completed')->sum('amount'),
            'total_expense' => $baseQuery()->where('type', 'expense')->where('status', 'completed')->sum('amount'),
        ];

        return view('transactions.index', compact('transactions', 'accounts', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['account', 'payment.contract']);
        return view('transactions.show', compact('transaction'));
    }
}

