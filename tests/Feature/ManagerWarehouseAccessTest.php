<?php
use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;

test('manager can create stock transactions for all warehouses', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $warehouse1 = Warehouse::factory()->create();
    $warehouse2 = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $response1 = $this->actingAs($manager)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse1->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);
    $response1->assertRedirect();

    $response2 = $this->actingAs($manager)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse2->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);
    $response2->assertRedirect();
});
