<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ErrorLogController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryStockController;
use App\Http\Controllers\MyWarehouseController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth', HandleInertiaRequests::class])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard / Root — Handles redirect based on role
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // My Warehouse — Staff (and potentially admin/manager)
    Route::middleware([RoleMiddleware::class . ':admin,manager,staff'])->group(function () {
        Route::get('/my-warehouse', [MyWarehouseController::class, 'index'])->name('my-warehouse');
    });

    // Inventory Stocks — All roles
    Route::get('/inventory-stocks', [InventoryStockController::class, 'index'])->name('inventory-stocks.index');

    // Notifications — all authenticated users
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Warehouse map — all authenticated users
    Route::get('/warehouse-map', function () {
        $warehouses = \App\Models\Warehouse::where('is_active', true)
            ->withSum('inventoryStocks', 'quantity')
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id, 'name' => $w->name, 'city' => $w->city,
                'latitude' => (float) $w->latitude, 'longitude' => (float) $w->longitude,
                'total_stock' => (int) ($w->inventory_stocks_sum_quantity ?? 0),
            ]);
        return \Inertia\Inertia::render('WarehouseMap', ['warehouses' => $warehouses]);
    })->name('warehouse-map');

    // Products Index — Read-only for all roles
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Products — Admin, Manager
    Route::middleware([RoleMiddleware::class . ':admin,manager'])->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
    });

    // Products Show — Read-only for all roles
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Categories/Suppliers — Read-only for all roles (or just admin/manager if not needed by auditor, but auditor might need them for filtering. We'll leave index if needed, but for now they are resource)
    // Wait, the prompt doesn't explicitly mention auditor accessing category index. It says "create/edit/delete category". We'll keep them as admin/manager.
    Route::middleware([RoleMiddleware::class . ':admin,manager'])->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
    });

    // Warehouses Index — Read-only for Auditor
    Route::middleware([RoleMiddleware::class . ':admin,manager,auditor'])->group(function () {
        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    });

    // Warehouses — Admin, Manager
    Route::middleware([RoleMiddleware::class . ':admin,manager'])->group(function () {
        Route::resource('warehouses', WarehouseController::class)->except(['index', 'show']);
    });

    // Warehouses Show — Read-only for Auditor
    Route::middleware([RoleMiddleware::class . ':admin,manager,auditor'])->group(function () {
        Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
    });

    // Stock transactions
    Route::middleware([RoleMiddleware::class . ':admin,manager,staff'])->group(function () {
        Route::get('/stock-transactions', [StockTransactionController::class, 'index'])->name('stock-transactions.index');
        Route::post('/stock-transactions/store-in', [StockTransactionController::class, 'storeIn'])->name('stock-transactions.store-in');
        Route::post('/stock-transactions/store-out', [StockTransactionController::class, 'storeOut'])->name('stock-transactions.store-out');
    });

    // Stock transfers
    Route::middleware([RoleMiddleware::class . ':admin,manager,staff'])->group(function () {
        Route::get('/stock-transfers', [StockTransferController::class, 'index'])->name('stock-transfers.index');
        Route::post('/stock-transfers', [StockTransferController::class, 'store'])->name('stock-transfers.store');
        Route::get('/stock-transfers/{stockTransfer}', [StockTransferController::class, 'show'])->name('stock-transfers.show');
    });

    // Import — Admin, Manager
    Route::middleware([RoleMiddleware::class . ':admin,manager'])->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import/products', [ImportController::class, 'importProducts'])->name('import.products');
        Route::post('/import/stock', [ImportController::class, 'importStock'])->name('import.stock');
        Route::get('/import/template/{type}', [ImportController::class, 'downloadTemplate'])->name('import.template');
    });

    // Reports Index — Admin, Manager, Auditor
    Route::middleware([RoleMiddleware::class . ':admin,manager,auditor'])->group(function () {
        Route::get('/reports', [ExportController::class, 'index'])->name('reports.index');
    });

    // Export — Admin, Manager
    Route::middleware([RoleMiddleware::class . ':admin,manager'])->group(function () {
        Route::get('/export/inventory', [ExportController::class, 'inventoryReport'])->name('export.inventory');
        Route::get('/export/low-stock', [ExportController::class, 'lowStockReport'])->name('export.low-stock');
        Route::get('/export/transactions', [ExportController::class, 'transactionReport'])->name('export.transactions');
        Route::get('/export/transfers', [ExportController::class, 'transferReport'])->name('export.transfers');
    });

    // Admin-only routes
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/error-logs', [ErrorLogController::class, 'index'])->name('error-logs.index');
        Route::patch('/error-logs/{errorLog}/resolve', [ErrorLogController::class, 'resolve'])->name('error-logs.resolve');
    });
});
