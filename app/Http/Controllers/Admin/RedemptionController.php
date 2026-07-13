<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redemption;
use App\Models\PointTransaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedemptionController extends Controller
{
    /**
     * Display a listing of redemptions.
     */
    public function index(Request $request)
    {
        $query = Redemption::with(['user', 'reward']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('reward', function($r) use ($search) {
                      $r->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $redemptions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.redemptions.index', compact('redemptions'));
    }

    /**
     * Update/Process redemption status.
     */
    public function process(Request $request, $id)
    {
        $redemption = Redemption::findOrFail($id);
        $oldStatus = $redemption->status;

        $request->validate([
            'status' => 'required|in:active,used,completed,cancelled',
        ]);

        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', 'Status penukaran tidak berubah.');
        }

        // If trying to modify an already finalized/used status, guard against it (except allowing active -> cancelled)
        if (in_array($oldStatus, ['completed', 'used', 'cancelled']) && $newStatus !== $oldStatus) {
            return redirect()->back()->with('error', 'Penukaran yang sudah final tidak dapat diubah kembali.');
        }

        DB::beginTransaction();
        try {
            if ($newStatus === 'cancelled') {
                // Refund points to user
                $user = $redemption->user;
                $user->increment('points', $redemption->points_spent);

                // Increment reward stock back
                $reward = $redemption->reward;
                $reward->increment('stock', 1);

                // Create point transaction log
                PointTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $redemption->points_spent,
                    'type' => 'refund',
                    'description' => "Pengembalian poin (Pembatalan klaim {$reward->name})",
                ]);

                // Notify user
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'info',
                    'title' => '❌ Klaim Hadiah Dibatalkan',
                    'message' => "Klaim Anda untuk {$reward->name} (Kode: {$redemption->code}) telah dibatalkan oleh admin. Poin sebesar {$redemption->points_spent} telah dikembalikan ke akun Anda.",
                    'is_read' => false,
                ]);
            } elseif (in_array($newStatus, ['used', 'completed'])) {
                // Notify user that their reward is marked as claimed/used
                $reward = $redemption->reward;
                $user = $redemption->user;
                
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'info',
                    'title' => '🎁 Hadiah Telah Diserahkan / Digunakan',
                    'message' => "Kode voucher {$redemption->code} untuk {$reward->name} telah berhasil digunakan / diserahkan. Terima kasih!",
                    'is_read' => false,
                ]);
            }

            $redemption->update([
                'status' => $newStatus,
            ]);

            DB::commit();
            
            $statusLabels = [
                'active' => 'Aktif',
                'used' => 'Digunakan',
                'completed' => 'Diserahkan',
                'cancelled' => 'Dibatalkan/Refund'
            ];

            return redirect()->back()->with('success', "Status penukaran {$redemption->code} berhasil diubah menjadi: " . $statusLabels[$newStatus]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses perubahan status penukaran.');
        }
    }
}
