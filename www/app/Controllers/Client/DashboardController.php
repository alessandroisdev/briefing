<?php

namespace App\Controllers\Client;

use App\Core\View;

class DashboardController
{
    public function index()
    {
        return View::render('client.dashboard.index');
    }
}
