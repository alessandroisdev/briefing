<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\EmailSetting;
use App\Core\Flash;

class EmailSettingsController
{
    public function index()
    {
        $settingsRaw = EmailSetting::all();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s->key] = $s->value;
        }

        response(View::render('admin.settings.email', ['settings' => $settings]))->send();
    }

    public function save()
    {
        $data = request()->all();
        
        $keys = [
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure', 'from_email', 'from_name',
            'telegram_bot_token', 'telegram_chat_id'
        ];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                EmailSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $data[$key]]
                );
            }
        }

        // Handle checkbox which is omitted if unchecked
        $telegramEnabled = isset($data['telegram_enabled']) ? '1' : '0';
        EmailSetting::updateOrCreate(
            ['key' => 'telegram_enabled'],
            ['value' => $telegramEnabled]
        );

        Flash::success('Configurações de E-mail salvas com sucesso!');
        response()->redirect('/admin/settings/email');
    }
}
