<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Notification;
use App\Services\NotificationService;

test('TC-69: Low-stock notification is created', function () {
    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['name' => 'Low Stock Product']);
    $warehouse = Warehouse::factory()->create(['name' => 'Test Warehouse']);
    
    NotificationService::lowStockAlert($product->name, $warehouse->name, 5, 10, $product->id, $warehouse->id);
    
    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin->id,
        'title' => 'Stok Rendah',
    ]);
});

test('TC-70: User can view notification list and mark as read', function () {
    $admin = User::factory()->admin()->create();
    
    $notification = Notification::create([
        'user_id' => $admin->id,
        'title' => 'Test',
        'message' => 'Test msg',
        'type' => 'info',
        'is_read' => false
    ]);
    
    $this->actingAs($admin)->get('/notifications')->assertStatus(200);
    
    $this->actingAs($admin)->patch("/notifications/{$notification->id}/read")->assertRedirect();
    
    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'is_read' => true,
    ]);
});
