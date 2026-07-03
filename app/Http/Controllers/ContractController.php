<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Owner;
use App\Models\ContractTemplate;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Contract::with(['tenant', 'property', 'owner'])
            ->when($agencyId, fn ($q) => $q->where('agency_id', $agencyId));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->latest()->paginate(15);

        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        $agencyId = $this->requireAgencyId();
        
        $tenants = Tenant::where('agency_id', $agencyId)
            ->where('status', 'actif')
            ->get();

        $properties = Property::where('agency_id', $agencyId)
            ->where('status', 'libre')
            ->get();

        $owners = Owner::where('agency_id', $agencyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $templates = ContractTemplate::where('agency_id', $agencyId)
            ->where(function ($q) {
                $q->where('is_active', true)->orWhere('is_default', true);
            })
            ->get();

        return view('contracts.create', compact('tenants', 'properties', 'owners', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'property_id' => 'required|exists:properties,id',
            'owner_id' => 'nullable|exists:owners,id',
            'type_contrat' => 'nullable|string|in:' . implode(',', array_keys(Contract::typesContrat())),
            'rent_amount' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_frequency' => 'required|in:monthly,quarterly,yearly',
            'payment_day' => 'required|integer|min:1|max:31',
            'template_id' => 'nullable|exists:contract_templates,id',
            'notes' => 'nullable|string',
        ]);

        $validated['agency_id'] = $this->requireAgencyId();
        $validated['status'] = 'draft';

        $contract = $this->contractService->createContract($validated);

        // Mettre à jour le statut du bien
        $property = Property::find($validated['property_id']);
        $property->update(['status' => 'occupe']);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat créé avec succès.');
    }

    public function show(Contract $contract)
    {
        $agencyId = $this->requireAgencyId();
        
        $this->authorizeAgency($contract->agency_id);
        $contract->load(['tenant', 'property', 'owner', 'payments', 'paymentSchedules']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $agencyId = $this->requireAgencyId();
        $this->authorizeAgency($contract->agency_id);

        $tenants = Tenant::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->get();
        $properties = Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->get();
        $owners = Owner::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->where('is_active', true)->orderBy('name')->get();
        $templates = ContractTemplate::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->get();

        return view('contracts.edit', compact('contract', 'tenants', 'properties', 'owners', 'templates'));
    }

    public function update(Request $request, Contract $contract)
    {
        $this->authorizeAgency($contract->agency_id);
        
        $validated = $request->validate([
            'owner_id' => 'nullable|exists:owners,id',
            'type_contrat' => 'nullable|string|in:' . implode(',', array_keys(Contract::typesContrat())),
            'rent_amount' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_frequency' => 'required|in:monthly,quarterly,yearly',
            'payment_day' => 'required|integer|min:1|max:31',
            'status' => 'required|in:draft,active,expired,terminated',
            'notes' => 'nullable|string',
        ]);

        $contract->update($validated);

        // Libérer le bien si le contrat est terminé/expiré et qu'il n'y a plus de contrat actif
        if (in_array($validated['status'], ['terminated', 'expired'])) {
            $property = $contract->property;
            if ($property && !$property->contracts()->whereIn('status', ['draft', 'active'])->exists()) {
                $property->update(['status' => 'libre']);
            }
        }

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(Contract $contract)
    {
        $this->authorizeAgency($contract->agency_id);

        $property = $contract->property;
        $contract->delete();

        // Remettre le bien à "libre" s'il n'a plus de contrat actif
        if ($property && !$property->contracts()->whereIn('status', ['draft', 'active'])->exists()) {
            $property->update(['status' => 'libre']);
        }

        return redirect()->route('contracts.index')
            ->with('success', 'Contrat supprimé avec succès.');
    }

    public function download(Contract $contract)
    {
        $this->authorizeAgency($contract->agency_id);
        
        if (!$contract->pdf_path) {
            $this->contractService->generateContractPDF($contract);
        }

        $path = storage_path('app/public/' . $contract->pdf_path);
        return response()->download($path);
    }

    public function sign(Contract $contract)
    {
        $this->authorizeAgency($contract->agency_id);
        
        $contract->update([
            'status' => 'active',
            'signed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Contrat signé avec succès.');
    }
}
