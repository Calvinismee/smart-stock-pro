<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Category;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('admin can view products index', function () {
    Product::factory(3)->create();
    
    $this->actingAs($this->admin)
         ->get('/products')
         ->assertStatus(200);
});

test('admin can create product', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
         ->post('/products', [
             'sku' => 'SKU-TEST-001',
             'name' => 'Test Product',
             'category_id' => $category->id,
             'unit' => 'pcs',
             'minimum_stock' => 10,
             'purchase_price' => 1000,
             'selling_price' => 1500,
             'is_active' => true,
         ]);

    $response->assertRedirect('/products');
    $this->assertDatabaseHas('products', ['sku' => 'SKU-TEST-001']);
});

test('admin can update product', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->admin)
         ->put('/products/' . $product->id, array_merge($product->toArray(), [
             'name' => 'Updated Name',
         ]));

    $response->assertRedirect('/products');
    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
});

test('admin can delete product', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->admin)
         ->delete('/products/' . $product->id);

    $response->assertRedirect('/products');
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
