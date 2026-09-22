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
        
        $tenants = Tenant::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'actif')
            ->orderBy('first_name')
            ->get();

        $properties = Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->whereIn('status', ['libre', 'maintenance'])
            ->get();

        $owners = Owner::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $templates = ContractTemplate::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
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

        // Le bien reste libre tant que le contrat est en brouillon

        // Invalider le cache dashboard
        $this->forgetDashboardCache($validated['agency_id']);

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

        // Gérer signed_at si on passe à active
        if ($validated['status'] === 'active' && !$contract->signed_at) {
            $validated['signed_at'] = now();
        }

        $contract->update($validated);

        $property = $contract->property;
        if ($property) {
            if ($validated['status'] === 'active') {
                $property->update(['status' => 'occupe']);
            } elseif (in_array($validated['status'], ['terminated', 'expired'])) {
                if (!$property->contracts()->whereIn('status', ['active'])->exists()) {
                    $property->update(['status' => 'libre']);
                }
            }
        }

        // Invalider le cache dashboard
        $this->forgetDashboardCache($contract->agency_id);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(Contract $contract)
    {
        $this->authorizeAgency($contract->agency_id);

        $agencyId = $contract->agency_id;
        $property = $contract->property;
        $contract->delete();

        // Remettre le bien à "libre" s'il n'a plus de contrat actif
        if ($property && !$property->contracts()->whereIn('status', ['draft', 'active'])->exists()) {
            $property->update(['status' => 'libre']);
        }

        // Invalider le cache dashboard
        $this->forgetDashboardCache($agencyId);

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

    public function searchProperties(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        $q = trim($request->get('q', ''));

        $properties = Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->whereIn('status', ['libre', 'maintenance'])
            ->where(function ($query) use ($q) {
                $query->where('address', 'like', "%{$q}%")
                    ->orWhere('designation', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('neighborhood', 'like', "%{$q}%")
                    ->orWhereHas('parent', function ($pq) use ($q) {
                        $pq->where('address', 'like', "%{$q}%")
                           ->orWhere('designation', 'like', "%{$q}%")
                           ->orWhere('neighborhood', 'like', "%{$q}%");
                    });
            })
            ->with('parent')
            ->limit(20)
            ->get();

        return response()->json($properties->map(fn ($p) => [
            'id'           => $p->id,
            'label'        => $p->full_address . ($p->designation ? ' · ' . $p->designation : '') . ' — ' . $p->city,
            'monthly_rent' => (int) $p->monthly_rent,
            'type'         => $p->type,
        ]));
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
