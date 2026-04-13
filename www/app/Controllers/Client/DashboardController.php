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
            $briefings = \App\Models\ClientBriefing::with(['template', 'messages' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])->where('client_id', $client->id)->orderBy('created_at', 'desc')->get();

            $quotations = \App\Models\Quotation::where('client_id', $client->id)
                ->where('status', '!=', 'draft')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $quotations = [];
        }

        response(View::render('client.dashboard.index', [
            'client' => $client,
            'briefings' => $briefings,
            'quotations' => $quotations
        ]))->send();
    }
}
