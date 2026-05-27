<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\StockBatch;
use App\Models\AuditLog;

beforeEach(function () {
    $this->product = Product::factory()->create();
    $this->warehouse = Warehouse::factory()->create();
});

test('TC-45: Admin can create stock-in transaction for any warehouse', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/stock-transactions/store-in', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
        'notes' => 'Test In'
    ])->assertRedirect();
});

test('TC-46: Staf Gudang can create stock-in only for assigned warehouse', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouse->id]);
    $this->actingAs($staff)->post('/stock-transactions/store-in', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ])->assertRedirect();
});

test('TC-47: Staf Gudang cannot create stock-in for another warehouse', function () {
    $otherWarehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouse->id]);
    
    $this->actingAs($staff)->post('/stock-transactions/store-in', [
        'product_id' => $this->product->id,
        'warehouse_id' => $otherWarehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ])->assertStatus(403);
});

test('TC-48: Creating stock-in increases inventory_stocks quantity and creates batch', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)->post('/stock-transactions/store-in', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 25,
        'transaction_date' => now()->toDateString(),
    ])->assertRedirect();
    
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 25
    ]);
    
    $this->assertDatabaseHas('stock_batches', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'initial_quantity' => 25
    ]);
});

test('TC-49: Creating stock-in creates an audit log', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)->post('/stock-transactions/store-in', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ])->assertRedirect();
    
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'stock_in',
        'module' => 'stock_transactions'
    ]);
});

test('TC-50: Invalid quantity zero or negative is rejected', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/stock-transactions/store-in', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 0,
        'transaction_date' => now()->toDateString(),
    ])->assertSessionHasErrors('quantity');
});
