@extends('layouts.app')

@section('title', 'Nouveau compte')
@section('page-title', 'Nouveau compte')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau compte</h2>
            <p class="page-subtitle">Creez un compte financier</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom du compte <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Compte principal, Caisse..." class="input-modern">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de compte <span class="text-red-500">*</span></label>
                    <select name="type" id="type" class="input-modern">
                        <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Especes</option>
                        <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Banque</option>
                        <option value="mobile_money" {{ old('type') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div id="bank_fields" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom de la banque <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="Ex: UBA, SGBC..." class="input-modern">
                    @error('bank_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero de compte</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Optionnel" class="input-modern">
                    @error('account_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Solde initial (FCFA)</label>
                    <input type="number" name="balance" value="{{ old('balance', 0) }}" step="0.01" min="0" class="input-modern">
                    @error('balance') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Compte actif</span>
                    </label>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('accounts.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Creer le compte</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    document.getElementById('bank_fields').classList.toggle('hidden', this.value !== 'bank');
});
document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endsection
