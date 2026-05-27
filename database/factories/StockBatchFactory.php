<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockBatchFactory extends Factory
{
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(10, 100);
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'stock_transaction_id' => null, // Typically null for seeders or initial stock
            'initial_quantity' => $qty,
            'remaining_quantity' => $qty,
            'unit_cost' => $this->faker->randomFloat(2, 10, 1000),
            'arrived_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
