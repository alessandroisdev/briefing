<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Core\RedisManager;

class NotificationService
{
    /**
     * Envia uma notificação apenas para usuários administradores.
     * 
     * @param string $title
     * @param string $message
     * @param string $type (info, success, warning)
     * @param string|null $actionUrl
     * @return void
     */
    public static function sendToAdmins($title, $message, $type = 'info', $actionUrl = null)
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'action_url' => $actionUrl,
                'read_at' => null
            ]);

            // Dispara sinal SSE em Tempo Real
            RedisManager::publish('notifications_channel', [
                'event' => 'new_notification',
                'notification_id' => $notification->id,
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'action_url' => $actionUrl
            ]);
        }
    }
}
