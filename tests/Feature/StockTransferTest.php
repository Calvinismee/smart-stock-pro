<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('can transfer stock between warehouses', function () {
    $product = Product::factory()->create();
    $source = Warehouse::factory()->create();
    $dest = Warehouse::factory()->create();

    InventoryStock::create([
        'product_id' => $product->id,
        'warehouse_id' => $source->id,
        'quantity' => 100,
    ]);

    $response = $this->actingAs($this->admin)
        ->from('/stock-transfers')
        ->post('/stock-transfers', [
            'product_id' => $product->id,
            'source_warehouse_id' => $source->id,
            'destination_warehouse_id' => $dest->id,
            'quantity' => 30,
            'transfer_date' => now()->toDateString(),
        ]);

    $response->assertRedirect('/stock-transfers');
    
    $this->assertDatabaseHas('inventory_stocks', [
        'warehouse_id' => $source->id,
        'quantity' => 70,
    ]);
    
    $transfer = \App\Models\StockTransfer::first();
    expect($transfer->status)->toBe('in_transit');

    $this->post('/stock-transfers/' . $transfer->id . '/receive')->assertRedirect();

    $this->assertDatabaseHas('inventory_stocks', [
        'warehouse_id' => $dest->id,
        'quantity' => 30,
    ]);
    expect($transfer->fresh()->status)->toBe('completed');
});

test('cannot transfer more than available stock', function () {
    $product = Product::factory()->create();
    $source = Warehouse::factory()->create();
    $dest = Warehouse::factory()->create();

    InventoryStock::create([
        'product_id' => $product->id,
        'warehouse_id' => $source->id,
        'quantity' => 20,
    ]);

    $response = $this->actingAs($this->admin)
        ->from('/stock-transfers')
        ->post('/stock-transfers', [
            'product_id' => $product->id,
            'source_warehouse_id' => $source->id,
            'destination_warehouse_id' => $dest->id,
            'quantity' => 30,
            'transfer_date' => now()->toDateString(),
        ]);

    $response->assertSessionHas('error');
    
    $this->assertDatabaseHas('inventory_stocks', [
        'warehouse_id' => $source->id,
        'quantity' => 20,
    ]);
});
