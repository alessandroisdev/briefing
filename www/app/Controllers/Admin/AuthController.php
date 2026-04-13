<?php

namespace App\Controllers\Admin;

use App\Core\View;

class AuthController
{
    public function loginForm()
    {
        echo View::render('admin.auth.login');
    }

    public function login()
    {
        // Simple password logic here
        header('Location: /admin/dashboard');
        exit;
    }
}
