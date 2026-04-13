<?php

namespace App\Controllers\Client;

use App\Core\View;

class DashboardController
{
    public function index()
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login');
        }

        $user = \App\Models\User::find(session()->get('client_id'));
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        
        $briefings = [];
        if ($client) {
            $briefings = \App\Models\ClientBriefing::with('template')
                            ->where('client_id', $client->id)
                            ->orderBy('updated_at', 'DESC')
                            ->get();
        }

        response(View::render('client.dashboard.index', [
            'briefings' => $briefings
        ]))->send();
    }
}
