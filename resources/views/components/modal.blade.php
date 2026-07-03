@props([
    'id' => 'app-modal',
    'size' => 'md',
    'title' => null,
    'closeOnBackdrop' => true,
])

@php
    $sizeClass = match($size ?? 'md') {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'max' => 'max-w-[95vw] max-h-[90vh]',
        default => 'max-w-lg',
    };
@endphp

<div id="{{ $id }}" class="modal-overlay fixed inset-0 z-[100] hidden opacity-0 transition-opacity duration-200 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="modal-backdrop absolute inset-0" @if($closeOnBackdrop) data-modal-close="{{ $id }}" @endif></div>
    <div class="modal-content relative {{ $sizeClass }} w-full bg-white dark:bg-slate-800 rounded-xl shadow-2xl transform transition-all duration-200 scale-95 max-h-[90vh] overflow-auto" data-modal-content>
        @if($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 id="{{ $id }}-title" class="text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
                <button type="button" class="modal-close p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" data-modal-close="{{ $id }}" aria-label="Fermer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @else
            <button type="button" class="modal-close absolute top-3 right-3 z-10 p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-white transition-colors" data-modal-close="{{ $id }}" aria-label="Fermer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        @endif
        <div class="{{ $title ? 'p-6' : 'p-6 pt-12' }}">
            {{ $slot }}
        </div>
    </div>
</div>
