<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Owner::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId));

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $owners = $query->with('properties')->paginate(15);

        return view('owners.index', compact('owners'));
    }

    public function create()
    {
        return view('owners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'identification_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['agency_id'] = $this->requireAgencyId();

        Owner::create($validated);

        return redirect()->route('owners.index')
            ->with('success', 'Propriétaire créé avec succès.');
    }

    public function show(Owner $owner)
    {
        $this->authorizeAgency($owner->agency_id);
        $owner->load(['properties', 'contracts.tenant', 'contracts.property']);
        return view('owners.show', compact('owner'));
    }

    public function edit(Owner $owner)
    {
        $this->authorizeAgency($owner->agency_id);
        return view('owners.edit', compact('owner'));
    }

    public function update(Request $request, Owner $owner)
    {
        $this->authorizeAgency($owner->agency_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'identification_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $owner->update($validated);

        return redirect()->route('owners.index')
            ->with('success', 'Propriétaire mis à jour avec succès.');
    }

    public function destroy(Owner $owner)
    {
        $this->authorizeAgency($owner->agency_id);

        $owner->delete();
        return redirect()->route('owners.index')
            ->with('success', 'Propriétaire supprimé avec succès.');
    }
}
