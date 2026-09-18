<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgencyController extends Controller
{
    /**
     * Affiche la liste des agences
     */
    public function index(Request $request)
    {
        $query = Agency::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $agencies = $query->withCount(['users', 'properties', 'tenants', 'contracts'])->latest()->paginate(20);

        return view('agencies.index', compact('agencies'));
    }

    /**
     * Affiche les détails d'une agence
     */
    public function show(Agency $agency)
    {
        $agency->load(['users', 'properties', 'tenants', 'contracts']);
        
        return view('agencies.show', compact('agency'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('agencies.create');
    }

    /**
     * Enregistre une nouvelle agence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agencies,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:2',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('agencies/logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $agency = Agency::create($validated);

        return redirect()->route('agencies.show', $agency)
            ->with('success', 'Agence créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Agency $agency)
    {
        return view('agencies.edit', compact('agency'));
    }

    /**
     * Met à jour une agence
     */
    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agencies,email,' . $agency->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:2',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo
            if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
                Storage::disk('public')->delete($agency->logo_path);
            }
            
            $validated['logo_path'] = $request->file('logo')->store('agencies/logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $agency->update($validated);

        return redirect()->route('agencies.show', $agency)
            ->with('success', 'Agence mise à jour avec succès.');
    }

    /**
     * Supprime une agence
     */
    public function destroy(Agency $agency)
    {
        // Vérifier les contrats actifs
        $activeContracts = $agency->contracts()->where('status', 'active')->count();
        if ($activeContracts > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette agence : ' . $activeContracts . ' contrat(s) actif(s) sont en cours.');
        }

        // Vérifier les locataires actifs
        $activeTenants = $agency->tenants()->where('status', 'actif')->count();
        if ($activeTenants > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette agence : ' . $activeTenants . ' locataire(s) actif(s) sont enregistrés.');
        }

        // Vérifier les biens immobiliers
        $propertiesCount = $agency->properties()->count();
        if ($propertiesCount > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette agence : ' . $propertiesCount . ' bien(s) immobilier(s) sont enregistrés. Veuillez d\'abord les supprimer.');
        }

        // Vérifier les propriétaires
        $ownersCount = $agency->owners()->count();
        if ($ownersCount > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette agence : ' . $ownersCount . ' propriétaire(s) sont enregistrés. Veuillez d\'abord les supprimer.');
        }

        // Vérifier les comptes avec solde
        $accountsWithBalance = $agency->accounts()->where('balance', '!=', 0)->count();
        if ($accountsWithBalance > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette agence : ' . $accountsWithBalance . ' compte(s) ont un solde non nul.');
        }

        // Supprimer le logo
        if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
            Storage::disk('public')->delete($agency->logo_path);
        }

        $agency->delete();

        return redirect()->route('agencies.index')
            ->with('success', 'Agence supprimée avec succès.');
    }
}

