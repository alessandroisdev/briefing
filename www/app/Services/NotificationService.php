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
     * @param \App\Enums\AlertType $type
     * @param string|null $actionUrl
     * @return void
     */
    public static function sendToAdmins($title, $message, \App\Enums\AlertType $type = \App\Enums\AlertType::Info, $actionUrl = null)
    {
        $admins = User::where('role', \App\Enums\UserRole::Admin->value)->get();

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
                'type' => $type->value,
                'action_url' => $actionUrl
            ]);
        }

        // ==========================================
        //  Integração Telegram API (Disparo Global)
        // ==========================================
        $telegramEnabled = \App\Models\EmailSetting::getVal('telegram_enabled', '0');
        if ($telegramEnabled === '1') {
            $botToken = \App\Models\EmailSetting::getVal('telegram_bot_token');
            $chatId = \App\Models\EmailSetting::getVal('telegram_chat_id');

            if (!empty($botToken) && !empty($chatId)) {
                $emoji = match ($type) {
                    \App\Enums\AlertType::Success => '✅',
                    \App\Enums\AlertType::Warning => '⚠️',
                    \App\Enums\AlertType::Danger => '🚨',
                    default => '🔔'
                };

                $telegramMessage = "{$emoji} <b>{$title}</b>\n\n{$message}";
                if ($actionUrl) {
                    $appUrl = env('APP_URL', 'http://localhost:8000');
                    $fullUrl = str_starts_with($actionUrl, 'http') ? $actionUrl : $appUrl . $actionUrl;
                    $telegramMessage .= "\n\n🔗 <a href='{$fullUrl}'>Acessar no Painel</a>";
                }

                $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
                $data = [
                    'chat_id' => $chatId,
                    'text' => $telegramMessage,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ];

                // Fast Fire & Forget Context (2 sec timeout to avoid blocking)
                $options = [
                    'http' => [
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                        'timeout' => 2 
                    ]
                ];
                $context  = stream_context_create($options);
                
                try {
                    @file_get_contents($url, false, $context);
                } catch (\Exception $e) {
                    // Ignore on failure to not break user loop
                }
            }
        }
    }

    /**
     * Envia uma notificação para um cliente específico.
     * 
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param \App\Enums\AlertType $type
     * @param string|null $actionUrl
     * @return void
     */
    public static function sendToClient($userId, $title, $message, \App\Enums\AlertType $type = \App\Enums\AlertType::Info, $actionUrl = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'read_at' => null
        ]);

        // Dispara sinal SSE em Tempo Real para o cliente
        RedisManager::publish('notifications_channel', [
            'event' => 'new_notification',
            'notification_id' => $notification->id,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type->value,
            'action_url' => $actionUrl
        ]);
    }
}
