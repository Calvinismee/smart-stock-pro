<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category:id,name', 'supplier:id,name']);

        $warehouseId = $request->input('warehouse_id');
        $query->withSum(['inventoryStocks' => function($q) use ($warehouseId) {
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }
        }], 'quantity');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDir);

        $products = $query->paginate(15)->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => Category::select('id', 'name')->get(),
            'warehouses' => \App\Models\Warehouse::where('is_active', true)->select('id', 'name')->get(),
            'filters' => $request->only(['search', 'category_id', 'is_active', 'warehouse_id', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        if (auth()->user()->role === 'staff' || auth()->user()->role === 'auditor') {
            abort(403, 'Akses ditolak.');
        }

        return Inertia::render('Products/Create', [
            'categories' => Category::select('id', 'name')->get(),
            'suppliers' => Supplier::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'staff' || auth()->user()->role === 'auditor') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $galleryImages = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $galleryImages[] = $file->store('products', 'public');
            }
        }
        $validated['gallery'] = $galleryImages;

        $product = Product::create($validated);

        AuditLogService::log('create', 'products', "Created product: {$product->name}", null, $product->toArray());

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category:id,name', 'supplier:id,name', 'inventoryStocks.warehouse:id,name,city']);

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product)
    {
        if (auth()->user()->role === 'staff' || auth()->user()->role === 'auditor') {
            abort(403, 'Akses ditolak.');
        }

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::select('id', 'name')->get(),
            'suppliers' => Supplier::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        if (auth()->user()->role === 'staff' || auth()->user()->role === 'auditor') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', "unique:products,sku,{$product->id}"],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:2048'],
            'existing_gallery' => ['nullable', 'array'],
            'existing_gallery.*' => ['string'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $product->toArray();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $existingGallery = $request->input('existing_gallery', []);
        $currentGallery = $product->gallery ?? [];
        
        // Delete removed images
        $removedImages = array_diff($currentGallery, $existingGallery);
        foreach ($removedImages as $img) {
            Storage::disk('public')->delete($img);
        }

        $newGallery = $existingGallery;
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $newGallery[] = $file->store('products', 'public');
            }
        }
        $validated['gallery'] = $newGallery;
        unset($validated['existing_gallery']);

        $product->update($validated);

        AuditLogService::log('update', 'products', "Updated product: {$product->name}", $oldValues, $product->fresh()->toArray());

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat menghapus produk.');
        }

        $oldValues = $product->toArray();
        $name = $product->name;

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->gallery) {
            foreach ($product->gallery as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $product->delete();

        AuditLogService::log('delete', 'products', "Deleted product: {$name}", $oldValues);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
