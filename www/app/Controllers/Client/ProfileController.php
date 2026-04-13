<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\User;
use App\Models\Client;

class ProfileController
{
    public function index()
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login');
        }

        $user = User::find(session()->get('client_id'));
        $client = Client::where('user_id', $user->id)->first();

        response(View::render('client.profile.index', [
            'user' => $user,
            'client' => $client
        ]))->send();
    }

    public function updatePassword()
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login');
        }

        $data = request()->all();
        $user = User::find(session()->get('client_id'));

        if (empty($data['password']) || strlen($data['password']) < 4) {
            \App\Core\Flash::error('A nova senha deve ter no mínimo 4 caracteres.');
            response()->redirect('/cliente/perfil');
        }

        if ($data['password'] !== $data['password_confirm']) {
            \App\Core\Flash::error('As senhas não coincidem!');
            response()->redirect('/cliente/perfil');
        }

        $user->update([
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);

        \App\Services\NotificationService::sendToAdmins(
            "Segurança de Conta",
            "O cliente <b>{$user->name}</b> redefiniu sua senha de acesso estática no perfil.",
            \App\Enums\AlertType::Warning,
            "/admin/clients"
        );

        \App\Core\Flash::success('Senha de acesso atualizada com sucesso!');
        response()->redirect('/cliente/perfil');
    }
}
