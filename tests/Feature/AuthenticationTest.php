<?php

use App\Models\User;

test('TC-01: User can view login page', function () {
    $this->get('/login')->assertStatus(200);
});

test('TC-02: Admin can login successfully', function () {
    $admin = User::factory()->admin()->create(['password' => bcrypt('Password123!')]);
    
    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'Password123!',
    ])->assertRedirect(route('dashboard'));
    
    $this->assertAuthenticatedAs($admin);
});

test('TC-03: Manajer Gudang can login successfully', function () {
    $manager = User::factory()->manager()->create(['password' => bcrypt('Password123!')]);
    
    $this->post('/login', [
        'email' => $manager->email,
        'password' => 'Password123!',
    ])->assertRedirect(route('dashboard'));
});

test('TC-04: Staf Gudang can login successfully and redirect to my warehouse', function () {
    $staff = User::factory()->staff()->create(['password' => bcrypt('Password123!')]);
    
    $this->post('/login', [
        'email' => $staff->email,
        'password' => 'Password123!',
    ])->assertRedirect(route('my-warehouse'));
});

test('TC-05: Viewer can login successfully', function () {
    $viewer = User::factory()->viewer()->create(['password' => bcrypt('Password123!')]);
    
    $this->post('/login', [
        'email' => $viewer->email,
        'password' => 'Password123!',
    ])->assertRedirect(route('dashboard'));
});

test('TC-06: User cannot login with invalid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('Password123!')]);
    
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'WrongPassword!123',
    ])->assertSessionHasErrors('password');
    
    $this->assertGuest();
});

test('TC-07: Inactive user cannot login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('Password123!'),
        'is_active' => false,
    ]);
    
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'Password123!',
    ])->assertSessionHasErrors('email');
    
    $this->assertGuest();
});

test('TC-08: User can logout successfully', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)->post('/logout')->assertRedirect('/login');
    $this->assertGuest();
});
