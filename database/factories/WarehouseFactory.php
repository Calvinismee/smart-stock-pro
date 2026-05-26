<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('WH-???')),
            'name' => 'Gudang ' . fake()->city(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-8, -1),
            'longitude' => fake()->longitude(105, 140),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
