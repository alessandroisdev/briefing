<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Notification;

class NotificationController
{
    public function index()
    {
        $adminUser = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)->first();
        $adminUserId = $adminUser ? $adminUser->id : 1; 

        $notifications = Notification::where('user_id', $adminUserId)
                            ->orderBy('id', 'desc')
                            ->get();

        response(View::render('admin.notifications.index', ['notifications' => $notifications]))->send();
    }

    public function markAsRead($id)
    {
        $adminUser = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)->first();
        $adminUserId = $adminUser ? $adminUser->id : 1; 

        $notification = Notification::where('id', $id)
                            ->where('user_id', $adminUserId)
                            ->first();

        if ($notification) {
            $notification->update(['read_at' => date('Y-m-d H:i:s')]);
            
            if ($notification->action_url) {
                response()->redirect($notification->action_url);
            }
        }

        response()->redirect('/admin/notifications');
    }

    public function markAllAsRead()
    {
        $adminUser = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)->first();
        $adminUserId = $adminUser ? $adminUser->id : 1; 

        Notification::where('user_id', $adminUserId)
                    ->whereNull('read_at')
                    ->update(['read_at' => date('Y-m-d H:i:s')]);
        
        \App\Core\Flash::success('Todas as notificações foram marcadas como lidas.');
        response()->redirect('/admin/notifications');
    }
}
