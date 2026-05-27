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
                'status' => 'in_transit',
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // Decrease source stock
            $sourceStock->decrement('quantity', $data['quantity']);

            // FIFO Logic: Deduct from oldest batches first
            $qtyToDeduct = $data['quantity'];
            $batches = \App\Models\StockBatch::where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['source_warehouse_id'])
                ->where('remaining_quantity', '>', 0)
                ->orderBy('arrived_at', 'asc')
                ->lockForUpdate()
                ->get();

            $totalCost = 0;
            foreach ($batches as $batch) {
                if ($qtyToDeduct <= 0) break;

                $deducted = min($batch->remaining_quantity, $qtyToDeduct);
                $qtyToDeduct -= $deducted;
                $batch->decrement('remaining_quantity', $deducted);
                $totalCost += ($deducted * $batch->unit_cost);
            }

            if ($qtyToDeduct > 0) {
                \App\Models\StockBatch::create([
                    'product_id' => $data['product_id'],
                    'warehouse_id' => $data['source_warehouse_id'],
                    'stock_transaction_id' => null,
                    'initial_quantity' => 0,
                    'remaining_quantity' => -$qtyToDeduct,
                    'unit_cost' => \App\Models\Product::find($data['product_id'])->purchase_price,
                ]);
            }

            // Notify destination warehouse
            NotificationService::create(
                type: 'transfer_incoming',
                title: 'Transfer Barang Masuk',
                message: "Terdapat transfer masuk ({$code}) sejumlah {$data['quantity']} unit untuk produk ID {$data['product_id']}.",
                severity: 'info',
                relatedType: 'transfer',
                relatedId: $transfer->id,
                warehouseId: $data['destination_warehouse_id']
            );

            // Check minimum stock in source warehouse
            $product = Product::find($data['product_id']);
            $sourceWarehouse = Warehouse::find($data['source_warehouse_id']);
            $destWarehouse = Warehouse::find($data['destination_warehouse_id']);

            $freshStock = $sourceStock->fresh();
            $minStock = $freshStock->minimum_stock > 0 ? $freshStock->minimum_stock : $product->minimum_stock;

            if ($freshStock->quantity <= $minStock) {
                NotificationService::lowStockAlert(
                    $product->name,
                    $sourceWarehouse->name,
                    $freshStock->quantity,
                    $minStock,
                    $product->id,
                    $sourceWarehouse->id
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

    public function receive(StockTransfer $transfer, int $userId): StockTransfer
    {
        return DB::transaction(function () use ($transfer, $userId) {
            if ($transfer->status !== 'in_transit') {
                throw new \Exception('Transfer gudang tidak dalam status pengiriman (in_transit).');
            }

            // Increase destination stock
            $destStock = InventoryStock::firstOrCreate(
                ['product_id' => $transfer->product_id, 'warehouse_id' => $transfer->destination_warehouse_id],
                ['quantity' => 0]
            );
            $destStock->increment('quantity', $transfer->quantity);

            // Create new Stock Batch at destination
            $product = \App\Models\Product::find($transfer->product_id);
            \App\Models\StockBatch::create([
                'product_id' => $transfer->product_id,
                'warehouse_id' => $transfer->destination_warehouse_id,
                'stock_transaction_id' => null,
                'initial_quantity' => $transfer->quantity,
                'remaining_quantity' => $transfer->quantity,
                'unit_cost' => $product->purchase_price,
            ]);

            $transfer->update(['status' => 'completed']);
            
            // Notify source warehouse that the transfer has been received
            NotificationService::create(
                type: 'transfer_completed',
                title: 'Transfer Selesai Diterima',
                message: "Transfer ({$transfer->transfer_code}) sejumlah {$transfer->quantity} unit telah sukses diterima oleh gudang tujuan.",
                severity: 'info',
                relatedType: 'transfer',
                relatedId: $transfer->id,
                warehouseId: $transfer->source_warehouse_id
            );

            // Audit log
            AuditLogService::log(
                'receive',
                'stock_transfers',
                "Received {$transfer->quantity} units of product ID {$transfer->product_id} at destination warehouse ID {$transfer->destination_warehouse_id}",
                null,
                $transfer->fresh()->toArray()
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
