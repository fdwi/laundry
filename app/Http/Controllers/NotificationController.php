<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Ambil semua notifikasi user yang login (JSON untuk polling)
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->with('order:id,order_number')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id'           => $n->id,
                    'type'         => $n->type,
                    'title'        => $n->title,
                    'message'      => $n->message,
                    'is_read'      => $n->is_read,
                    'order_number' => $n->order?->order_number,
                    'order_id'     => $n->order_id,
                    'created_at'   => $n->created_at?->diffForHumans(),
                    'icon'         => $n->icon,
                ];
            });

        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Hapus semua notifikasi yang sudah dibaca
     */
    public function clearRead()
    {
        Auth::user()->notifications()->where('is_read', true)->delete();

        return response()->json(['success' => true]);
    }
}
