<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;

test('admin can import products via csv', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $csvContent = "sku,name,category,unit,purchase_price,selling_price,minimum_stock\nTEST-001,Test Product,Test Category,pcs,100,200,10";
    $file = UploadedFile::fake()->createWithContent('products.csv', $csvContent);

    $response = $this->actingAs($admin)
        ->post('/import/products', [
            'file' => $file
        ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);
});

test('admin can import stock via csv', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = \App\Models\Product::factory()->create(['sku' => 'TEST-002']);
    $warehouse = \App\Models\Warehouse::factory()->create(['code' => 'WH-01']);
    
    $csvContent = "sku,warehouse_code,quantity\nTEST-002,WH-01,150";
    $file = UploadedFile::fake()->createWithContent('stock.csv', $csvContent);

    $response = $this->actingAs($admin)
        ->post('/import/stock', [
            'file' => $file
        ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('inventory_stocks', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 150
    ]);
});
