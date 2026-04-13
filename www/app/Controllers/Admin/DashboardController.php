<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\User;
use App\Models\Client;

class DashboardController
{
    public function index()
    {
        $activeStatuses = [
            \App\Enums\BriefingStatus::Criado->value,
            \App\Enums\BriefingStatus::Editando->value,
            \App\Enums\BriefingStatus::Executando->value
        ];

        $metrics = [
            'total_clients' => Client::count(),
            'active_briefings' => \App\Models\ClientBriefing::whereIn('status', $activeStatuses)->count(),
            'pending_approvals' => Client::whereNotNull('pending_updates')->count(),
        ];

        $clients = Client::with('user')->get();

        response(View::render('admin.dashboard.index', [
            'metrics' => $metrics,
            'clients' => $clients
        ]))->send();
    }
}
