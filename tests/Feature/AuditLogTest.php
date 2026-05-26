<?php

use App\Models\User;

test('creating category creates audit log', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
         ->post('/categories', [
             'name' => 'New Category For Audit',
         ]);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'create',
        'module' => 'categories',
    ]);
});
