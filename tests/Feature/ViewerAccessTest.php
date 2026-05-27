<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;

test('viewer can access dashboard', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $this->actingAs($viewer)->get('/')->assertStatus(200);
});

test('viewer can access products index and show', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $product = Product::factory()->create();

    $this->actingAs($viewer)->get('/products')->assertStatus(200);
    $this->actingAs($viewer)->get('/products/' . $product->id)->assertStatus(200);
});

test('viewer cannot create edit delete products', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $product = Product::factory()->create();

    $this->actingAs($viewer)->get('/products/create')->assertStatus(403);
    $this->actingAs($viewer)->get('/products/' . $product->id . '/edit')->assertStatus(403);
    $this->actingAs($viewer)->delete('/products/' . $product->id)->assertStatus(403);
});

test('viewer cannot mutate inventory data', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($viewer)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});

test('viewer can view reports but not export', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    
    $this->actingAs($viewer)->get('/reports')->assertStatus(200);
    $this->actingAs($viewer)->get('/export/inventory')->assertStatus(403);
});
