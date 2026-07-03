<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Contract;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Invoice::with(['contract.tenant', 'contract.property'])
            ->whereHas('contract', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('issue_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('issue_date', '<=', $request->end_date);
        }

        $invoices = $query->latest('issue_date')->paginate(15);

        // Statistiques
        $stats = [
            'total_pending' => Invoice::whereHas('contract', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            })
            ->where('status', 'pending')
            ->sum('amount'),
            'total_paid' => Invoice::whereHas('contract', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            })
            ->where('status', 'paid')
            ->sum('amount'),
            'total_overdue' => Invoice::whereHas('contract', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            })
            ->where('status', 'overdue')
            ->sum('amount'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $agencyId = $this->requireAgencyId();
        $contracts = Contract::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->with(['tenant', 'property'])
            ->get();

        return view('invoices.create', compact('contracts'));
    }

    public function store(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'invoice_type' => 'required|in:loyer,commission,charges,other',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'description' => 'nullable|string',
        ]);

        $contract = Contract::where('agency_id', $agencyId)->findOrFail($validated['contract_id']);

        $invoiceNumber = 'FAC-' . now()->format('Y') . '-' . str_pad(
            Invoice::whereYear('created_at', now()->year)->count() + 1, 5, '0', STR_PAD_LEFT
        );

        $invoice = Invoice::create([
            'contract_id' => $validated['contract_id'],
            'invoice_number' => $invoiceNumber,
            'invoice_type' => $validated['invoice_type'],
            'amount' => $validated['amount'],
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? '',
            'status' => 'pending',
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Facture créée avec succès.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['contract.tenant', 'contract.property', 'contract.owner']);
        return view('invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $invoice->load(['contract.tenant', 'contract.property', 'contract.owner']);
        
        if (!$invoice->pdf_path || !file_exists(storage_path('app/public/invoices/' . $invoice->pdf_path))) {
            $this->generateInvoicePDF($invoice);
        }

        $path = storage_path('app/public/invoices/' . $invoice->pdf_path);
        return response()->download($path);
    }

    private function generateInvoicePDF(Invoice $invoice): void
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $filename = 'invoice_' . $invoice->invoice_number . '.pdf';
        $path = storage_path('app/public/invoices/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        $invoice->update(['pdf_path' => $filename]);
    }
}

