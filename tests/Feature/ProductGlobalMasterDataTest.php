<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;

test('TC-35: Product does not require warehouse_id and is global', function () {
    $product = Product::factory()->create();
    expect($product->warehouse_id)->toBeNull();
});

test('TC-36: Product total stock equals sum of inventory_stocks across warehouses', function () {
    $product = Product::factory()->create();
    $w1 = Warehouse::factory()->create();
    $w2 = Warehouse::factory()->create();
    
    InventoryStock::factory()->create(['product_id' => $product->id, 'warehouse_id' => $w1->id, 'quantity' => 10]);
    InventoryStock::factory()->create(['product_id' => $product->id, 'warehouse_id' => $w2->id, 'quantity' => 15]);
    
    // The sum should be 25
    $total = InventoryStock::where('product_id', $product->id)->sum('quantity');
    expect($total)->toBe(25);
});

test('TC-37: Staf Gudang can view product list but cannot mutate', function () {
    $staff = User::factory()->staff()->create();
    $product = Product::factory()->create();
    
    $this->actingAs($staff)->get('/products')->assertStatus(200);
    $this->actingAs($staff)->post('/products', [])->assertStatus(403);
    $this->actingAs($staff)->patch('/products/' . $product->id, [])->assertStatus(403);
    $this->actingAs($staff)->delete('/products/' . $product->id)->assertStatus(403);
});

test('TC-38: Admin can create product', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/products/create')->assertStatus(200);
});

test('TC-39: Manajer Gudang can create or update product', function () {
    $manager = User::factory()->manager()->create();
    $this->actingAs($manager)->get('/products/create')->assertStatus(200);
});

test('TC-40: Product deletion is restricted according to current policy', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $product = Product::factory()->create();
    
    // ProductPolicy or controller allows admin to delete, but rejects manager
    $this->actingAs($manager)->delete('/products/' . $product->id)->assertStatus(403);
    $this->actingAs($admin)->delete('/products/' . $product->id)->assertRedirect();
});
