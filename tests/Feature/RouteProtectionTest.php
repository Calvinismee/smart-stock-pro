<?php

use App\Models\User;

test('TC-81: All protected routes require authentication', function () {
    $this->get('/')->assertRedirect('/login');
    $this->get('/products')->assertRedirect('/login');
    $this->post('/products')->assertRedirect('/login');
});

test('TC-82: Staff cannot bypass warehouse scope by manually changing request payload', function () {
    $warehouseA = \App\Models\Warehouse::factory()->create();
    $warehouseB = \App\Models\Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $warehouseA->id]);
    $product = \App\Models\Product::factory()->create();
    
    // Staff tries to POST stock-in to warehouse B, even though assigned to A
    $this->actingAs($staff)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouseB->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ])->assertStatus(403);
});

test('TC-83: Viewer cannot mutate data by manually sending POST/PUT/PATCH/DELETE requests', function () {
    $viewer = User::factory()->viewer()->create();
    $product = \App\Models\Product::factory()->create();
    
    $this->actingAs($viewer)->post('/products', [])->assertStatus(403);
    $this->actingAs($viewer)->patch('/products/' . $product->id, [])->assertStatus(403);
    $this->actingAs($viewer)->delete('/products/' . $product->id)->assertStatus(403);
    
    $this->actingAs($viewer)->post('/stock-transactions/store-in', [])->assertStatus(403);
    $this->actingAs($viewer)->post('/stock-transfers', [])->assertStatus(403);
    $this->actingAs($viewer)->post('/import/products', [])->assertStatus(403);
});
