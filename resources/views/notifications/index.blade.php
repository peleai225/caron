@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Notifications</h2>
            <p class="page-subtitle">{{ $unreadCount }} notification{{ $unreadCount > 1 ? 's' : '' }} non lue{{ $unreadCount > 1 ? 's' : '' }}</p>
        </div>
        @if($unreadCount > 0)
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="btn-primary">Tout marquer comme lu</button>
        </form>
        @endif
    </header>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="card-panel">
        @if($notifications->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach($notifications as $notification)
            <div class="px-5 py-4 hover:bg-slate-50/50 transition-colors {{ is_null($notification->read_at) ? 'bg-primary-50/30' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            @if(is_null($notification->read_at) || !$notification->is_read)
                            <span class="w-2 h-2 bg-primary-600 rounded-full flex-shrink-0"></span>
                            @endif
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $notification->title ?? ($notification->data['title'] ?? 'Notification') }}</p>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">{{ $notification->message ?? ($notification->data['message'] ?? '') }}</p>
                        @if(!empty($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="inline-block mt-1.5 text-xs text-primary-600 hover:text-primary-700 font-medium">Voir le détail →</a>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(is_null($notification->read_at) || !$notification->is_read)
                        <form action="{{ route('notifications.read', $notification) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-[11px] text-primary-600 hover:text-primary-700 font-medium">Marquer lu</button>
                        </form>
                        @endif
                        <form id="form-delete-notif-{{ $notification->id }}" action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-confirm="Supprimer cette notification ?" data-confirm-form="form-delete-notif-{{ $notification->id }}" class="text-[11px] text-red-600 hover:text-red-700 font-medium">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-5 py-4 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
        @else
        <div class="px-5 py-12 text-center">
            <div class="empty-state-icon mx-auto mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-900">Aucune notification</p>
            <p class="text-xs text-slate-500 mt-1">Vous n'avez aucune notification pour le moment</p>
        </div>
        @endif
    </div>
</div>
@endsection
