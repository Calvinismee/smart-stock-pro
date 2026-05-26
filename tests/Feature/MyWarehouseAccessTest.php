<?php

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->warehouseA = Warehouse::factory()->create(['name' => 'Gudang A']);
    $this->warehouseB = Warehouse::factory()->create(['name' => 'Gudang B']);
    $this->product = Product::factory()->create(['category_id' => $this->category->id]);
    
    InventoryStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouseA->id,
        'quantity' => 50,
    ]);
    
    InventoryStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouseB->id,
        'quantity' => 100,
    ]);
});

test('staff can access my-warehouse', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouseA->id]);
    
    $this->actingAs($staff)
        ->get('/my-warehouse')
        ->assertStatus(200)
        ->assertSee('Gudang A');
});

test('staff only sees assigned warehouse data', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouseA->id]);
    
    $response = $this->actingAs($staff)->get('/my-warehouse');
    
    $response->assertStatus(200);
    $page = $response->viewData('page');
    $warehouse = $page['props']['warehouse'];
    $stats = $page['props']['stats'];
    
    expect($warehouse['id'])->toBe($this->warehouseA->id);
    expect($stats['totalItems'])->toBe(1);
});

test('admin without warehouse cannot access my-warehouse', function () {
    $admin = User::factory()->create(['role' => 'admin', 'warehouse_id' => null]);
    
    $this->actingAs($admin)
        ->get('/my-warehouse')
        ->assertStatus(403);
});
