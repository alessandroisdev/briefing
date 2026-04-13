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

        echo View::render('admin.settings.email', ['settings' => $settings]);
    }

    public function save()
    {
        $data = $_POST;
        
        $keys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure', 'from_email', 'from_name'];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                EmailSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $data[$key]]
                );
            }
        }

        Flash::success('Configurações de E-mail salvas com sucesso!');
        header('Location: /admin/settings/email');
        exit;
    }
}
