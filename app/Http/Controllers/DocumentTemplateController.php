<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\Contract;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentTemplateController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Affiche la liste des templates
     */
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = DocumentTemplate::query()
            ->where(function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId)
                  ->orWhereNull('agency_id'); // Templates système
            });

        // Filtres
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->where('is_system', true);
            } else {
                $query->where('is_system', false);
            }
        }

        $templates = $query->active()->latest()->paginate(20);

        $categories = [
            'contract' => 'Contrats',
            'termination' => 'Résiliations',
            'amendment' => 'Avenants',
            'notification' => 'Notifications',
            'legal' => 'Documents juridiques',
            'receipt' => 'Reçus',
            'option' => 'Options',
            'sale' => 'Ventes',
            'management' => 'Gestion',
            'other' => 'Autres',
        ];

        return view('document-templates.index', compact('templates', 'categories'));
    }

    /**
     * Affiche les détails d'un template
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        $documentTemplate->load('agency');
        
        // Préparer les variables disponibles
        $availableVariables = $documentTemplate->getAvailableVariables();
        
        return view('document-templates.show', compact('documentTemplate', 'availableVariables'));
    }

    /**
     * Affiche le formulaire de génération de document
     */
    public function generate(DocumentTemplate $documentTemplate, Request $request)
    {
        $contractId = $request->get('contract_id');
        $contract = $contractId ? Contract::with(['tenant', 'property', 'owner', 'agency'])->find($contractId) : null;
        
        // Préparer les variables par défaut si un contrat est sélectionné
        $defaultVariables = [];
        if ($contract) {
            $defaultVariables = $this->documentService->prepareContractVariables($contract);
        }

        $availableVariables = $documentTemplate->getAvailableVariables();
        
        // Récupérer les contrats pour le select
        $agencyId = $this->requireAgencyId();
        $contracts = Contract::where('agency_id', $agencyId)
            ->with(['tenant', 'property'])
            ->latest()
            ->get();

        return view('document-templates.generate', compact(
            'documentTemplate',
            'availableVariables',
            'defaultVariables',
            'contracts',
            'contract'
        ));
    }

    /**
     * Génère le document
     */
    public function storeGenerated(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'format' => 'required|in:docx,pdf',
            'contract_id' => 'nullable|exists:contracts,id',
            'variables' => 'nullable|array',
        ]);

        try {
            // Préparer les variables
            $variables = $validated['variables'] ?? [];
            
            // Si un contrat est fourni, utiliser ses variables
            if ($validated['contract_id']) {
                $contract = Contract::with(['tenant', 'property', 'owner', 'agency'])->find($validated['contract_id']);
                $contractVariables = $this->documentService->prepareContractVariables($contract);
                $variables = array_merge($contractVariables, $variables);
            }

            // Générer le document
            if ($validated['format'] === 'docx') {
                $filePath = $this->documentService->generateDocx($documentTemplate, $variables);
            } else {
                $filePath = $this->documentService->generatePdf($documentTemplate, $variables);
            }

            // Incrémenter le compteur d'utilisation
            $documentTemplate->incrementUsage();

            // Télécharger le fichier
            $fullPath = storage_path('app/public/' . $filePath);
            
            return response()->download($fullPath)->deleteFileAfterSend(false);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la génération: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $categories = [
            'contract' => 'Contrats',
            'termination' => 'Résiliations',
            'amendment' => 'Avenants',
            'notification' => 'Notifications',
            'legal' => 'Documents juridiques',
            'receipt' => 'Reçus',
            'option' => 'Options',
            'sale' => 'Ventes',
            'management' => 'Gestion',
            'other' => 'Autres',
        ];

        return view('document-templates.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:contract,termination,amendment,notification,legal,receipt,option,sale,management,other',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:docx|max:10240',
            'country' => 'nullable|string|max:2',
        ]);

        $agencyId = $this->requireAgencyId();

        try {
            // Sauvegarder le fichier temporairement
            $tempPath = $request->file('file')->storeAs('temp', Str::random(40) . '.docx');
            $fullTempPath = storage_path('app/' . $tempPath);

            // Importer le template
            $template = $this->documentService->importTemplate($fullTempPath, [
                'name' => $validated['name'],
                'category' => $validated['category'],
                'country' => $validated['country'] ?? 'CI',
                'description' => $validated['description'] ?? null,
                'agency_id' => $agencyId,
                'is_system' => false,
                'is_active' => true,
            ]);

            // Supprimer le fichier temporaire
            Storage::delete($tempPath);

            return redirect()->route('document-templates.show', $template)
                ->with('success', 'Template créé avec succès.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        if (!$documentTemplate->canBeEdited()) {
            return back()->withErrors(['error' => 'Ce template système ne peut pas être modifié.']);
        }

        $categories = [
            'contract' => 'Contrats',
            'termination' => 'Résiliations',
            'amendment' => 'Avenants',
            'notification' => 'Notifications',
            'legal' => 'Documents juridiques',
            'receipt' => 'Reçus',
            'option' => 'Options',
            'sale' => 'Ventes',
            'management' => 'Gestion',
            'other' => 'Autres',
        ];

        return view('document-templates.edit', compact('documentTemplate', 'categories'));
    }

    /**
     * Met à jour un template
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        if (!$documentTemplate->canBeEdited()) {
            return back()->withErrors(['error' => 'Ce template système ne peut pas être modifié.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:contract,termination,amendment,notification,legal,receipt,option,sale,management,other',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $documentTemplate->update($validated);

        return redirect()->route('document-templates.show', $documentTemplate)
            ->with('success', 'Template mis à jour avec succès.');
    }

    /**
     * Supprime un template
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        if (!$documentTemplate->canBeEdited()) {
            return back()->withErrors(['error' => 'Ce template système ne peut pas être supprimé.']);
        }

        // Supprimer le fichier
        if ($documentTemplate->file_path && Storage::disk('public')->exists($documentTemplate->file_path)) {
            Storage::disk('public')->delete($documentTemplate->file_path);
        }

        $documentTemplate->delete();

        return redirect()->route('document-templates.index')
            ->with('success', 'Template supprimé avec succès.');
    }
}

