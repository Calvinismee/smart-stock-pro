<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportStockJob implements ShouldQueue
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
                'import_failed', 'Import Stok Gagal', "File import stok tidak ditemukan.", 'critical', $this->userId
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
                'import_failed', 'Import Stok Gagal', "File kosong atau hanya berisi header.", 'critical', $this->userId
            );
            @unlink($path);
            return;
        }

        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $success = 0; $failed = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = array_combine($header, array_pad($rows[$i], count($header), null));
            $product = \App\Models\Product::where('sku', $row['sku'] ?? '')->first();
            $warehouse = \App\Models\Warehouse::where('code', $row['warehouse_code'] ?? '')->first();

            if (!$product || !$warehouse || !is_numeric($row['quantity'] ?? null)) {
                $failed++;
                continue;
            }

            \App\Models\InventoryStock::updateOrCreate(
                ['product_id'=>$product->id, 'warehouse_id'=>$warehouse->id],
                ['quantity'=>max(0, (int)$row['quantity'])]
            );
            $success++;
        }

        \App\Services\AuditLogService::log('import','inventory_stocks',"Background imported stock: {$success} success, {$failed} failed");
        
        \App\Services\NotificationService::create(
            'import_success', 'Import Stok Selesai', "Berhasil: {$success} baris. Gagal: {$failed} baris.", 'info', $this->userId
        );

        @unlink($path);
    }
}
