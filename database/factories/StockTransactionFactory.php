<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['in', 'out']),
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'user_id' => User::factory(),
            'quantity' => $this->faker->numberBetween(1, 50),
            'notes' => $this->faker->sentence(),
            'transaction_date' => $this->faker->date(),
        ];
    }
}
