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
        response(View::render('admin.clients.index', ['clients' => $clients]))->send();
    }

    public function create()
    {
        response(View::render('admin.clients.create'))->send();
    }

    public function store()
    {
        $data = request()->all();

        // Validations
        if (User::where('email', $data['email'])->exists()) {
            \App\Core\Flash::error('O email já está em uso.');
            response()->redirect('/admin/clients/create');
        }

        // Create User
        $user = clone new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->document = $data['document'] ?? null;
        $user->role = \App\Enums\UserRole::Client;
        
        if (!empty($data['password'])) {
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $user->save();

        // Create Client specific data
        $client = clone new Client();
        $client->user_id = $user->id;
        $client->company_name = $data['company_name'] ?? null;
        $client->address = $data['address'] ?? null;
        $client->status = \App\Enums\ActiveStatus::Active;
        $client->save();

        \App\Core\Flash::success('Cliente criado com sucesso!');
        response()->redirect('/admin/clients');
    }

    public function edit($id)
    {
        $client = Client::with('user')->find($id);

        if (!$client) {
            \App\Core\Flash::error('Cliente não localizado.');
            response()->redirect('/admin/clients');
        }

        response(View::render('admin.clients.edit', ['client' => $client]))->send();
    }

    public function update($id)
    {
        $client = Client::with('user')->find($id);

        if ($client) {
            $data = request()->all();
            
            // Only update user if relation exists
            if ($client->user) {
                $userData = [
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?? null,
                    'document' => $data['document'] ?? null,
                ];
                
                if (!empty($data['password'])) {
                    $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                
                $client->user->update($userData);
            }

            $client->update([
                'company_name' => $data['company_name'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
            
            \App\Core\Flash::success('Dados do cliente atualizados com sucesso!');
        }

        response()->redirect('/admin/clients');
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

        response()->redirect('/admin/clients');
    }
}
