<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ImportController extends Controller
{
    public function index()
    {
        return Inertia::render('Import/Index');
    }

    public function importProducts(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);
        $file = $request->file('file');
        
        $path = $file->storeAs('imports', 'products_'.time().'.'.$file->getClientOriginalExtension(), 'private');

        \App\Jobs\ImportProductsJob::dispatch($path, auth()->id());

        return back()->with('success', "File import produk berhasil diunggah. Proses sedang berjalan di latar belakang. Anda akan menerima notifikasi jika sudah selesai.");
    }

    public function importStock(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);
        $file = $request->file('file');
        
        $path = $file->storeAs('imports', 'stock_'.time().'.'.$file->getClientOriginalExtension(), 'private');

        \App\Jobs\ImportStockJob::dispatch($path, auth()->id());

        return back()->with('success', "File import stok berhasil diunggah. Proses sedang berjalan di latar belakang. Anda akan menerima notifikasi jika sudah selesai.");
    }

    public function downloadTemplate(string $type)
    {
        $templates = [
            'products' => "sku,name,category,supplier,unit,purchase_price,selling_price,minimum_stock,description\nSKU-SAMPLE-001,Product Name,Category Name,Supplier Name,pcs,100000,150000,10,Description here",
            'stock' => "sku,warehouse_code,quantity\nSKU-0001-SM,WH-JKT,100",
        ];

        if (!isset($templates[$type])) abort(404);

        return response($templates[$type], 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=template_{$type}.csv",
        ]);
    }
}
