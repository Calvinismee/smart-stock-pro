<?php
use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;

test('viewer cannot create mutate inventory data', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($viewer)->post('/stock-transactions/store-in', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertStatus(403);
});
