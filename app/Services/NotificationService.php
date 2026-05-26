<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

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
    ): void {
        if ($userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'severity' => $severity,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
            ]);
        } else {
            // Notify all admins and managers
            $users = User::whereIn('role', ['admin', 'manager'])->where('is_active', true)->get();
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'severity' => $severity,
                    'related_type' => $relatedType,
                    'related_id' => $relatedId,
                ]);
            }
        }
    }

    public static function lowStockAlert(
        string $productName,
        string $warehouseName,
        int $currentQty,
        int $minimumStock,
        int $productId,
    ): void {
        $severity = $currentQty <= 0 ? 'critical' : ($currentQty <= $minimumStock / 2 ? 'critical' : 'warning');

        self::create(
            type: 'low_stock',
            title: 'Stok Rendah',
            message: "Stok {$productName} di {$warehouseName} di bawah minimum ({$currentQty}/{$minimumStock} unit)",
            severity: $severity,
            relatedType: 'product',
            relatedId: $productId,
        );
    }
}
