<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use App\Models\Property;
use App\Models\Contract;
use Illuminate\Http\Request;

class EtatDesLieuxController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $query = EtatDesLieux::with(['property', 'contract.tenant'])
            ->whereHas('property', fn ($q) => $q->where('agency_id', $agencyId));

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $etatDesLieux = $query->latest('date')->paginate(15);
        $properties = Property::where('agency_id', $agencyId)->orderBy('address')->get();
        $contracts = Contract::whereHas('property', fn ($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'active')
            ->with(['property', 'tenant'])
            ->get();

        return view('etat-des-lieux.index', compact('etatDesLieux', 'properties', 'contracts'));
    }

    public function create(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $propertyId = $request->get('property_id');
        $contractId = $request->get('contract_id');

        $property = $propertyId ? Property::where('agency_id', $agencyId)->findOrFail($propertyId) : null;
        $contract = $contractId ? Contract::whereHas('property', fn ($q) => $q->where('agency_id', $agencyId))->findOrFail($contractId) : null;

        $properties = Property::where('agency_id', $agencyId)->orderBy('address')->get();
        $contracts = $contract ? collect([$contract]) : Contract::whereHas('property', fn ($q) => $q->where('agency_id', $agencyId))->where('status', 'active')->with(['property', 'tenant'])->get();

        return view('etat-des-lieux.create', compact('properties', 'contracts', 'property', 'contract'));
    }

    public function store(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'type' => 'required|in:entree,sortie',
            'date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        $property = Property::where('agency_id', $agencyId)->findOrFail($validated['property_id']);
        $validated['property_id'] = $property->id;

        EtatDesLieux::create($validated);

        return redirect()->route('etat-des-lieux.index')
            ->with('success', 'État des lieux enregistré.');
    }

    public function show(EtatDesLieux $etatDesLieux)
    {
        $this->authorizeAgency($etatDesLieux->property?->agency_id);
        $etatDesLieux->load(['property', 'contract.tenant']);

        return view('etat-des-lieux.show', compact('etatDesLieux'));
    }

    public function edit(EtatDesLieux $etatDesLieux)
    {
        $agencyId = $this->requireAgencyId();
        $this->authorizeAgency($etatDesLieux->property?->agency_id);

        $properties = Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->orderBy('address')->get();
        $contracts = Contract::when($agencyId, fn ($q) => $q->whereHas('property', fn ($pq) => $pq->where('agency_id', $agencyId)))
            ->where('status', 'active')
            ->with(['property', 'tenant'])
            ->get();

        return view('etat-des-lieux.edit', compact('etatDesLieux', 'properties', 'contracts'));
    }

    public function update(Request $request, EtatDesLieux $etatDesLieux)
    {
        $agencyId = $this->requireAgencyId();
        $this->authorizeAgency($etatDesLieux->property?->agency_id);

        $validated = $request->validate([
            'property_id'  => 'required|exists:properties,id',
            'contract_id'  => 'nullable|exists:contracts,id',
            'type'         => 'required|in:entree,sortie',
            'date'         => 'required|date',
            'observations' => 'nullable|string',
        ]);

        Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->findOrFail($validated['property_id']);

        $etatDesLieux->update($validated);

        return redirect()->route('etat-des-lieux.index')
            ->with('success', 'État des lieux mis à jour.');
    }

    public function destroy(EtatDesLieux $etatDesLieux)
    {
        $this->authorizeAgency($etatDesLieux->property?->agency_id);
        $etatDesLieux->delete();

        return redirect()->route('etat-des-lieux.index')
            ->with('success', 'État des lieux supprimé.');
    }
}
