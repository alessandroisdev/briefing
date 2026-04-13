<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\User;

class AuthController
{
    public function loginForm()
    {
        return View::render('client.auth.login');
    }

    public function login()
    {
        // Simple magic link or password logic here
        // For now just basic response
        header('Location: /cliente/dashboard');
        exit;
    }
}
