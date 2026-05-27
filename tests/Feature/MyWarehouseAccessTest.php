<?php

use App\Models\User;
use App\Models\Warehouse;

test('TC-17: Staf Gudang can access /my-warehouse', function () {
    $warehouse = Warehouse::factory()->create();
    $staff = User::factory()->create(['role' => 'staff', 'warehouse_id' => $warehouse->id]);
    $this->actingAs($staff)->get('/my-warehouse')->assertStatus(200);
});

test('TC-18: My Warehouse page only shows data from the assigned warehouse', function () {
    $jakarta = Warehouse::factory()->create(['name' => 'Jakarta']);
    $surabaya = Warehouse::factory()->create(['name' => 'Surabaya']);
    
    $staffJakarta = User::factory()->create(['role' => 'staff', 'warehouse_id' => $jakarta->id]);
    
    $response = $this->actingAs($staffJakarta)->get('/my-warehouse');
    $response->assertStatus(200);
    $response->assertSee('Jakarta');
    $response->assertDontSee('Surabaya');
});

test('TC-19: Viewer cannot access /my-warehouse', function () {
    $viewer = User::factory()->viewer()->create();
    $this->actingAs($viewer)->get('/my-warehouse')->assertStatus(403);
});

test('TC-20: Admin and Manajer Gudang behavior for /my-warehouse', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    
    // According to the app routing and MyWarehouseController, Admin & Manager 
    // will get 403 because they don't have a specific warehouse_id assigned.
    $this->actingAs($admin)->get('/my-warehouse')->assertStatus(403);
    $this->actingAs($manager)->get('/my-warehouse')->assertStatus(403);
});
