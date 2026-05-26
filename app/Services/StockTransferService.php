<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function transfer(array $data, int $userId): StockTransfer
    {
        return DB::transaction(function () use ($data, $userId) {
            // Validate source != destination
            if ($data['source_warehouse_id'] == $data['destination_warehouse_id']) {
                throw new \Exception('Gudang asal dan tujuan tidak boleh sama.');
            }

            // Check source stock with lock
            $sourceStock = InventoryStock::where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['source_warehouse_id'])
                ->lockForUpdate()
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $data['quantity']) {
                throw new \Exception('Stok di gudang asal tidak mencukupi. Stok tersedia: ' . ($sourceStock?->quantity ?? 0));
            }

            // Generate transfer code
            $code = $this->generateCode();

            // Create transfer record
            $transfer = StockTransfer::create([
                'transfer_code' => $code,
                'product_id' => $data['product_id'],
                'source_warehouse_id' => $data['source_warehouse_id'],
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'quantity' => $data['quantity'],
                'transfer_date' => $data['transfer_date'],
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // Decrease source stock
            $sourceStock->decrement('quantity', $data['quantity']);

            // Increase destination stock
            $destStock = InventoryStock::firstOrCreate(
                ['product_id' => $data['product_id'], 'warehouse_id' => $data['destination_warehouse_id']],
                ['quantity' => 0]
            );
            $destStock->increment('quantity', $data['quantity']);

            // Check minimum stock in source warehouse
            $product = Product::find($data['product_id']);
            $sourceWarehouse = Warehouse::find($data['source_warehouse_id']);
            $destWarehouse = Warehouse::find($data['destination_warehouse_id']);

            if ($sourceStock->fresh()->quantity <= $product->minimum_stock) {
                NotificationService::lowStockAlert(
                    $product->name,
                    $sourceWarehouse->name,
                    $sourceStock->fresh()->quantity,
                    $product->minimum_stock,
                    $product->id,
                );
            }

            // Audit log
            AuditLogService::log(
                'transfer',
                'stock_transfers',
                "Transferred {$data['quantity']} units of {$product->name} from {$sourceWarehouse->name} to {$destWarehouse->name}",
                null,
                $transfer->toArray()
            );

            return $transfer;
        });
    }

    private function generateCode(): string
    {
        $prefix = 'TRF';
        $date = now()->format('Ymd');
        $last = StockTransfer::where('transfer_code', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('transfer_code')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->transfer_code, -4);
            $next = $lastNum + 1;
        } else {
            $next = 1;
        }

        return "{$prefix}-{$date}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
