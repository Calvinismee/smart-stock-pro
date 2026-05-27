<?php

use App\Models\User;
use App\Models\Product;
use App\Models\AuditLog;
use App\Services\AuditLogService;

test('TC-71: Product create creates audit log', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)->post('/products', [
        'name' => 'Log Test Product',
        'sku' => 'LOG-001',
        'category_id' => \App\Models\Category::factory()->create()->id,
        'unit' => 'pcs',
        'purchase_price' => 100,
        'selling_price' => 150,
        'minimum_stock' => 5,
    ])->assertRedirect();
    
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'create',
        'module' => 'products',
    ]);
});

test('TC-72: Only Admin can access audit log page', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $staff = User::factory()->staff()->create();
    $viewer = User::factory()->viewer()->create();
    
    $this->actingAs($admin)->get('/audit-logs')->assertStatus(200);
    $this->actingAs($manager)->get('/audit-logs')->assertStatus(403);
    $this->actingAs($staff)->get('/audit-logs')->assertStatus(403);
    $this->actingAs($viewer)->get('/audit-logs')->assertStatus(403);
});

test('TC-73: Audit log stores required fields correctly via service', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin);
    AuditLogService::log('test_action', 'test_module', 'Test description');
    
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'test_action',
        'module' => 'test_module',
        'description' => 'Test description',
    ]);
});
