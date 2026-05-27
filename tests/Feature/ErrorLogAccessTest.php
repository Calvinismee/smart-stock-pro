<?php

use App\Models\User;
use App\Models\ErrorLog;

test('TC-74: Admin can access error log page', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/error-logs')->assertStatus(200);
});

test('TC-75: Manajer Gudang, Staf Gudang, Viewer cannot access error log page', function () {
    $manager = User::factory()->manager()->create();
    $staff = User::factory()->staff()->create();
    $viewer = User::factory()->viewer()->create();
    
    $this->actingAs($manager)->get('/error-logs')->assertStatus(403);
    $this->actingAs($staff)->get('/error-logs')->assertStatus(403);
    $this->actingAs($viewer)->get('/error-logs')->assertStatus(403);
});


