<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\User;

class AuthController
{
    public function loginForm()
    {
        echo View::render('client.auth.login');
    }

    public function login()
    {
        $data = $_POST;
        $identification = $data['identification'] ?? '';
        $code = $data['code'] ?? '';
        
        $user = User::where('role', 'client')
                    ->where(function($q) use ($identification) {
                        $q->where('email', $identification)
                          ->orWhere('phone', $identification)
                          ->orWhere('document', $identification);
                    })
                    ->first();

        if ($user) {
            // Check Magic Link
            if (!empty($code) && $user->magic_link_token === $code) {
                if (strtotime($user->magic_link_expires) > time()) {
                    // Valid!
                    $_SESSION['client_id'] = $user->id;
                    $user->update(['magic_link_token' => null]); // invalidate
                    
                    \App\Services\NotificationService::sendToAdmins(
                        "Autenticação via Magic Link",
                        "O cliente <b>{$user->name}</b> efetuou login usando o link de e-mail.",
                        "info",
                        "/admin/clients"
                    );
                    
                    \App\Core\Flash::success('Autenticação via Código Mágico realizada com sucesso!');
                    header('Location: /cliente/dashboard');
                    exit;
                }
            }
            
            // Checking Password Fallback (if they have one)
            if (!empty($code) && !empty($user->password) && password_verify($code, $user->password)) {
                $_SESSION['client_id'] = $user->id;
                
                \App\Services\NotificationService::sendToAdmins(
                    "Novo Acesso",
                    "O cliente <b>{$user->name}</b> acabou de entrar no portal.",
                    "info",
                    "/admin/clients"
                );
                
                \App\Core\Flash::success('Bem-vindo de volta!');
                header('Location: /cliente/dashboard');
                exit;
            }
        }

        \App\Core\Flash::error('Credenciais inválidas ou código preterido expirado!');
        header('Location: /cliente/login');
        exit;
    }

    public function requestMagicLink()
    {
        $identification = trim($_POST['identificacao'] ?? '');

        if (empty($identification)) {
            \App\Core\Flash::error('Por favor, informe sua Identificação.');
            header('Location: /cliente/login');
            exit;
        }

        $user = User::where('role', 'client')
            ->where(function($q) use ($identification) {
                $q->where('email', $identification)
                  ->orWhere('phone', $identification)
                  ->orWhere('document', $identification);
            })->first();

        // Sempre damos um feedback positivo evitando Enumeração de usuários!
        \App\Core\Flash::info('Se a identificação estiver correta, enviaremos um código de acesso para o seu e-mail.');

        if ($user && !empty($user->email)) {
            $token = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8); // Simpler 8 chars for typing
            $user->update([
                'magic_link_token' => $token,
                'magic_link_expires' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            ]);

            $loginUrl = "http://localhost:8000/cliente/login";
            $body = "<h2>Acesso ao Painel de Briefings</h2>
                     <p>Olá {$user->name},</p>
                     <p>Aqui está o seu Código de Acesso único e temporário:</p>
                     <p style='padding:15px; background:#f4f4f4; border:1px dashed #ccc; font-size:24px; font-weight:bold; letter-spacing:3px; text-align:center;'>
                        {$token}
                     </p>
                     <p>Use este código junto da sua identificação em <a href='{$loginUrl}'>{$loginUrl}</a></p>
                     <p><em>Este código expira em 24 horas.</em></p>";

            \App\Services\EmailQueueService::enqueue($user->email, $user->name, 'Seu Código de Acesso ao Portal', $body);
        }

        header('Location: /cliente/login');
        exit;
    }
}
