<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockTransactionService
{
    public function stockIn(array $data, int $userId): StockTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            // Generate transaction code
            $code = $this->generateCode('in');

            // Create transaction
            $transaction = StockTransaction::create([
                'transaction_code' => $code,
                'type' => 'in',
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'quantity' => $data['quantity'],
                'transaction_date' => $data['transaction_date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // Update or create inventory stock
            $stock = InventoryStock::firstOrCreate(
                ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']],
                ['quantity' => 0]
            );
            $stock->increment('quantity', $data['quantity']);

            // Audit log
            $product = Product::find($data['product_id']);
            $warehouse = Warehouse::find($data['warehouse_id']);
            AuditLogService::log(
                'stock_in',
                'stock_transactions',
                "Stock in {$data['quantity']} units of {$product->name} to {$warehouse->name}",
                null,
                $transaction->toArray()
            );

            return $transaction;
        });
    }

    public function stockOut(array $data, int $userId): StockTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            // Check stock availability
            $stock = InventoryStock::where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->quantity < $data['quantity']) {
                throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . ($stock?->quantity ?? 0));
            }

            // Generate transaction code
            $code = $this->generateCode('out');

            // Create transaction
            $transaction = StockTransaction::create([
                'transaction_code' => $code,
                'type' => 'out',
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => null,
                'quantity' => $data['quantity'],
                'transaction_date' => $data['transaction_date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // Decrease stock
            $stock->decrement('quantity', $data['quantity']);

            // Check minimum stock threshold
            $product = Product::find($data['product_id']);
            $warehouse = Warehouse::find($data['warehouse_id']);

            if ($stock->fresh()->quantity <= $product->minimum_stock) {
                NotificationService::lowStockAlert(
                    $product->name,
                    $warehouse->name,
                    $stock->fresh()->quantity,
                    $product->minimum_stock,
                    $product->id,
                );
            }

            // Audit log
            AuditLogService::log(
                'stock_out',
                'stock_transactions',
                "Stock out {$data['quantity']} units of {$product->name} from {$warehouse->name}",
                null,
                $transaction->toArray()
            );

            return $transaction;
        });
    }

    private function generateCode(string $type): string
    {
        $prefix = $type === 'in' ? 'TRX-IN' : 'TRX-OUT';
        $date = now()->format('Ymd');
        $last = StockTransaction::where('transaction_code', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('transaction_code')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->transaction_code, -4);
            $next = $lastNum + 1;
        } else {
            $next = 1;
        }

        return "{$prefix}-{$date}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
