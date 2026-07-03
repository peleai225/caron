<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;
        
        $ownerId = null;
        // Les propriétaires voient uniquement les données de leurs biens
        if ($user->hasRole('proprietaire')) {
            $owner = \App\Models\Owner::where('email', $user->email)->first();
            if (!$owner) {
                return redirect()->route('owner.dashboard')
                    ->with('error', 'Aucun compte propriétaire associé.');
            }
            $ownerId = $owner->id;
            $agencyId = $owner->agency_id ?? $agencyId;
        } else {
            // Pour les autres rôles, vérifier l'agence
            if (!$agencyId) {
                return redirect()->route('dashboard')
                    ->with('error', 'Vous devez être associé à une agence pour accéder aux rapports.');
            }
        }

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        $report = $this->reportService->generateFinancialReport(
            $agencyId,
            $startDate,
            $endDate,
            $ownerId
        );

        return view('reports.index', compact('report', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;
        $ownerId = null;

        if ($user->hasRole('proprietaire')) {
            $owner = \App\Models\Owner::where('email', $user->email)->first();
            if (!$owner) {
                return redirect()->back()->with('error', 'Aucun compte propriétaire associé.');
            }
            $ownerId = $owner->id;
            $agencyId = $owner->agency_id ?? $agencyId;
        }

        if (!$agencyId && !$ownerId) {
            return redirect()->back()
                ->with('error', 'Vous devez être associé à une agence pour exporter les rapports.');
        }

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        $report = $this->reportService->generateFinancialReport(
            $agencyId,
            $startDate,
            $endDate,
            $ownerId
        );

        $filename = 'rapport-financier-' . now()->format('Y-m-d') . '.xlsx';
        $path = $this->reportService->exportToExcel($report, $filename);

        return response()->download(storage_path('app/public/reports/' . $path));
    }

    public function exportPDF(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;
        $ownerId = null;

        if ($user->hasRole('proprietaire')) {
            $owner = \App\Models\Owner::where('email', $user->email)->first();
            if (!$owner) {
                return redirect()->back()->with('error', 'Aucun compte propriétaire associé.');
            }
            $ownerId = $owner->id;
            $agencyId = $owner->agency_id ?? $agencyId;
        }

        if (!$agencyId && !$ownerId) {
            return redirect()->back()
                ->with('error', 'Vous devez être associé à une agence pour exporter les rapports.');
        }

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        $report = $this->reportService->generateFinancialReport(
            $agencyId,
            $startDate,
            $endDate,
            $ownerId
        );

        $filename = 'rapport-financier-' . now()->format('Y-m-d') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', ['data' => $report])
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}
