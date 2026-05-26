<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\StockTransaction;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MyWarehouseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $warehouseId = $user->warehouse_id;
        
        if (!$warehouseId) {
            abort(403, 'Anda tidak ditugaskan ke gudang tertentu (Tidak memiliki akses My Warehouse).');
        }

        $warehouse = \App\Models\Warehouse::find($warehouseId);

        $totalItems = InventoryStock::where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->count();

        $lowStockItems = InventoryStock::with('product:id,name,sku')
            ->where('warehouse_id', $warehouseId)
            ->join('products', 'inventory_stocks.product_id', '=', 'products.id')
            ->whereRaw('inventory_stocks.quantity <= GREATEST(inventory_stocks.minimum_stock, products.minimum_stock)')
            ->select('inventory_stocks.*')
            ->take(5)
            ->get();

        $recentStockIns = StockTransaction::with('product:id,name')
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'in')
            ->latest('transaction_date')
            ->take(5)
            ->get();

        $recentStockOuts = StockTransaction::with('product:id,name')
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'out')
            ->latest('transaction_date')
            ->take(5)
            ->get();

        $recentTransfers = StockTransfer::with(['product:id,name', 'destinationWarehouse:id,name'])
            ->where('source_warehouse_id', $warehouseId)
            ->latest('transfer_date')
            ->take(5)
            ->get();

        return Inertia::render('MyWarehouse/Index', [
            'warehouse' => $warehouse,
            'stats' => [
                'totalItems' => $totalItems,
                'lowStockCount' => count($lowStockItems),
            ],
            'lowStockItems' => $lowStockItems,
            'recentStockIns' => $recentStockIns,
            'recentStockOuts' => $recentStockOuts,
            'recentTransfers' => $recentTransfers,
        ]);
    }
}
