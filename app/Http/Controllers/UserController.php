<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $query = User::with('roles')
            ->when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->when(!$agencyId, fn ($q) => $q->with('agency'))
            ->when($request->filled('role'), fn ($q) => $q->role($request->role))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('name', 'like', '%' . $request->search . '%')
                   ->orWhere('email', 'like', '%' . $request->search . '%');
            }))
            ->orderBy('name');

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $this->requireAgencyId();
        $roles = $this->getAllowedRoles();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role'  => 'required|in:' . implode(',', $this->getAllowedRoles()->pluck('name')->toArray()),
            'phone' => 'nullable|string|max:20',
        ]);

        $token = Str::random(64);

        $user = User::create([
            'name'                   => $validated['name'],
            'email'                  => $validated['email'],
            'phone'                  => $validated['phone'] ?? null,
            'password'               => Hash::make(Str::random(32)),
            'agency_id'              => $agencyId,
            'invitation_token'       => hash('sha256', $token),
            'invitation_expires_at'  => now()->addHours(48),
            'is_active'              => false,
        ]);

        $user->assignRole($validated['role']);

        // Envoyer l'email d'invitation
        $invitationUrl = route('invitation.accept', ['token' => $token]);
        Mail::to($user->email)->send(new \App\Mail\UserInvitation($user, $invitationUrl));

        return redirect()->route('users.index')
            ->with('success', "Invitation envoyée à {$user->email}. Le lien est valable 48h.");
    }

    public function show(User $user)
    {
        $this->authorizeAgency($user->agency_id);
        $user->load(['roles', 'agency']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeAgency($user->agency_id);
        $roles = $this->getAllowedRoles();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAgency($user->agency_id);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'required|in:' . implode(',', $this->getAllowedRoles()->pluck('name')->toArray()),
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour.');
    }

    public function toggleActive(User $user)
    {
        $this->authorizeAgency($user->agency_id);

        // Empêcher de désactiver son propre compte
        abort_if($user->id === auth()->id(), 403, 'Impossible de désactiver votre propre compte.');

        $user->update(['is_active' => !$user->is_active]);

        $msg = $user->is_active ? 'Compte réactivé.' : 'Compte désactivé.';

        return redirect()->route('users.index')->with('success', $msg);
    }

    public function resendInvitation(User $user)
    {
        $this->authorizeAgency($user->agency_id);
        abort_if($user->is_active, 422, 'Cet utilisateur a déjà activé son compte.');

        $token = Str::random(64);
        $user->update([
            'invitation_token'      => hash('sha256', $token),
            'invitation_expires_at' => now()->addHours(48),
        ]);

        $invitationUrl = route('invitation.accept', ['token' => $token]);
        Mail::to($user->email)->send(new \App\Mail\UserInvitation($user, $invitationUrl));

        return redirect()->route('users.index')
            ->with('success', "Invitation renvoyée à {$user->email}.");
    }

    public function destroy(User $user)
    {
        $this->authorizeAgency($user->agency_id);
        abort_if($user->id === auth()->id(), 403, 'Impossible de supprimer votre propre compte.');

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }

    private function getAllowedRoles()
    {
        $user = auth()->user();

        // super_admin peut créer tous les rôles sauf super_admin
        if ($user->hasRole('super_admin')) {
            return Role::whereNotIn('name', ['super_admin', 'locataire'])->orderBy('name')->get();
        }

        // admin_agence peut créer tous les rôles opérationnels sauf admin+ et locataire
        return Role::whereIn('name', [
            'admin_agence', 'gestionnaire', 'comptable', 'charge_recouvrement', 'agent_immobilier', 'proprietaire'
        ])->orderBy('name')->get();
    }
}
