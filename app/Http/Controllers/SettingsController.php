<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a le droit d'accéder aux paramètres
        if (!$user->hasAnyRole(['super_admin', 'admin_agence', 'gestionnaire', 'comptable'])) {
            abort(403, 'Vous n\'avez pas la permission d\'accéder aux paramètres.');
        }
        
        $agency = $user->agency;
        $agencyId = $agency ? $agency->id : null;
        
        $settings = Setting::where(function($query) use ($agencyId) {
                if ($agencyId) {
                    $query->where('agency_id', $agencyId);
                }
            })
            ->orWhereNull('agency_id')
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('settings.index', compact('agency', 'settings'));
    }

    public function updateAgency(Request $request)
    {
        $user = Auth::user();
        
        // Seuls les admins et gestionnaires peuvent modifier les paramètres de l'agence
        if (!$user->hasAnyRole(['super_admin', 'admin_agence', 'gestionnaire'])) {
            abort(403, 'Vous n\'avez pas la permission de modifier les paramètres de l\'agence.');
        }
        
        $agency = $user->agency;
        
        if (!$agency) {
            return redirect()->route('settings.index')
                ->with('error', 'Aucune agence associée à votre compte.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg', 'max:512'], // 512KB
        ]);

        // Gestion du logo
        if ($request->hasFile('logo')) {
            if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
                Storage::disk('public')->delete($agency->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        // Gestion du favicon
        if ($request->hasFile('favicon')) {
            if ($agency->favicon_path && Storage::disk('public')->exists($agency->favicon_path)) {
                Storage::disk('public')->delete($agency->favicon_path);
            }
            $path = $request->file('favicon')->store('favicons', 'public');
            $validated['favicon_path'] = $path;
        }

        unset($validated['logo'], $validated['favicon']);
        $agency->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres de l\'agence mis à jour avec succès.');
    }

    public function updateApplication(Request $request)
    {
        $user = Auth::user();
        
        // Seuls les admins peuvent modifier les paramètres de l'application
        if (!$user->hasAnyRole(['super_admin', 'admin_agence'])) {
            abort(403, 'Vous n\'avez pas la permission de modifier les paramètres de l\'application.');
        }
        
        $agencyId = $user->agency_id;

        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'app_email' => ['nullable', 'email', 'max:255'],
            'app_phone' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:10'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value, $agencyId, 'string', 'application');
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres de l\'application mis à jour avec succès.');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        // Seuls les admins et gestionnaires peuvent modifier les paramètres de notifications
        if (!$user->hasAnyRole(['super_admin', 'admin_agence', 'gestionnaire'])) {
            abort(403, 'Vous n\'avez pas la permission de modifier les paramètres de notifications.');
        }
        
        $agencyId = $user->agency_id;

        $validated = $request->validate([
            'email_notifications' => ['nullable', 'boolean'],
            'sms_notifications' => ['nullable', 'boolean'],
            'payment_reminders' => ['nullable', 'boolean'],
            'contract_expiry_alerts' => ['nullable', 'boolean'],
            'overdue_payment_alerts' => ['nullable', 'boolean'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? false, $agencyId, 'boolean', 'notifications');
        }

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres de notifications mis à jour avec succès.');
    }

    public function updatePaymentGateway(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['super_admin', 'admin_agence'])) {
            abort(403, 'Vous n\'avez pas la permission de modifier les paramètres de paiement.');
        }

        $validated = $request->validate([
            'moneyfusion_api_key' => ['nullable', 'string', 'max:500'],
            'moneyfusion_api_url' => ['nullable', 'url', 'max:500'],
        ]);

        // Stockage global (agency_id = null) — accessible à toutes les agences
        // Pour le super_admin on stocke sans agence ; pour admin_agence on stocke par agence
        $agencyId = $user->hasRole('super_admin') ? null : $user->agency_id;

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', $agencyId, 'string', 'payment_gateway');
        }

        return redirect()->route('settings.index', ['tab' => 'payment'])
            ->with('success', 'Paramètres MoneyFusion enregistrés avec succès.');
    }

    public function deleteLogo()
    {
        $user = Auth::user();
        
        // Seuls les admins et gestionnaires peuvent supprimer le logo
        if (!$user->hasAnyRole(['super_admin', 'admin_agence', 'gestionnaire'])) {
            abort(403, 'Vous n\'avez pas la permission de supprimer le logo.');
        }
        
        $agency = $user->agency;

        if (!$agency) {
            return redirect()->route('settings.index')
                ->with('error', 'Aucune agence associée à votre compte.');
        }

        if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
            Storage::disk('public')->delete($agency->logo_path);
        }

        $agency->update(['logo_path' => null]);

        return redirect()->route('settings.index')
            ->with('success', 'Logo supprimé avec succès.');
    }

    public function updateEmail(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'admin_agence'])) {
            abort(403);
        }

        $validated = $request->validate([
            'mail_mailer'       => 'required|in:smtp,log',
            'mail_host'         => 'required_if:mail_mailer,smtp|nullable|string|max:255',
            'mail_port'         => 'required_if:mail_mailer,smtp|nullable|integer|in:25,465,587,2525',
            'mail_encryption'   => 'nullable|in:tls,ssl,starttls',
            'mail_username'     => 'required_if:mail_mailer,smtp|nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:500',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name'    => 'required|string|max:255',
        ]);

        // Le mot de passe est optionnel à l'update — on garde l'ancien si vide
        if (empty($validated['mail_password'])) {
            unset($validated['mail_password']);
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', null, 'string', 'email');
        }

        return redirect()->route('settings.index', ['tab' => 'email'])
            ->with('success', 'Configuration email enregistrée. Utilisez le bouton de test pour vérifier.');
    }

    public function testEmail(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'admin_agence'])) {
            abort(403);
        }

        $request->validate(['test_email' => 'required|email']);

        try {
            Mail::raw(
                "Ceci est un email de test envoyé depuis " . config('app.name') . ".\n\nVotre configuration SMTP fonctionne correctement.",
                function ($message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('[Test] Configuration email — ' . config('app.name'));
                }
            );

            return redirect()->route('settings.index', ['tab' => 'email'])
                ->with('success', "Email de test envoyé à {$request->test_email}. Vérifiez votre boîte mail (et vos spams).");
        } catch (\Exception $e) {
            return redirect()->route('settings.index', ['tab' => 'email'])
                ->with('error', 'Échec de l\'envoi : ' . $e->getMessage());
        }
    }

    public function deleteFavicon()
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['super_admin', 'admin_agence', 'gestionnaire'])) {
            abort(403, 'Vous n\'avez pas la permission de supprimer le favicon.');
        }
        $agency = $user->agency;
        if (!$agency) {
            return redirect()->route('settings.index')->with('error', 'Aucune agence associée.');
        }
        if ($agency->favicon_path && Storage::disk('public')->exists($agency->favicon_path)) {
            Storage::disk('public')->delete($agency->favicon_path);
        }
        $agency->update(['favicon_path' => null]);
        return redirect()->route('settings.index')->with('success', 'Favicon supprimé avec succès.');
    }
}
