<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\StockBatch;

beforeEach(function () {
    $this->product = Product::factory()->create();
    $this->warehouse = Warehouse::factory()->create();
    
    // Seed initial stock to allow stock-out
    InventoryStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 50,
    ]);
    
    StockBatch::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'initial_quantity' => 50,
        'remaining_quantity' => 50,
        'unit_cost' => 10000,
        'arrived_at' => now()->subDays(10),
    ]);
});

test('TC-51: Admin can create stock-out transaction', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/stock-transactions/store-out', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ])->assertRedirect();
});

test('TC-52: Staf Gudang cannot create stock-out from another warehouse', function () {
    $otherWarehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $otherWarehouse->id]);
    
    $this->actingAs($staff)->post('/stock-transactions/store-out', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ])->assertStatus(403);
});

test('TC-53: Stock-out with sufficient stock decreases inventory_stocks quantity', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/stock-transactions/store-out', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 20,
        'transaction_date' => now()->toDateString(),
    ])->assertRedirect();
    
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 30
    ]);
});

test('TC-54: Stock-out with insufficient stock is rejected', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/stock-transactions/store-out', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 100, // We only have 50
        'transaction_date' => now()->toDateString(),
    ])->assertSessionHas('error');
});

test('TC-55: Stock quantity must never become negative', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/stock-transactions/store-out', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 51, 
        'transaction_date' => now()->toDateString(),
    ])->assertSessionHas('error'); 
});
