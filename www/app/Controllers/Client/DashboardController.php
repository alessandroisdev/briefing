<?php

namespace App\Controllers\Client;

use App\Core\View;

class DashboardController
{
    public function index()
    {
        echo View::render('client.dashboard.index');
    }
}
