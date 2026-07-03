<?php

namespace App\Http\Controllers;

use App\Models\Litige;
use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Owner;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LitigeController extends Controller
{
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $query = Litige::with(['contract', 'tenant', 'property', 'owner'])
            ->where('agency_id', $agencyId);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        if ($request->filled('nature_litige')) {
            $query->where('nature_litige', $request->nature_litige);
        }
        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->date_fin);
        }

        $litiges = $query->latest('date_debut')->paginate(15);
        $owners = Owner::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('litiges.index', compact('litiges', 'owners'));
    }

    public function create()
    {
        $agencyId = $this->requireAgencyId();

        $contracts = Contract::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->with(['tenant', 'property', 'owner'])
            ->get();
        $tenants = Tenant::where('agency_id', $agencyId)->orderBy('first_name')->get();
        $properties = Property::where('agency_id', $agencyId)->orderBy('address')->get();
        $owners = Owner::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('litiges.create', compact('contracts', 'tenants', 'properties', 'owners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'property_id' => 'nullable|exists:properties,id',
            'owner_id' => 'nullable|exists:owners,id',
            'reference' => 'nullable|string|max:100',
            'personnes_concernées' => 'nullable|string|max:255',
            'lieu_intervention' => 'nullable|string|max:255',
            'type_contrat' => 'nullable|string|max:100',
            'nature_litige' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'suivi_commentaires' => 'nullable|string',
            'statut' => 'nullable|string|in:en_cours,regle,cloture',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $validated['agency_id'] = $this->requireAgencyId();

        $couts = [];
        foreach (['frais_huissier', 'honoraires_avocat', 'frais_reparation', 'dedommagement', 'transport', 'autres'] as $k) {
            if ($request->filled("couts_{$k}")) {
                $couts[$k] = (float) str_replace(',', '.', $request->input("couts_{$k}"));
            }
        }
        $validated['couts_engages'] = $couts ?: null;

        $pertes = [];
        foreach (['loyer_impaye', 'charges_non_recouvrees', 'risque_perte_locataire'] as $k) {
            if ($request->filled("pertes_{$k}")) {
                $pertes[$k] = (float) str_replace(',', '.', $request->input("pertes_{$k}"));
            }
        }
        $validated['pertes_financieres'] = $pertes ?: null;

        Litige::create($validated);

        return redirect()->route('litiges.index')->with('success', 'Litige enregistré avec succès.');
    }

    public function show(Litige $litige)
    {
        $agencyId = $this->requireAgencyId();
        if ($litige->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé.');
        }
        $litige->load(['contract.tenant', 'contract.property', 'tenant', 'property', 'owner']);
        return view('litiges.show', compact('litige'));
    }

    public function edit(Litige $litige)
    {
        $agencyId = $this->requireAgencyId();
        if ($litige->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé.');
        }
        $agencyId = $this->requireAgencyId();
        $contracts = Contract::where('agency_id', $agencyId)->where('status', 'active')->with(['tenant', 'property', 'owner'])->get();
        $tenants = Tenant::where('agency_id', $agencyId)->orderBy('first_name')->get();
        $properties = Property::where('agency_id', $agencyId)->orderBy('address')->get();
        $owners = Owner::where('agency_id', $agencyId)->orderBy('name')->get();
        return view('litiges.edit', compact('litige', 'contracts', 'tenants', 'properties', 'owners'));
    }

    public function update(Request $request, Litige $litige)
    {
        $agencyId = $this->requireAgencyId();
        if ($litige->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'property_id' => 'nullable|exists:properties,id',
            'owner_id' => 'nullable|exists:owners,id',
            'reference' => 'nullable|string|max:100',
            'personnes_concernées' => 'nullable|string|max:255',
            'lieu_intervention' => 'nullable|string|max:255',
            'type_contrat' => 'nullable|string|max:100',
            'nature_litige' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'suivi_commentaires' => 'nullable|string',
            'statut' => 'nullable|string|in:en_cours,regle,cloture',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        $couts = [];
        foreach (['frais_huissier', 'honoraires_avocat', 'frais_reparation', 'dedommagement', 'transport', 'autres'] as $k) {
            if ($request->filled("couts_{$k}")) {
                $couts[$k] = (float) str_replace(',', '.', $request->input("couts_{$k}"));
            }
        }
        $validated['couts_engages'] = $couts ?: null;

        $pertes = [];
        foreach (['loyer_impaye', 'charges_non_recouvrees', 'risque_perte_locataire'] as $k) {
            if ($request->filled("pertes_{$k}")) {
                $pertes[$k] = (float) str_replace(',', '.', $request->input("pertes_{$k}"));
            }
        }
        $validated['pertes_financieres'] = $pertes ?: null;

        $litige->update($validated);

        return redirect()->route('litiges.show', $litige)->with('success', 'Litige mis à jour.');
    }

    public function destroy(Litige $litige)
    {
        $agencyId = $this->requireAgencyId();
        if ($litige->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé.');
        }
        $litige->delete();
        return redirect()->route('litiges.index')->with('success', 'Litige supprimé.');
    }

    public function rapport(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $query = Litige::with(['contract', 'tenant', 'property', 'owner'])
            ->where('agency_id', $agencyId);

        if ($request->filled('date_debut')) {
            $query->where(function ($q) use ($request) {
                $q->where('date_debut', '>=', $request->date_debut)
                    ->orWhere('date_fin', '>=', $request->date_debut);
            });
        }
        if ($request->filled('date_fin')) {
            $query->where(function ($q) use ($request) {
                $q->where('date_fin', '<=', $request->date_fin)
                    ->orWhere('date_debut', '<=', $request->date_fin);
            });
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        $litiges = $query->orderBy('date_debut')->get();
        $owners = Owner::where('agency_id', $agencyId)->orderBy('name')->get();

        return view('litiges.rapport', compact('litiges', 'owners'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $agencyId = $this->requireAgencyId();
        $query = Litige::with(['contract', 'tenant', 'property', 'owner'])
            ->where('agency_id', $agencyId);
        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->date_fin);
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        $litiges = $query->orderBy('date_debut')->get();

        $filename = 'rapport_litiges_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($litiges) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Référence', 'Personnes concernées', 'Lieu', 'Type contrat', 'Nature litige',
                'Bailleur', 'Date début', 'Date fin', 'Statut', 'Suivi / Commentaires',
                'Coûts engagés', 'Pertes financières'
            ], ';');
            foreach ($litiges as $l) {
                $bailleur = $l->owner ? $l->owner->name : '—';
                $couts = $l->couts_engages ? json_encode($l->couts_engages) : '';
                $pertes = $l->pertes_financieres ? json_encode($l->pertes_financieres) : '';
                fputcsv($out, [
                    $l->reference,
                    $l->personnes_concernées,
                    $l->lieu_intervention,
                    $l->type_contrat,
                    $l->nature_litige,
                    $bailleur,
                    $l->date_debut?->format('d/m/Y'),
                    $l->date_fin?->format('d/m/Y'),
                    $l->statut,
                    $l->suivi_commentaires,
                    $couts,
                    $pertes,
                ], ';');
            }
            fclose($out);
        }, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        $query = Litige::with(['contract', 'tenant', 'property', 'owner'])
            ->where('agency_id', $agencyId);
        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->date_fin);
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        $litiges = $query->orderBy('date_debut')->get();

        $pdf = Pdf::loadView('litiges.pdf', compact('litiges'));
        return $pdf->download('rapport_litiges_' . date('Y-m-d') . '.pdf');
    }

    public function exportWord(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        $query = Litige::with(['contract', 'tenant', 'property', 'owner'])
            ->where('agency_id', $agencyId);
        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->date_fin);
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        $litiges = $query->orderBy('date_debut')->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addTitle('Rapport de situation juridique - Litiges immobiliers', 0);
        $section->addText('Généré le ' . now()->format('d/m/Y H:i'), ['size' => 9, 'color' => '666666']);
        $section->addTextBreak(1);

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => 'CCCCCC']);
        $headerStyle = ['bold' => true, 'bgColor' => 'DC2626', 'color' => 'FFFFFF'];
        $table->addRow();
        $table->addCell(1200)->addText('Réf.', $headerStyle);
        $table->addCell(2500)->addText('Personnes / Lieu', $headerStyle);
        $table->addCell(2000)->addText('Type contrat', $headerStyle);
        $table->addCell(2000)->addText('Bailleur', $headerStyle);
        $table->addCell(2500)->addText('Nature litige', $headerStyle);
        $table->addCell(1500)->addText('Date début', $headerStyle);
        $table->addCell(1500)->addText('Date fin', $headerStyle);
        $table->addCell(1200)->addText('Statut', $headerStyle);

        foreach ($litiges as $l) {
            $table->addRow();
            $table->addCell(1200)->addText($l->reference ?? '—');
            $table->addCell(2500)->addText(($l->personnes_concernées ?? ($l->tenant ? $l->tenant->first_name . ' ' . $l->tenant->last_name : '—')) . "\n" . ($l->lieu_intervention ?? ''));
            $table->addCell(2000)->addText(Litige::typesContrat()[$l->type_contrat] ?? $l->type_contrat ?? '—');
            $table->addCell(2000)->addText($l->owner?->name ?? '—');
            $table->addCell(2500)->addText(Litige::naturesLitige()[$l->nature_litige] ?? $l->nature_litige ?? '—');
            $table->addCell(1500)->addText($l->date_debut?->format('d/m/Y') ?? '—');
            $table->addCell(1500)->addText($l->date_fin?->format('d/m/Y') ?? '—');
            $table->addCell(1200)->addText($l->statut ?? '—');
        }

        $filename = storage_path('app/rapport_litiges_' . date('Y-m-d_His') . '.docx');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
