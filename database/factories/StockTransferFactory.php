<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transfer_code' => 'TRF-' . $this->faker->unique()->numerify('#####'),
            'product_id' => Product::factory(),
            'source_warehouse_id' => Warehouse::factory(),
            'destination_warehouse_id' => Warehouse::factory(),
            'created_by' => User::factory(),
            'quantity' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->randomElement(['in_transit', 'completed', 'cancelled']),
            'transfer_date' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
