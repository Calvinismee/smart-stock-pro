<?php

use App\Models\User;

test('viewer cannot access user management', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    
    $this->actingAs($viewer)
         ->get('/users')
         ->assertStatus(403);
});

test('staff cannot access user management', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    
    $this->actingAs($staff)
         ->get('/users')
         ->assertStatus(403);
});

test('manager cannot access user management', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    
    $this->actingAs($manager)
         ->get('/users')
         ->assertStatus(403);
});

test('admin can access user management', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $this->actingAs($admin)
         ->get('/users')
         ->assertStatus(200);
});

test('staff cannot access warehouse management', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    
    $this->actingAs($staff)
         ->get('/warehouses')
         ->assertStatus(403);
});

test('manager can access warehouse management', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    
    $this->actingAs($manager)
         ->get('/warehouses')
         ->assertStatus(200);
});
