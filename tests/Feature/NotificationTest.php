<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryStock;

test('low stock triggers notification', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['minimum_stock' => 10]);
    $warehouse = Warehouse::factory()->create();

    InventoryStock::create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 15,
    ]);

    $this->actingAs($admin)
        ->post('/stock-transactions/store-out', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 10, // Remaining 5 < 10
            'transaction_date' => now()->toDateString(),
        ]);

    $this->assertDatabaseHas('notifications', [
        'title' => 'Stok Rendah',
    ]);
});
