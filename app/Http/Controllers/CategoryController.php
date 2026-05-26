<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($search = $request->input('search')) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        $sortField = $request->input('sort', 'name');
        $sortDir = $request->input('direction', 'asc');
        $query->orderBy($sortField, $sortDir);

        return Inertia::render('Categories/Index', [
            'categories' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create($validated);
        AuditLogService::log('create', 'categories', "Created category: {$category->name}");

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', "unique:categories,name,{$category->id}"],
            'description' => ['nullable', 'string'],
        ]);

        $old = $category->toArray();
        $category->update($validated);
        AuditLogService::log('update', 'categories', "Updated category: {$category->name}", $old, $category->fresh()->toArray());

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $old = $category->toArray();
        $category->delete();
        AuditLogService::log('delete', 'categories', "Deleted category: {$name}", $old);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
