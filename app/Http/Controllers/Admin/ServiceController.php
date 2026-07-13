<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * List all services
     */
    public function index()
    {
        $services = Service::orderBy('name', 'asc')->get();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show service creation form
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store new service
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:services,name',
            'description' => 'required|string|max:500',
            'unit' => 'required|in:kg,pcs',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
        ]);

        $pricePerKg = $request->unit === 'kg' ? $request->price : 0;
        $pricePerPcs = $request->unit === 'pcs' ? $request->price : 0;

        Service::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'unit' => $request->unit,
            'price_per_kg' => $pricePerKg,
            'price_per_pcs' => $pricePerPcs,
            'duration_hours' => $request->duration_hours,
            'is_active' => true,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan baru berhasil ditambahkan!');
    }

    /**
     * Show service edit form
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:services,name,' . $service->id,
            'description' => 'required|string|max:500',
            'unit' => 'required|in:kg,pcs',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
        ]);

        $pricePerKg = $request->unit === 'kg' ? $request->price : 0;
        $pricePerPcs = $request->unit === 'pcs' ? $request->price : 0;

        $service->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'unit' => $request->unit,
            'price_per_kg' => $pricePerKg,
            'price_per_pcs' => $pricePerPcs,
            'duration_hours' => $request->duration_hours,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Detail layanan berhasil diperbarui!');
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();

        $statusStr = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', 'Layanan ' . $service->name . ' berhasil ' . $statusStr . '!');
    }
}
