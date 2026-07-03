<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Auth::user()
            ->notifications()
            ->where(function($query) {
                $query->where('is_read', false)
                      ->orWhereNull('read_at');
            })
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['read_at' => now(), 'is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->where(function($query) {
                $query->where('is_read', false)
                      ->orWhereNull('read_at');
            })
            ->update(['read_at' => now(), 'is_read' => true]);

        return redirect()->route('notifications.index')
            ->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée.');
    }
}
