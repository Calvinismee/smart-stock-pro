<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
            'role' => 'viewer',
            'warehouse_id' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function manager(): static
    {
        return $this->state(['role' => 'manager']);
    }

    public function staff(): static
    {
        return $this->state(fn() => [
            'role' => 'staff',
            'warehouse_id' => Warehouse::inRandomOrder()->first()?->id,
        ]);
    }

    public function viewer(): static
    {
        return $this->state(['role' => 'viewer']);
    }
}
