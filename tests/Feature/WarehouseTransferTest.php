<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\StockBatch;
use App\Models\StockTransfer;

beforeEach(function () {
    $this->product = Product::factory()->create();
    $this->sourceWarehouse = Warehouse::factory()->create();
    $this->destWarehouse = Warehouse::factory()->create();
    
    $this->admin = User::factory()->admin()->create();
    
    InventoryStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->sourceWarehouse->id,
        'quantity' => 50,
    ]);
    
    StockBatch::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->sourceWarehouse->id,
        'initial_quantity' => 50,
        'remaining_quantity' => 50,
    ]);
});

test('TC-59: Admin can transfer stock from any warehouse to any other warehouse', function () {
    $this->actingAs($this->admin)->post('/stock-transfers', [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destWarehouse->id,
        'quantity' => 10,
        'transfer_date' => now()->toDateString(),
    ])->assertRedirect();
});

test('TC-60: Staf Gudang can transfer only from assigned warehouse', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->sourceWarehouse->id]);
    
    $this->actingAs($staff)->post('/stock-transfers', [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destWarehouse->id,
        'quantity' => 5,
        'transfer_date' => now()->toDateString(),
    ])->assertRedirect();
});

test('TC-61: Staf Gudang cannot transfer from another warehouse', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->destWarehouse->id]); // Assigned to dest
    
    $this->actingAs($staff)->post('/stock-transfers', [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destWarehouse->id,
        'quantity' => 5,
        'transfer_date' => now()->toDateString(),
    ])->assertStatus(403);
});

test('TC-62: Viewer cannot create transfer', function () {
    $viewer = User::factory()->viewer()->create();
    
    $this->actingAs($viewer)->post('/stock-transfers', [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destWarehouse->id,
        'quantity' => 5,
        'transfer_date' => now()->toDateString(),
    ])->assertStatus(403);
});

test('TC-63: Transfer increases destination warehouse stock when received', function () {
    // Note: Depends on whether the app creates the transfer as "pending" or "completed".
    // If pending, we must test the "receive" endpoint.
    $transfer = StockTransfer::factory()->create([
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destWarehouse->id,
        'quantity' => 15,
        'status' => 'in_transit',
    ]);
    
    $this->actingAs($this->admin)->post("/stock-transfers/{$transfer->id}/receive")
         ->assertRedirect();
         
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->destWarehouse->id,
        'quantity' => 15
    ]);
});
