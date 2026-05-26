<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryStockController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryStock::with(['product.category', 'warehouse']);

        $user = auth()->user();
        if ($user->role === 'staff') {
            $query->where('warehouse_id', $user->warehouse_id);
        }

        $joined = false;

        if ($search = $request->input('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($warehouseId = $request->input('warehouse_id')) {
            if ($user->role !== 'staff') {
                $query->where('warehouse_id', $warehouseId);
            }
        }

        if ($status = $request->input('status')) {
            $query->join('products', 'inventory_stocks.product_id', '=', 'products.id')
                  ->select('inventory_stocks.*');
            $joined = true;
            
            if ($status === 'kritis') {
                $query->where('inventory_stocks.quantity', 0);
            } elseif ($status === 'menipis') {
                $query->where('inventory_stocks.quantity', '>', 0)
                      ->whereRaw('inventory_stocks.quantity <= GREATEST(inventory_stocks.minimum_stock, products.minimum_stock)');
            } elseif ($status === 'aman') {
                $query->whereRaw('inventory_stocks.quantity > GREATEST(inventory_stocks.minimum_stock, products.minimum_stock)');
            }
        }

        $sortField = $request->input('sort', 'updated_at');
        $sortDir = $request->input('direction', 'desc');
        
        if (in_array($sortField, ['quantity', 'updated_at'])) {
            if ($joined) {
                $query->orderBy("inventory_stocks.{$sortField}", $sortDir);
            } else {
                $query->orderBy($sortField, $sortDir);
            }
        } else {
            if ($joined) {
                $query->orderBy('inventory_stocks.updated_at', 'desc');
            } else {
                $query->orderBy('updated_at', 'desc');
            }
        }

        $stocks = $query->paginate(15)->withQueryString();

        $warehousesQuery = Warehouse::where('is_active', true)->select('id', 'name');
        if ($user->role === 'staff') {
            $warehousesQuery->where('id', $user->warehouse_id);
        }

        return Inertia::render('InventoryStocks/Index', [
            'stocks' => $stocks,
            'categories' => Category::select('id', 'name')->get(),
            'warehouses' => $warehousesQuery->get(),
            'filters' => $request->only(['search', 'category_id', 'warehouse_id', 'status', 'sort', 'direction']),
        ]);
    }
}
