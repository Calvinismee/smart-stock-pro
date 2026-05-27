<?php

use App\Models\User;

test('TC-09: Admin can access /dashboard', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/')->assertStatus(200);
});

test('TC-10: Manajer Gudang can access /dashboard', function () {
    $manager = User::factory()->manager()->create();
    $this->actingAs($manager)->get('/')->assertStatus(200);
});

test('TC-11: Viewer can access /dashboard in read-only mode', function () {
    $viewer = User::factory()->viewer()->create();
    $this->actingAs($viewer)->get('/')->assertStatus(200);
});

test('TC-12: Staf Gudang cannot access /dashboard and is redirected', function () {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff)->get('/')->assertRedirect(route('my-warehouse'));
});

test('TC-13: Admin is redirected to /dashboard after login', function () {
    $admin = User::factory()->admin()->create(['password' => bcrypt('Password123!')]);
    $this->post('/login', ['email' => $admin->email, 'password' => 'Password123!'])
         ->assertRedirect(route('dashboard'));
});

test('TC-14: Manajer Gudang is redirected to /dashboard after login', function () {
    $manager = User::factory()->manager()->create(['password' => bcrypt('Password123!')]);
    $this->post('/login', ['email' => $manager->email, 'password' => 'Password123!'])
         ->assertRedirect(route('dashboard'));
});

test('TC-15: Viewer is redirected to /dashboard after login', function () {
    $viewer = User::factory()->viewer()->create(['password' => bcrypt('Password123!')]);
    $this->post('/login', ['email' => $viewer->email, 'password' => 'Password123!'])
         ->assertRedirect(route('dashboard'));
});

test('TC-16: Staf Gudang is redirected to /my-warehouse after login', function () {
    $staff = User::factory()->staff()->create(['password' => bcrypt('Password123!')]);
    $this->post('/login', ['email' => $staff->email, 'password' => 'Password123!'])
         ->assertRedirect(route('my-warehouse'));
});
