<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Property::with(['owner', 'images', 'contracts'])
            ->when($agencyId, fn ($q) => $q->where('agency_id', $agencyId));

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('address', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%')
                  ->orWhere('designation', 'like', '%' . $search . '%')
                  ->orWhere('neighborhood', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        $properties = $query->paginate(12);
        $owners = Owner::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->orderBy('name')->get();

        return view('properties.index', compact('properties', 'owners'));
    }

    public function create()
    {
        $agencyId = $this->requireAgencyId();
        
        $owners = Owner::where('agency_id', $agencyId)
            ->where('is_active', true)
            ->get();

        $buildings = Property::where('agency_id', $agencyId)
            ->where('type', 'immeuble')
            ->whereNull('parent_id')
            ->orderBy('address')
            ->get();

        return view('properties.create', compact('owners', 'buildings'));
    }

    public function store(Request $request)
    {
        $rules = [
            'owner_id' => 'required_without:parent_id|nullable|exists:owners,id',
            'parent_id' => 'nullable|exists:properties,id',
            'type' => 'required|in:maison,immeuble,boutique,terrain',
            'unit_type' => 'nullable|string|in:' . implode(',', array_keys(Property::unitTypes())),
            'status' => 'required|in:libre,occupe,maintenance',
            'address' => 'required_without:parent_id|nullable|string|max:255',
            'designation' => 'required_unless:parent_id,null|nullable|string|max:100',
            'city' => 'required_without:parent_id|nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'surface' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'monthly_rent' => 'nullable|numeric|min:0',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ];
        $validated = $request->validate($rules);

        $validated['agency_id'] = $this->requireAgencyId();
        if (!empty($validated['parent_id'])) {
            $parent = Property::find($validated['parent_id']);
            $validated['address'] = $parent->address;
            $validated['city'] = $parent->city;
            $validated['neighborhood'] = $parent->neighborhood;
            $validated['owner_id'] = $validated['owner_id'] ?? $parent->owner_id;
        } else {
            $validated['parent_id'] = null;
        }

        $property = Property::create($validated);

        // Upload des photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('properties/' . $property->id, 'public');
                $property->images()->create([
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('properties.index')
            ->with('success', 'Bien immobilier créé avec succès.');
    }

    public function show(Property $property)
    {
        $agencyId = $this->requireAgencyId();
        
        $this->authorizeAgency($property->agency_id);
        $property->load(['owner', 'parent', 'units', 'images', 'contracts.tenant', 'expenses']);
        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $agencyId = $this->requireAgencyId();
        $this->authorizeAgency($property->agency_id);
        
        $owners = Owner::where('agency_id', $agencyId)
            ->where('is_active', true)
            ->get();

        $buildings = Property::where('agency_id', $agencyId)
            ->where('type', 'immeuble')
            ->whereNull('parent_id')
            ->where('id', '!=', $property->id)
            ->orderBy('address')
            ->get();

        return view('properties.edit', compact('property', 'owners', 'buildings'));
    }

    public function update(Request $request, Property $property)
    {
        $agencyId = $this->requireAgencyId();
        $this->authorizeAgency($property->agency_id);
        
        $validated = $request->validate([
            'owner_id' => 'nullable|exists:owners,id',
            'parent_id' => 'nullable|exists:properties,id',
            'type' => 'required|in:maison,immeuble,boutique,terrain',
            'unit_type' => 'nullable|string|in:' . implode(',', array_keys(Property::unitTypes())),
            'status' => 'required|in:libre,occupe,maintenance',
            'address' => 'required_without:parent_id|nullable|string|max:255',
            'designation' => 'required_unless:parent_id,null|nullable|string|max:100',
            'city' => 'required_without:parent_id|nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'surface' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'monthly_rent' => 'nullable|numeric|min:0',
        ]);

        if (!empty($validated['parent_id'])) {
            $parent = Property::find($validated['parent_id']);
            $validated['address'] = $parent->address;
            $validated['city'] = $parent->city;
            $validated['neighborhood'] = $parent->neighborhood;
            $validated['owner_id'] = $validated['owner_id'] ?? $parent->owner_id;
        } else {
            $validated['parent_id'] = null;
        }

        $property->update($validated);

        return redirect()->route('properties.index')
            ->with('success', 'Bien immobilier mis à jour avec succès.');
    }

    public function destroy(Property $property)
    {
        $this->authorizeAgency($property->agency_id);
        
        // Supprimer les images
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $property->images()->delete();

        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Bien immobilier supprimé avec succès.');
    }
}
