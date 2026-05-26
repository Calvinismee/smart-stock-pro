<?php
use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;

test('staff cannot create or edit products', function () {
    $warehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $warehouse->id]);
    
    $response = $this->actingAs($staff)->get('/products/create');
    $response->assertStatus(403);
    
    $responseStore = $this->actingAs($staff)->post('/products', []);
    $responseStore->assertStatus(403);
});

test('manager can edit products but not delete if restricted to admin', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $product = Product::factory()->create();
    
    $responseDel = $this->actingAs($manager)->delete('/products/' . $product->id);
    $responseDel->assertStatus(403);
});
