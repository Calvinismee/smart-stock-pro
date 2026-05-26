<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::withSum('inventoryStocks', 'quantity');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('city', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
            });
        }

        $sortField = $request->input('sort', 'name');
        $sortDir = $request->input('direction', 'asc');
        $query->orderBy($sortField, $sortDir);

        return Inertia::render('Warehouses/Index', [
            'warehouses' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Warehouses/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:warehouses,code'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $warehouse = Warehouse::create($validated);
        AuditLogService::log('create', 'warehouses', "Created warehouse: {$warehouse->name}");

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['inventoryStocks.product:id,name,sku,minimum_stock']);

        return Inertia::render('Warehouses/Show', ['warehouse' => $warehouse]);
    }

    public function edit(Warehouse $warehouse)
    {
        return Inertia::render('Warehouses/Edit', ['warehouse' => $warehouse]);
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', "unique:warehouses,code,{$warehouse->id}"],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $old = $warehouse->toArray();
        $warehouse->update($validated);
        AuditLogService::log('update', 'warehouses', "Updated warehouse: {$warehouse->name}", $old, $warehouse->fresh()->toArray());

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $name = $warehouse->name;
        $old = $warehouse->toArray();
        $warehouse->delete();
        AuditLogService::log('delete', 'warehouses', "Deleted warehouse: {$name}", $old);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
