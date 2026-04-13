<?php

namespace App\Controllers\Admin;

use App\Core\View;

class AuthController
{
    public function loginForm()
    {
        response(View::render('admin.auth.login'))->send();
    }

    public function login()
    {
        // Simple password logic here
        response()->redirect('/admin/dashboard');
    }
}
