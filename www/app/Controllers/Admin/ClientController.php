<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\User;
use App\Models\Client;

class ClientController
{
    public function index()
    {
        $clients = Client::with('user')->orderBy('id', 'desc')->get();
        return View::render('admin.clients.index', ['clients' => $clients]);
    }

    public function create()
    {
        return View::render('admin.clients.create');
    }

    public function store()
    {
        // For pure PHP, we handle $_POST directly
        $data = $_POST;

        // Simple validation mock
        if (empty($data['company_name']) || empty($data['email'])) {
            header('Location: /admin/clients/create?error=mission_fields');
            exit;
        }

        // Create User
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'document' => $data['document'] ?? null,
            'role' => 'client',
            // No password by default for magic link
        ]);

        // Create Client
        Client::create([
            'user_id' => $user->id,
            'company_name' => $data['company_name'],
            'status' => 'active'
        ]);

        header('Location: /admin/clients');
        exit;
    }

    public function edit($id)
    {
        $client = Client::with('user')->find($id);

        if (!$client) {
            header('Location: /admin/clients');
            exit;
        }

        return View::render('admin.clients.edit', ['client' => $client]);
    }

    public function update($id)
    {
        $data = $_POST;
        $client = Client::with('user')->find($id);

        if ($client) {
            $client->update([
                'company_name' => $data['company_name'],
                'status' => $data['status']
            ]);

            $client->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'document' => $data['document'] ?? null,
            ]);
        }

        header('Location: /admin/clients/' . $client->id . '/edit');
        exit;
    }

    public function generateMagicLink($id)
    {
        $client = Client::with('user')->find($id);

        if ($client && $client->user) {
            // Generate a secure random token
            $token = bin2hex(random_bytes(32));
            
            $client->user->update([
                'magic_link_token' => $token,
                'magic_link_expires' => date('Y-m-d H:i:s', strtotime('+24 hours'))
            ]);

            header('Location: /admin/clients/' . $client->id . '/edit?magic_link=' . $token);
            exit;
        }

        header('Location: /admin/clients');
        exit;
    }
}
