<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all customer users with pagination and search
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = User::where('role', 'customer');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $users = $query->withCount('orders')
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();
        
        return view('admin.users', compact('users', 'search'));
    }

    /**
     * Toggle active/inactive status of a customer
     */
    public function toggleStatus($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', 'Akun pelanggan ' . $user->name . ' berhasil ' . $statusStr . '!');
    }
}
