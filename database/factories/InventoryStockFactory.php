<?php

namespace Database\Factories;

use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryStockFactory extends Factory
{
    protected $model = InventoryStock::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()?->id ?? ProductFactory::new(),
            'warehouse_id' => Warehouse::inRandomOrder()->first()?->id ?? WarehouseFactory::new(),
            'quantity' => fake()->numberBetween(0, 500),
        ];
    }
}
