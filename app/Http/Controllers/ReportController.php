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
        $request->validate([
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $params = $this->resolveReportParams($request);
        if ($params instanceof \Illuminate\Http\RedirectResponse) {
            return $params;
        }

        $report = $this->reportService->generateFinancialReport(
            $params['agency_id'],
            $params['start_date'],
            $params['end_date'],
            $params['owner_id']
        );

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        return view('reports.index', compact('report', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $params = $this->resolveReportParams($request);
        if ($params instanceof \Illuminate\Http\RedirectResponse) {
            return $params;
        }

        $report = $this->reportService->generateFinancialReport(
            $params['agency_id'],
            $params['start_date'],
            $params['end_date'],
            $params['owner_id']
        );

        $filename = 'rapport-financier-' . now()->format('Y-m-d') . '.xlsx';
        $path = $this->reportService->exportToExcel($report, $filename);

        return response()->download(storage_path('app/public/reports/' . $path))
            ->deleteFileAfterSend(true);
    }

    public function exportPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $params = $this->resolveReportParams($request);
        if ($params instanceof \Illuminate\Http\RedirectResponse) {
            return $params;
        }

        $report = $this->reportService->generateFinancialReport(
            $params['agency_id'],
            $params['start_date'],
            $params['end_date'],
            $params['owner_id']
        );

        $filename = 'rapport-financier-' . now()->format('Y-m-d') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', ['data' => $report])
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function resolveReportParams(Request $request): array|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;
        $ownerId = null;

        if ($user->hasRole('proprietaire')) {
            $owner = \App\Models\Owner::where('email', $user->email)->first();
            if (!$owner) {
                return redirect()->route('owner.dashboard')
                    ->with('error', 'Aucun compte propriétaire associé.');
            }
            $ownerId = $owner->id;
            $agencyId = $owner->agency_id ?? $agencyId;
        } elseif (!$agencyId) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez être associé à une agence pour accéder aux rapports.');
        }

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : now()->endOfMonth();

        return [
            'agency_id' => $agencyId,
            'owner_id' => $ownerId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
