<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('staff can record stock in', function () {
    $product = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();

    $response = $this->actingAs($this->admin)
        ->from('/stock-transactions')
        ->post('/stock-transactions/store-in', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50,
            'transaction_date' => now()->toDateString(),
            'notes' => 'Test Stock In',
        ]);

    $response->assertRedirect('/stock-transactions');
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 50,
    ]);
});

test('staff can record stock out if sufficient', function () {
    $product = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();
    InventoryStock::create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 100,
    ]);

    $response = $this->actingAs($this->admin)
        ->from('/stock-transactions')
        ->post('/stock-transactions/store-out', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 40,
            'transaction_date' => now()->toDateString(),
        ]);

    $response->assertRedirect('/stock-transactions');
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 60,
    ]);
});

test('staff cannot record stock out if insufficient', function () {
    $product = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();
    InventoryStock::create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
    ]);

    $response = $this->actingAs($this->admin)
        ->from('/stock-transactions')
        ->post('/stock-transactions/store-out', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50,
            'transaction_date' => now()->toDateString(),
        ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
    ]);
});
