<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $purchasePrice = fake()->randomFloat(2, 50000, 5000000);

        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'name' => fake()->words(3, true),
            'category_id' => Category::inRandomOrder()->first()?->id ?? CategoryFactory::new(),
            'supplier_id' => Supplier::inRandomOrder()->first()?->id ?? SupplierFactory::new(),
            'description' => fake()->paragraph(),
            'unit' => fake()->randomElement(['pcs', 'unit', 'box', 'set']),
            'purchase_price' => $purchasePrice,
            'selling_price' => $purchasePrice * fake()->randomFloat(2, 1.1, 1.5),
            'minimum_stock' => fake()->numberBetween(5, 50),
            'image' => null,
            'is_active' => true,
        ];
    }
}
