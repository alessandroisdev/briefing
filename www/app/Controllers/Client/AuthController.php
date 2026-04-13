<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\User;

class AuthController
{
    public function loginForm()
    {
        response(View::render('client.auth.login'))->send();
    }

    public function login()
    {
        $identification = request()->input('identification', '');
        $code = request()->input('code', '');
        
        $user = User::where('role', \App\Enums\UserRole::Client->value)
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
                    session()->put('client_id', $user->id);
                    $user->update(['magic_link_token' => null]); // invalidate
                    
                    \App\Services\NotificationService::sendToAdmins(
                        "Autenticação via Magic Link",
                        "O cliente <b>{$user->name}</b> efetuou login usando o link de e-mail.",
                        \App\Enums\AlertType::Info,
                        "/admin/clients"
                    );
                    
                    \App\Core\Flash::success('Autenticação via Código Mágico realizada com sucesso!');
                    response()->redirect('/cliente/dashboard');
                }
            }
            
            // Checking Password Fallback (if they have one)
            if (!empty($code) && !empty($user->password) && password_verify($code, $user->password)) {
                session()->put('client_id', $user->id);
                
                \App\Services\NotificationService::sendToAdmins(
                    "Novo Acesso",
                    "O cliente <b>{$user->name}</b> acabou de entrar no portal.",
                    \App\Enums\AlertType::Info,
                    "/admin/clients"
                );
                
                \App\Core\Flash::success('Bem-vindo de volta!');
                response()->redirect('/cliente/dashboard');
            }
        }

        \App\Core\Flash::error('Credenciais inválidas ou código preterido expirado!');
        response()->redirect('/cliente/login');
    }

    public function requestMagicLink()
    {
        $identification = trim(request()->input('identificacao', ''));

        if (empty($identification)) {
            \App\Core\Flash::error('Por favor, informe sua Identificação.');
            response()->redirect('/cliente/login');
        }

        $user = User::where('role', \App\Enums\UserRole::Client->value)
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
            $body = "<h3>Acesso ao Painel de Briefings</h3>
                     <p>Olá <b>{$user->name}</b>,</p>
                     <p>Aqui está o seu Código Mágico de Acesso, único e temporário:</p>
                     <div style='padding:20px; background-color: #f1f5f9; border:1px dashed #cbd5e1; border-radius: 6px; font-size:32px; font-weight:800; letter-spacing:5px; text-align:center; color: #0f172a; margin: 20px 0;'>
                        {$token}
                     </div>
                     <p>Use este código junto da sua identificação (e-mail ou documento) acessando:</p>
                     <div class='button-wrap'>
                         <a href='{$loginUrl}' class='button'>Fazer Login Agora</a>
                     </div>
                     <p style='color: #64748b; font-size: 14px;'><em>Este código expira em 24 horas e só pode ser usado uma vez.</em></p>";

            \App\Services\EmailQueueService::push($user->email, $user->name, 'Seu Código de Acesso ao Portal', $body);
        }

        response()->redirect('/cliente/login');
    }
}
