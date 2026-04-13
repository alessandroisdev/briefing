<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\User;
use App\Models\Client;

class ProfileController
{
    public function index()
    {
        if (empty($_SESSION['client_id'])) {
            header('Location: /cliente/login');
            exit;
        }

        $user = User::find($_SESSION['client_id']);
        $client = Client::where('user_id', $user->id)->first();

        echo View::render('client.profile.index', [
            'user' => $user,
            'client' => $client
        ]);
    }

    public function updatePassword()
    {
        if (empty($_SESSION['client_id'])) {
            header('Location: /cliente/login');
            exit;
        }

        $data = $_POST;
        $user = User::find($_SESSION['client_id']);

        if (empty($data['password']) || strlen($data['password']) < 4) {
            \App\Core\Flash::error('A nova senha deve ter no mínimo 4 caracteres.');
            header('Location: /cliente/perfil');
            exit;
        }

        if ($data['password'] !== $data['password_confirm']) {
            \App\Core\Flash::error('As senhas não coincidem!');
            header('Location: /cliente/perfil');
            exit;
        }

        $user->update([
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);

        \App\Core\Flash::success('Senha de acesso atualizada com sucesso!');
        header('Location: /cliente/perfil');
        exit;
    }
}
