<?php

use App\Models\User;
use App\Models\Warehouse;

beforeEach(function () {
    $this->warehouse = Warehouse::factory()->create();
});

test('admin can access dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)
        ->get('/')
        ->assertStatus(200);
});

test('manager can access dashboard', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager)
        ->get('/')
        ->assertStatus(200);
});

test('staff cannot access dashboard', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouse->id]);
    $this->actingAs($staff)
        ->get('/')
        ->assertRedirect(route('my-warehouse'));
});

test('staff is redirected to my-warehouse after login', function () {
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $this->warehouse->id]);
    
    $this->post('/login', [
        'email' => $staff->email,
        'password' => 'password',
    ])->assertRedirect(route('my-warehouse'));
});

test('viewer is redirected to reports after login', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    
    $this->post('/login', [
        'email' => $viewer->email,
        'password' => 'password',
    ])->assertRedirect(route('reports.index'));
});
