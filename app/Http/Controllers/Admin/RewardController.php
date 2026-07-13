<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * Display a listing of the rewards.
     */
    public function index()
    {
        $rewards = Reward::orderBy('points_cost', 'asc')->paginate(10);
        return view('admin.rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new reward.
     */
    public function create()
    {
        return view('admin.rewards.create');
    }

    /**
     * Store a newly created reward in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'points_cost' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        Reward::create($request->all());

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Hadiah baru berhasil ditambahkan ke katalog!');
    }

    /**
     * Show the form for editing the specified reward.
     */
    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        return view('admin.rewards.edit', compact('reward'));
    }

    /**
     * Update the specified reward in storage.
     */
    public function update(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'points_cost' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $reward->update($request->all());

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Detail hadiah berhasil diperbarui!');
    }

    /**
     * Remove the specified reward from storage.
     */
    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Hadiah berhasil dihapus dari katalog.');
    }
}
