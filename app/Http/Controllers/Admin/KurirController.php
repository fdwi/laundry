<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class KurirController extends Controller
{
    /**
     * List all kurir accounts
     */
    public function index()
    {
        $kurirMembers = User::where('role', 'kurir')
            ->withCount('kurirOrders')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.kurir.index', compact('kurirMembers'));
    }

    /**
     * Show kurir creation form
     */
    public function create()
    {
        return view('admin.kurir.create');
    }

    /**
     * Store new kurir account
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kurir',
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => true,
        ]);

        return redirect()->route('admin.kurir.index')
            ->with('success', 'Akun kurir baru berhasil dibuat!');
    }

    /**
     * Show edit form for kurir
     */
    public function edit($id)
    {
        $kurir = User::where('role', 'kurir')->findOrFail($id);
        return view('admin.kurir.edit', compact('kurir'));
    }

    /**
     * Update kurir account
     */
    public function update(Request $request, $id)
    {
        $kurir = User::where('role', 'kurir')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $kurir->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $kurir->update($updateData);

        return redirect()->route('admin.kurir.index')
            ->with('success', 'Akun kurir berhasil diperbarui!');
    }

    /**
     * View orders processed by kurir member (performance overview)
     */
    public function orders($id)
    {
        $kurir = User::where('role', 'kurir')->findOrFail($id);
        
        $orders = Order::where('kurir_id', $kurir->id)
            ->with(['customer', 'service'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        $totalProcessed = Order::where('kurir_id', $kurir->id)->count();
        $completedProcessed = Order::where('kurir_id', $kurir->id)->where('status', Order::STATUS_DELIVERED)->count();

        return view('admin.kurir.orders', compact('kurir', 'orders', 'totalProcessed', 'completedProcessed'));
    }
}
