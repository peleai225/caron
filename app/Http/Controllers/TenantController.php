<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Tenant::with(['contracts.property'])
            ->when($agencyId, fn ($q) => $q->where('agency_id', $agencyId));

        // Filtres
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tenants = $query->paginate(15);

        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'cni_number' => 'nullable|string|max:50',
            'cni' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'contract' => 'nullable|file|mimes:pdf|max:5120',
            'notes' => 'nullable|string',
        ]);

        $validated['agency_id'] = $this->requireAgencyId();

        // Upload CNI
        if ($request->hasFile('cni')) {
            $validated['cni_path'] = $request->file('cni')->store('tenants/cni', 'public');
        }

        $tenant = Tenant::create($validated);

        // Upload contrat si fourni
        if ($request->hasFile('contract')) {
            $path = $request->file('contract')->store('tenants/contracts', 'public');
            $tenant->documents()->create([
                'type' => 'contrat',
                'name' => 'Contrat initial',
                'path' => $path,
            ]);
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Locataire créé avec succès.');
    }

    public function show(Tenant $tenant)
    {
        $agencyId = $this->requireAgencyId();
        
        $this->authorizeAgency($tenant->agency_id);
        $tenant->load(['contracts.property', 'documents']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $this->authorizeAgency($tenant->agency_id);
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $this->authorizeAgency($tenant->agency_id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'cni_number' => 'nullable|string|max:50',
            'status' => 'required|in:actif,en_retard,resilie',
            'notes' => 'nullable|string',
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.index')
            ->with('success', 'Locataire mis à jour avec succès.');
    }

    public function destroy(Tenant $tenant)
    {
        $this->authorizeAgency($tenant->agency_id);
        
        // Supprimer les documents
        foreach ($tenant->documents as $document) {
            Storage::disk('public')->delete($document->path);
        }
        $tenant->documents()->delete();

        if ($tenant->cni_path) {
            Storage::disk('public')->delete($tenant->cni_path);
        }

        $tenant->delete();

        return redirect()->route('tenants.index')
            ->with('success', 'Locataire supprimé avec succès.');
    }
}
