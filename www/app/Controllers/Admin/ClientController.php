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
        echo View::render('admin.clients.index', ['clients' => $clients]);
    }

    public function create()
    {
        echo View::render('admin.clients.create');
    }

    public function store()
    {
        $data = $_POST;

        // Validations
        if (User::where('email', $data['email'])->exists()) {
            \App\Core\Flash::error('O email já está em uso.');
            header('Location: /admin/clients/create');
            exit;
        }

        // Create User
        $user = clone new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->document = $data['document'] ?? null;
        $user->role = 'client';
        $user->save();

        // Create Client specific data
        $client = clone new Client();
        $client->user_id = $user->id;
        $client->company_name = $data['company_name'] ?? null;
        $client->address = $data['address'] ?? null;
        $client->status = 'active';
        $client->save();

        \App\Core\Flash::success('Cliente criado com sucesso!');
        header('Location: /admin/clients');
        exit;
    }

    public function edit($id)
    {
        $client = Client::with('user')->find($id);

        if (!$client) {
            \App\Core\Flash::error('Cliente não localizado.');
            header('Location: /admin/clients');
            exit;
        }

        echo View::render('admin.clients.edit', ['client' => $client]);
    }

    public function update($id)
    {
        $client = Client::with('user')->find($id);

        if ($client) {
            $data = $_POST;
            
            // Only update user if relation exists
            if ($client->user) {
                $client->user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?? null,
                    'document' => $data['document'] ?? null,
                ]);
            }

            $client->update([
                'company_name' => $data['company_name'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
            
            \App\Core\Flash::success('Dados do cliente atualizados com sucesso!');
        }

        header('Location: /admin/clients');
        exit;
    }

    public function generateMagicLink($id)
    {
        $client = Client::with('user')->find($id);

        if ($client && $client->user) {
            $token = bin2hex(random_bytes(32));
            
            $client->user->update([
                'magic_link_token' => $token,
                'magic_link_expires' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            ]);

            \App\Core\Flash::success('Login Link (Magic Link) gerado para ' . $client->user->email . '!');
        }

        header('Location: /admin/clients');
        exit;
    }
}
