@extends('layouts.app')

@section('title', 'Templates')
@section('page-title', 'Templates')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Templates de documents</h2>
            <p class="page-subtitle">Generez vos documents juridiques et administratifs</p>
        </div>
        <a href="{{ route('document-templates.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouveau template
        </a>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('document-templates.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Categorie</label>
                    <select name="category" class="input-modern">
                        <option value="">Toutes</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                    <select name="type" class="input-modern">
                        <option value="">Tous</option>
                        <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Systeme</option>
                        <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Personnel</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
                    <a href="{{ route('document-templates.index') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Templates Grid -->
    @if($templates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
            <div class="card-panel hover:border-slate-300 transition-colors">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ $template->name }}</h3>
                            @if($template->is_system)
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-primary-50 text-primary-700 ring-primary-600/20">Systeme</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Personnel</span>
                            @endif
                        </div>
                        <span class="px-1.5 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-600 rounded-md">{{ $categories[$template->category] ?? $template->category }}</span>
                    </div>

                    @if($template->description)
                        <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $template->description }}</p>
                    @endif

                    <div class="flex items-center justify-between text-xs text-slate-400 mb-3">
                        <span>Utilise {{ $template->usage_count }} fois</span>
                        <span>{{ $template->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('document-templates.show', $template) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                        <a href="{{ route('document-templates.generate', $template) }}" class="px-2 py-1 text-xs font-medium text-emerald-600 hover:bg-emerald-50 rounded transition-colors">Generer</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($templates->hasPages())
        <div class="mt-4">{{ $templates->links() }}</div>
        @endif
    @else
        <div class="card-panel">
            <div class="card-panel-body text-center py-12">
                <div class="empty-state-icon">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun template trouve</h3>
                <p class="text-xs text-slate-500 mb-4">Creez votre premier template de document</p>
                <a href="{{ route('document-templates.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nouveau template
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
