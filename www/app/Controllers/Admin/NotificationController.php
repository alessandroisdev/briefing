<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Notification;

class NotificationController
{
    public function index()
    {
        $adminUserId = $_SESSION['admin_id'] ?? ($_SESSION['client_id'] ?? 0);
        $notifications = Notification::where('user_id', $adminUserId)
                            ->orderBy('id', 'desc')
                            ->get();

        echo View::render('admin.notifications.index', ['notifications' => $notifications]);
    }

    public function markAsRead($id)
    {
        $adminUserId = $_SESSION['admin_id'] ?? ($_SESSION['client_id'] ?? 0);
        $notification = Notification::where('id', $id)
                            ->where('user_id', $adminUserId)
                            ->first();

        if ($notification) {
            $notification->update(['read_at' => date('Y-m-d H:i:s')]);
            
            if ($notification->action_url) {
                header('Location: ' . $notification->action_url);
                exit;
            }
        }

        header('Location: /admin/notifications');
        exit;
    }

    public function markAllAsRead()
    {
        $adminUserId = $_SESSION['admin_id'] ?? ($_SESSION['client_id'] ?? 0);
        Notification::where('user_id', $adminUserId)
                    ->whereNull('read_at')
                    ->update(['read_at' => date('Y-m-d H:i:s')]);
        
        \App\Core\Flash::success('Todas as notificações foram marcadas como lidas.');
        header('Location: /admin/notifications');
        exit;
    }
}
