@props(['status', 'size' => 'md'])

@php
    $classes = match($status) {
        'actif', 'active', 'libre', 'paye', 'paid', 'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'occupe', 'occupied' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'en_retard', 'overdue', 'impaye', 'maintenance', 'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
        'resilie', 'terminated', 'expired', 'cancelled' => 'bg-slate-50 text-slate-600 ring-slate-500/20',
        'draft', 'pending', 'waived' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'refunded' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        default => 'bg-slate-50 text-slate-600 ring-slate-500/20',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-1.5 py-0.5 text-[11px]',
        'md' => 'px-2 py-0.5 text-xs',
        'lg' => 'px-2.5 py-1 text-xs',
        default => 'px-2 py-0.5 text-xs',
    };

    $labels = [
        'actif' => 'Actif',
        'active' => 'Actif',
        'libre' => 'Libre',
        'occupe' => 'Occupe',
        'occupied' => 'Occupe',
        'en_retard' => 'En retard',
        'overdue' => 'En retard',
        'impaye' => 'Impaye',
        'paye' => 'Paye',
        'paid' => 'Paye',
        'completed' => 'Complete',
        'maintenance' => 'Maintenance',
        'resilie' => 'Resilie',
        'terminated' => 'Resilie',
        'expired' => 'Expire',
        'cancelled' => 'Annule',
        'draft' => 'Brouillon',
        'pending' => 'En attente',
        'failed' => 'Echoue',
        'refunded' => 'Rembourse',
        'waived' => 'Exonere',
    ];

    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center {{ $sizeClasses }} rounded-md font-medium ring-1 ring-inset {{ $classes }}">
    {{ $label }}
</span>
