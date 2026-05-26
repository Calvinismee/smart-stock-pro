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
        $ext = $file->getClientOriginalExtension();

        if (in_array($ext, ['xlsx', 'xls'])) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
        } else {
            $rows = array_map('str_getcsv', file($file->getRealPath()));
        }

        if (count($rows) < 2) return back()->with('error','File kosong atau hanya berisi header.');

        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $required = ['sku','name','category','unit','purchase_price','selling_price','minimum_stock'];
        $missing = array_diff($required, $header);
        if (!empty($missing)) return back()->with('error','Kolom wajib tidak ditemukan: '.implode(', ',$missing));

        $success = 0; $failed = 0; $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = array_combine($header, array_pad($rows[$i], count($header), null));

            $v = Validator::make($row, [
                'sku'=>'required|string|unique:products,sku','name'=>'required|string',
                'category'=>'required|string','unit'=>'required|string',
                'purchase_price'=>'required|numeric|min:0','selling_price'=>'required|numeric|min:0',
                'minimum_stock'=>'required|integer|min:0',
            ]);

            if ($v->fails()) {
                $failed++;
                $errors[] = ['row'=>$i+1, 'errors'=>$v->errors()->all()];
                continue;
            }

            $category = Category::firstOrCreate(['name' => trim($row['category'])]);
            $supplier = !empty($row['supplier']) ? Supplier::firstOrCreate(['name' => trim($row['supplier'])]) : null;

            Product::create([
                'sku'=>$row['sku'],'name'=>$row['name'],'category_id'=>$category->id,
                'supplier_id'=>$supplier?->id,'unit'=>$row['unit'],
                'purchase_price'=>$row['purchase_price'],'selling_price'=>$row['selling_price'],
                'minimum_stock'=>$row['minimum_stock'],'description'=>$row['description']??null,'is_active'=>true,
            ]);
            $success++;
        }

        AuditLogService::log('import','products',"Imported products: {$success} success, {$failed} failed");

        return back()->with('success',"Import selesai: {$success} berhasil, {$failed} gagal.")->with('import_errors', $errors);
    }

    public function importStock(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();

        if (in_array($ext, ['xlsx', 'xls'])) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
        } else {
            $rows = array_map('str_getcsv', file($file->getRealPath()));
        }

        if (count($rows) < 2) return back()->with('error','File kosong.');

        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $success = 0; $failed = 0; $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = array_combine($header, array_pad($rows[$i], count($header), null));
            $product = Product::where('sku', $row['sku'] ?? '')->first();
            $warehouse = Warehouse::where('code', $row['warehouse_code'] ?? '')->first();

            if (!$product || !$warehouse || !is_numeric($row['quantity'] ?? null)) {
                $failed++;
                $errors[] = ['row'=>$i+1, 'errors'=>['SKU atau kode gudang tidak valid']];
                continue;
            }

            InventoryStock::updateOrCreate(
                ['product_id'=>$product->id, 'warehouse_id'=>$warehouse->id],
                ['quantity'=>max(0, (int)$row['quantity'])]
            );
            $success++;
        }

        AuditLogService::log('import','inventory_stocks',"Imported stock: {$success} success, {$failed} failed");
        return back()->with('success',"Import stok selesai: {$success} berhasil, {$failed} gagal.");
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
