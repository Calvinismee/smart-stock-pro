<?php

use App\Models\User;

test('TC-77: Admin and Manajer Gudang can access reports', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    
    $this->actingAs($admin)->get('/reports')->assertStatus(200);
    $this->actingAs($manager)->get('/reports')->assertStatus(200);
});

test('TC-78: Viewer can access reports in read-only mode', function () {
    $viewer = User::factory()->viewer()->create();
    $this->actingAs($viewer)->get('/reports')->assertStatus(200);
});

test('TC-79: Admin and Manajer Gudang can export reports', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    
    // Test export inventory endpoint
    $this->actingAs($admin)->get('/export/inventory')->assertStatus(200);
    $this->actingAs($manager)->get('/export/inventory')->assertStatus(200);
});

test('TC-80: Viewer cannot export reports', function () {
    $viewer = User::factory()->viewer()->create();
    $this->actingAs($viewer)->get('/export/inventory')->assertStatus(403);
});
