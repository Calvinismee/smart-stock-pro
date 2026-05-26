<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_warehouses' => Warehouse::where('is_active', true)->count(),
            'total_suppliers' => Supplier::count(),
            'total_stock' => InventoryStock::sum('quantity'),
            'low_stock_count' => DB::table('inventory_stocks')
                ->join('products', 'inventory_stocks.product_id', '=', 'products.id')
                ->whereColumn('inventory_stocks.quantity', '<=', 'products.minimum_stock')
                ->count(),
            'inventory_value' => DB::table('inventory_stocks')
                ->join('products', 'inventory_stocks.product_id', '=', 'products.id')
                ->selectRaw('SUM(inventory_stocks.quantity * products.selling_price) as total')
                ->value('total') ?? 0,
        ];

        // Stock in/out trends (last 30 days)
        $stockTrends = StockTransaction::selectRaw("
                DATE(transaction_date) as date,
                type,
                SUM(quantity) as total
            ")
            ->where('transaction_date', '>=', now()->subDays(30))
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'stock_in' => $group->where('type', 'in')->sum('total'),
                    'stock_out' => $group->where('type', 'out')->sum('total'),
                ];
            })
            ->values();

        // Recent transactions
        $recentTransactions = StockTransaction::with(['product:id,name', 'warehouse:id,name', 'creator:id,name'])
            ->latest()
            ->take(5)
            ->get();

        // Recent transfers
        $recentTransfers = StockTransfer::with([
            'product:id,name',
            'sourceWarehouse:id,name',
            'destinationWarehouse:id,name',
            'creator:id,name',
        ])
            ->latest()
            ->take(5)
            ->get();

        // Low stock products
        $lowStockProducts = DB::table('inventory_stocks')
            ->join('products', 'inventory_stocks.product_id', '=', 'products.id')
            ->join('warehouses', 'inventory_stocks.warehouse_id', '=', 'warehouses.id')
            ->whereColumn('inventory_stocks.quantity', '<=', 'products.minimum_stock')
            ->select(
                'products.name as product_name',
                'products.sku',
                'warehouses.name as warehouse_name',
                'inventory_stocks.quantity',
                'products.minimum_stock'
            )
            ->orderBy('inventory_stocks.quantity')
            ->take(10)
            ->get();

        // Warehouse map data
        $warehouseMapData = Warehouse::where('is_active', true)
            ->withSum('inventoryStocks', 'quantity')
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'city' => $w->city,
                'latitude' => (float) $w->latitude,
                'longitude' => (float) $w->longitude,
                'total_stock' => (int) ($w->inventory_stocks_sum_quantity ?? 0),
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'stockTrends' => $stockTrends,
            'recentTransactions' => $recentTransactions,
            'recentTransfers' => $recentTransfers,
            'lowStockProducts' => $lowStockProducts,
            'warehouseMapData' => $warehouseMapData,
        ]);
    }
}
