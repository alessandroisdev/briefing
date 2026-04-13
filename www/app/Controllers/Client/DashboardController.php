<?php

namespace App\Controllers\Client;

use App\Core\View;

class DashboardController
{
    public function index()
    {
        if (empty($_SESSION['client_id'])) {
            header('Location: /cliente/login');
            exit;
        }

        $user = \App\Models\User::find($_SESSION['client_id']);
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        
        $briefings = [];
        if ($client) {
            $briefings = \App\Models\ClientBriefing::with('template')
                            ->where('client_id', $client->id)
                            ->orderBy('updated_at', 'DESC')
                            ->get();
        }

        echo View::render('client.dashboard.index', [
            'briefings' => $briefings
        ]);
    }
}
