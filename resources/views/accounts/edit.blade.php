@extends('layouts.app')

@section('title', 'Modifier le compte')
@section('page-title', 'Modifier le compte')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le compte</h2>
            <p class="page-subtitle">{{ $account->name }}</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('accounts.update', $account) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom du compte <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $account->name) }}" class="input-modern">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de compte <span class="text-red-500">*</span></label>
                    <select name="type" id="type" class="input-modern">
                        <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Especes</option>
                        <option value="bank" {{ old('type', $account->type) == 'bank' ? 'selected' : '' }}>Banque</option>
                        <option value="mobile_money" {{ old('type', $account->type) == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div id="bank_fields" class="{{ old('type', $account->type) == 'bank' ? '' : 'hidden' }}">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom de la banque <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $account->bank_name) }}" class="input-modern">
                    @error('bank_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero de compte</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $account->account_number) }}" class="input-modern">
                    @error('account_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Compte actif</span>
                    </label>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('accounts.show', $account) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    document.getElementById('bank_fields').classList.toggle('hidden', this.value !== 'bank');
});
</script>
@endsection
