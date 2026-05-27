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

test('admin can view all warehouse stock', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)->get('/inventory-stocks');
    
    $response->assertStatus(200);
    $page = $response->viewData('page');
    $stocks = $page['props']['stocks']['data'];
    
    expect(count($stocks))->toBe(2);
});

test('manager can view all warehouse stock', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    
    $response = $this->actingAs($manager)->get('/inventory-stocks');
    
    $response->assertStatus(200);
    $page = $response->viewData('page');
    $stocks = $page['props']['stocks']['data'];
    
    expect(count($stocks))->toBe(2);
});

test('staff can only view assigned warehouse stock', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouseA->id]);
    
    $response = $this->actingAs($staff)->get('/inventory-stocks');
    
    $response->assertStatus(200);
    $page = $response->viewData('page');
    $stocks = $page['props']['stocks']['data'];
    
    expect(count($stocks))->toBe(1);
    expect($stocks[0]['warehouse_id'])->toBe($this->warehouseA->id);
});

test('viewer can view all warehouse stock', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    
    $response = $this->actingAs($viewer)->get('/inventory-stocks');
    
    $response->assertStatus(200);
    $page = $response->viewData('page');
    $stocks = $page['props']['stocks']['data'];
    
    expect(count($stocks))->toBe(2);
});
