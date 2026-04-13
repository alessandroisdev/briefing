<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\User;
use App\Models\Client;

class DashboardController
{
    public function index()
    {
        // For now, load some fake metrics just to visualize the layout
        $metrics = [
            'total_clients' => Client::count(),
            'active_briefings' => 5, // mock
            'pending_approvals' => Client::whereNotNull('pending_updates')->count(),
        ];

        $clients = Client::with('user')->get();

        echo View::render('admin.dashboard.index', [
            'metrics' => $metrics,
            'clients' => $clients
        ]);
    }
}
