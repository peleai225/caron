<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Expense::with(['property'])
            ->when($agencyId, fn ($q) => $q->where('agency_id', $agencyId));

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        $expenses = $query->latest('expense_date')->paginate(15);
        $properties = Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->get();

        $stats = [
            'total_month' => Expense::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'total_year' => Expense::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'count_month' => Expense::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->count(),
        ];

        return view('expenses.index', compact('expenses', 'properties', 'stats'));
    }

    public function create()
    {
        $agencyId = $this->requireAgencyId();
        $properties = Property::where('agency_id', $agencyId)->get();
        
        return view('expenses.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'type' => 'required|in:maintenance,tax,insurance,utilities,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['agency_id'] = $this->requireAgencyId();

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $expense = Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    public function show(Expense $expense)
    {
        $this->authorizeAgency($expense->agency_id);
        $expense->load(['property', 'agency']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $agencyId = $this->requireAgencyId();
        abort_if($expense->agency_id !== $agencyId, 403);

        $properties = Property::where('agency_id', $agencyId)->get();

        return view('expenses.edit', compact('expense', 'properties'));
    }

    public function update(Request $request, Expense $expense)
    {
        abort_if($expense->agency_id !== $this->requireAgencyId(), 403);
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'type' => 'required|in:maintenance,tax,insurance,utilities,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeAgency($expense->agency_id);

        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }
}

