<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            'type' => 'required|in:' . implode(',', array_keys(\App\Models\Expense::expenseTypes())),
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

        // Invalider le cache dashboard
        $this->forgetDashboardCache($validated['agency_id']);

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
            'type' => 'required|in:' . implode(',', array_keys(\App\Models\Expense::expenseTypes())),
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

        // Invalider le cache dashboard
        $this->forgetDashboardCache($expense->agency_id);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    public function exportExcel(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $query = Expense::with(['property'])
            ->where('agency_id', $agencyId);

        $this->applyExpenseFilters($query, $request);

        $expenses = $query->latest('expense_date')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $fcfaFormat = '#,##0" FCFA"';

        $sheet->setCellValue('A1', 'Liste des Dépenses');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $period = ($request->start_date ?? '...') . ' - ' . ($request->end_date ?? '...');
            $sheet->setCellValue('A2', 'Période : ' . $period);
            $sheet->mergeCells('A2:F2');
        }

        $row = 4;
        foreach (['Date', 'Type', 'Bien', 'Description', 'Montant'] as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('DC2626');
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
        }
        $row++;

        $total = 0;
        foreach ($expenses as $expense) {
            $sheet->setCellValue('A' . $row, $expense->expense_date->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, \App\Models\Expense::expenseTypes()[$expense->type] ?? $expense->type);
            $sheet->setCellValue('C' . $row, $expense->property?->address ?? '—');
            $sheet->setCellValue('D' . $row, $expense->description);
            $sheet->setCellValue('E' . $row, (float) $expense->amount);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode($fcfaFormat);
            $total += (float) $expense->amount;
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'TOTAL');
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('E' . $row, $total);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode($fcfaFormat);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(18);

        $filename = 'depenses-' . now()->format('Y-m-d') . '.xlsx';
        $path = storage_path('app/public/reports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPDF(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $query = Expense::with(['property'])
            ->where('agency_id', $agencyId);

        $this->applyExpenseFilters($query, $request);

        $expenses = $query->latest('expense_date')->get();
        $total = $expenses->sum('amount');

        $filters = [
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'type'       => $request->type ? (\App\Models\Expense::expenseTypes()[$request->type] ?? $request->type) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('expenses.pdf', compact('expenses', 'total', 'filters'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('depenses-' . now()->format('Y-m-d') . '.pdf');
    }

    private function applyExpenseFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
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
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeAgency($expense->agency_id);

        $agencyId = $expense->agency_id;

        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        // Invalider le cache dashboard
        $this->forgetDashboardCache($agencyId);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }
}

