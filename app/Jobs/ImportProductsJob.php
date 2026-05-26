<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportProductsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public int $userId
    ) {}

    public function handle(): void
    {
        $path = storage_path('app/private/' . $this->filePath);
        if (!file_exists($path)) {
            \App\Services\NotificationService::create(
                'import_failed', 'Import Gagal', "File import produk tidak ditemukan.", 'critical', $this->userId
            );
            return;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['xlsx', 'xls'])) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
            $spreadsheet = $reader->load($path);
            $rows = $spreadsheet->getActiveSheet()->toArray();
        } else {
            $rows = array_map('str_getcsv', file($path));
        }

        if (count($rows) < 2) {
            \App\Services\NotificationService::create(
                'import_failed', 'Import Produk Gagal', "File kosong atau hanya berisi header.", 'critical', $this->userId
            );
            @unlink($path);
            return;
        }

        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $required = ['sku','name','category','unit','purchase_price','selling_price','minimum_stock'];
        $missing = array_diff($required, $header);
        if (!empty($missing)) {
            \App\Services\NotificationService::create(
                'import_failed', 'Import Produk Gagal', 'Kolom wajib tidak ditemukan: '.implode(', ',$missing), 'critical', $this->userId
            );
            @unlink($path);
            return;
        }

        $success = 0; $failed = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = array_combine($header, array_pad($rows[$i], count($header), null));

            $v = \Illuminate\Support\Facades\Validator::make($row, [
                'sku'=>'required|string|unique:products,sku','name'=>'required|string',
                'category'=>'required|string','unit'=>'required|string',
                'purchase_price'=>'required|numeric|min:0','selling_price'=>'required|numeric|min:0',
                'minimum_stock'=>'required|integer|min:0',
            ]);

            if ($v->fails()) {
                $failed++;
                continue;
            }

            $category = \App\Models\Category::firstOrCreate(['name' => trim($row['category'])]);
            $supplier = !empty($row['supplier']) ? \App\Models\Supplier::firstOrCreate(['name' => trim($row['supplier'])]) : null;

            \App\Models\Product::create([
                'sku'=>$row['sku'],'name'=>$row['name'],'category_id'=>$category->id,
                'supplier_id'=>$supplier?->id,'unit'=>$row['unit'],
                'purchase_price'=>$row['purchase_price'],'selling_price'=>$row['selling_price'],
                'minimum_stock'=>$row['minimum_stock'],'description'=>$row['description']??null,'is_active'=>true,
            ]);
            $success++;
        }

        \App\Services\AuditLogService::log('import','products',"Background imported products: {$success} success, {$failed} failed");
        
        \App\Services\NotificationService::create(
            'import_success', 'Import Produk Selesai', "Berhasil: {$success} baris. Gagal: {$failed} baris.", 'info', $this->userId
        );

        @unlink($path);
    }
}
