<?php
use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;

test('staf gudang can transfer from assigned warehouse to other warehouse', function () {
    $staffWarehouse = Warehouse::factory()->create();
    $otherWarehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $staffWarehouse->id]);
    $product = Product::factory()->create();
    InventoryStock::create(['product_id' => $product->id, 'warehouse_id' => $staffWarehouse->id, 'quantity' => 50]);

    $response = $this->actingAs($staff)->post('/stock-transfers', [
        'product_id' => $product->id,
        'source_warehouse_id' => $staffWarehouse->id,
        'destination_warehouse_id' => $otherWarehouse->id,
        'quantity' => 10,
        'transfer_date' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('stock_transfers', ['source_warehouse_id' => $staffWarehouse->id]);
});

test('staf gudang cannot transfer from other warehouse', function () {
    $staffWarehouse = Warehouse::factory()->create();
    $otherWarehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $staffWarehouse->id]);
    $product = Product::factory()->create();
    InventoryStock::create(['product_id' => $product->id, 'warehouse_id' => $otherWarehouse->id, 'quantity' => 50]);

    $response = $this->actingAs($staff)->post('/stock-transfers', [
        'product_id' => $product->id,
        'source_warehouse_id' => $otherWarehouse->id,
        'destination_warehouse_id' => $staffWarehouse->id,
        'quantity' => 10,
        'transfer_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});
