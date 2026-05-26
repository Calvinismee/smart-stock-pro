<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;

test('auditor can access dashboard', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $this->actingAs($auditor)->get('/')->assertStatus(200);
});

test('auditor can access products index and show', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $product = Product::factory()->create();

    $this->actingAs($auditor)->get('/products')->assertStatus(200);
    $this->actingAs($auditor)->get('/products/' . $product->id)->assertStatus(200);
});

test('auditor cannot create edit delete products', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $product = Product::factory()->create();

    $this->actingAs($auditor)->get('/products/create')->assertStatus(403);
    $this->actingAs($auditor)->get('/products/' . $product->id . '/edit')->assertStatus(403);
    $this->actingAs($auditor)->delete('/products/' . $product->id)->assertStatus(403);
});

test('auditor cannot mutate inventory data', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($auditor)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});

test('auditor can view reports but not export', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    
    $this->actingAs($auditor)->get('/reports')->assertStatus(200);
    $this->actingAs($auditor)->get('/export/inventory')->assertStatus(403);
});
