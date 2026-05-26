<?php
use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;

test('staf gudang can create stock in for assigned warehouse', function () {
    $warehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $warehouse->id]);
    $product = Product::factory()->create();

    $response = $this->actingAs($staff)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('stock_transactions', ['warehouse_id' => $warehouse->id]);
});

test('staf gudang cannot create stock in for other warehouse', function () {
    $staffWarehouse = Warehouse::factory()->create();
    $otherWarehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $staffWarehouse->id]);
    $product = Product::factory()->create();

    $response = $this->actingAs($staff)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $otherWarehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});

test('staf gudang can create stock out from assigned warehouse', function () {
    $warehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $warehouse->id]);
    $product = Product::factory()->create();
    InventoryStock::create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 50]);

    $response = $this->actingAs($staff)->post('/stock-transactions/store-out', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('stock_transactions', ['warehouse_id' => $warehouse->id, 'type' => 'out']);
});

test('staf gudang cannot create stock out from other warehouse', function () {
    $staffWarehouse = Warehouse::factory()->create();
    $otherWarehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $staffWarehouse->id]);
    $product = Product::factory()->create();
    InventoryStock::create(['product_id' => $product->id, 'warehouse_id' => $otherWarehouse->id, 'quantity' => 50]);

    $response = $this->actingAs($staff)->post('/stock-transactions/store-out', [
        'product_id' => $product->id,
        'warehouse_id' => $otherWarehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});
