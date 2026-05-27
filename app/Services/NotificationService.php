<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\LowStockAlertEvent;

class NotificationService
{
    public static function create(
        string $type,
        string $title,
        string $message,
        string $severity = 'info',
        ?int $userId = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        ?int $warehouseId = null
    ): void {
        if ($userId) {
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'severity' => $severity,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
            ]);
            
            if (in_array($type, ['low_stock', 'transfer_incoming', 'transfer_completed', 'new_product'])) {
                LowStockAlertEvent::dispatch($notification, $userId);
            }
        } else {
            // Notify all admins, managers, and conditionally staff
            $users = User::where('is_active', true)
                ->where(function ($q) use ($warehouseId, $type) {
                    if ($type === 'new_product') {
                        // Global notification for everyone
                        $q->whereNotNull('id');
                    } else {
                        $q->whereIn('role', ['admin', 'manager']);
                        if ($warehouseId) {
                            $q->orWhere(function ($sq) use ($warehouseId) {
                                $sq->where('role', 'staff')->where('warehouse_id', $warehouseId);
                            });
                        }
                    }
                })->get();
                
            foreach ($users as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'severity' => $severity,
                    'related_type' => $relatedType,
                    'related_id' => $relatedId,
                ]);
                
                if (in_array($type, ['low_stock', 'transfer_incoming', 'transfer_completed', 'new_product'])) {
                    LowStockAlertEvent::dispatch($notification, $user->id);
                }
            }
        }
    }

    public static function lowStockAlert(
        string $productName,
        string $warehouseName,
        int $currentQty,
        int $minimumStock,
        int $productId,
        ?int $warehouseId = null,
    ): void {
        $severity = $currentQty <= 0 ? 'critical' : ($currentQty <= $minimumStock / 2 ? 'critical' : 'warning');

        self::create(
            type: 'low_stock',
            title: 'Stok Rendah',
            message: "Stok {$productName} di {$warehouseName} di bawah minimum ({$currentQty}/{$minimumStock} unit)",
            severity: $severity,
            relatedType: 'product',
            relatedId: $productId,
            warehouseId: $warehouseId
        );
    }
}
