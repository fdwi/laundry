<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\PointTransaction;
use App\Models\Redemption;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RewardController extends Controller
{
    /**
     * Display rewards catalog and points history.
     */
    public function index()
    {
        $user = Auth::user();
        
        $rewards = Reward::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('points_cost', 'asc')
            ->get();
            
        $transactions = PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'transactions_page');
            
        $redemptions = Redemption::where('user_id', $user->id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.rewards.index', compact('rewards', 'transactions', 'redemptions'));
    }

    /**
     * Claim/Redeem a reward using points.
     */
    public function redeem(Request $request, $id)
    {
        $reward = Reward::where('is_active', true)->findOrFail($id);
        $user = Auth::user();

        if ($reward->stock <= 0) {
            return redirect()->back()->with('error', 'Stok hadiah ini sudah habis.');
        }

        if ($user->points < $reward->points_cost) {
            return redirect()->back()->with('error', 'Poin Anda tidak mencukupi untuk menukarkan hadiah ini.');
        }

        DB::beginTransaction();
        try {
            // Deduct user points
            $user->decrement('points', $reward->points_cost);

            // Deduct reward stock
            $reward->decrement('stock', 1);

            // Generate unique voucher/redemption code
            $code = 'LDRY-' . strtoupper(Str::random(6));
            while (Redemption::where('code', $code)->exists()) {
                $code = 'LDRY-' . strtoupper(Str::random(6));
            }

            // Create Redemption
            $redemption = Redemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'code' => $code,
                'status' => 'active',
            ]);

            // Create Point Transaction
            PointTransaction::create([
                'user_id' => $user->id,
                'amount' => -$reward->points_cost,
                'type' => 'redeem',
                'description' => "Penukaran hadiah: {$reward->name} ({$code})",
            ]);

            // Create notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => '🎉 Klaim Hadiah Berhasil!',
                'message' => "Anda telah berhasil menukarkan {$reward->points_cost} poin dengan {$reward->name}. Kode voucher Anda: {$code}.",
                'is_read' => false,
            ]);

            DB::commit();
            return redirect()->route('customer.rewards.index')
                ->with('success', "Berhasil menukarkan poin untuk {$reward->name}! Simpan kode voucher Anda: {$code}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses penukaran poin.');
        }
    }
}
