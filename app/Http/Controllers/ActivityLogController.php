<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Affiche la liste des logs d'activité
     */
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Activity::query()
            ->where(function($q) use ($agencyId) {
                // Pour Property, Tenant, Contract qui ont directement agency_id
                $q->whereHasMorph('subject', ['App\Models\Property', 'App\Models\Tenant', 'App\Models\Contract'], function ($subQ) use ($agencyId) {
                    $subQ->where('agency_id', $agencyId);
                })
                // Pour Payment qui est lié via Contract
                ->orWhereHasMorph('subject', ['App\Models\Payment'], function ($subQ) use ($agencyId) {
                    $subQ->whereHas('contract', function ($contractQ) use ($agencyId) {
                        $contractQ->where('agency_id', $agencyId);
                    });
                });
            });

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->with(['causer', 'subject'])->latest()->paginate(50);

        $users = \App\Models\User::where('agency_id', $agencyId)->get();

        return view('activity-logs.index', compact('logs', 'users'));
    }

    /**
     * Affiche les détails d'un log
     */
    public function show(Activity $activityLog)
    {
        $activityLog->load(['causer', 'subject']);
        
        return view('activity-logs.show', compact('activityLog'));
    }
}

