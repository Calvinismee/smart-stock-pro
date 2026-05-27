<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\StockBatch;
use App\Services\StockTransactionService;

beforeEach(function () {
    $this->product = Product::factory()->create();
    $this->warehouse = Warehouse::factory()->create();
    $this->admin = User::factory()->admin()->create();
    
    // Create Batch 1: 10 units, older date
    StockBatch::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'initial_quantity' => 10,
        'remaining_quantity' => 10,
        'arrived_at' => now()->subDays(10),
    ]);
    
    // Create Batch 2: 5 units, newer date
    StockBatch::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'initial_quantity' => 5,
        'remaining_quantity' => 5,
        'arrived_at' => now()->subDays(2),
    ]);
    
    InventoryStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 15,
    ]);
});

test('TC-56: Stock-out consumes oldest batch first (FIFO)', function () {
    $service = new StockTransactionService();
    $service->stockOut([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 12,
        'transaction_date' => now()->toDateString(),
        'notes' => 'FIFO Test',
    ], $this->admin->id);
    
    $batches = StockBatch::where('product_id', $this->product->id)
                ->where('warehouse_id', $this->warehouse->id)
                ->orderBy('arrived_at', 'asc')
                ->get();
                
    // Batch 1 should be fully consumed (10 -> 0)
    expect($batches[0]->remaining_quantity)->toEqual(0);
    // Batch 2 should be partially consumed (5 - 2 = 3)
    expect($batches[1]->remaining_quantity)->toEqual(3);
    
    // Overall inventory should be 3
    $stock = InventoryStock::where('product_id', $this->product->id)
                ->where('warehouse_id', $this->warehouse->id)
                ->first();
    expect($stock->quantity)->toEqual(3);
});

test('TC-57: FIFO does not consume batches from another warehouse', function () {
    $otherWarehouse = Warehouse::factory()->create();
    StockBatch::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $otherWarehouse->id,
        'initial_quantity' => 20,
        'remaining_quantity' => 20,
        'arrived_at' => now()->subDays(20), // Extremely old!
    ]);
    
    InventoryStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $otherWarehouse->id,
        'quantity' => 20,
    ]);
    
    $service = new StockTransactionService();
    $service->stockOut([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 5,
        'transaction_date' => now()->toDateString(),
        'notes' => 'FIFO Isolated Test',
    ], $this->admin->id);
    
    // The very old batch in otherWarehouse should NOT be touched
    $otherBatch = StockBatch::where('warehouse_id', $otherWarehouse->id)->first();
    expect($otherBatch->remaining_quantity)->toEqual(20);
});

test('TC-58: FIFO rejects stock-out if total remaining batch quantity is insufficient', function () {
    $this->actingAs($this->admin)->post('/stock-transactions/store-out', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 100, // We only have 15
        'transaction_date' => now()->toDateString(),
    ])->assertSessionHas('error');
});
