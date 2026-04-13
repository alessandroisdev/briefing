<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\ClientBriefing;

class BriefingController
{
    public function show($id)
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login');
        }

        $user = \App\Models\User::find(session()->get('client_id'));
        $client = \App\Models\Client::where('user_id', $user->id)->first();

        // Ensure client only accesses their own briefings
        $briefing = ClientBriefing::with(['template', 'client.user'])
                        ->where('id', $id)
                        ->where('client_id', $client->id ?? 0)
                        ->first();

        if (!$briefing) {
            response()->redirect('/cliente/dashboard');
        }

        response(View::render('client.briefings.show', ['briefing' => $briefing]))->send();
    }

    public function save($id)
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login');
        }

        $user = \App\Models\User::find(session()->get('client_id'));
        $client = \App\Models\Client::where('user_id', $user->id)->first();

        // Ensure client only accesses their own briefings
        $briefing = ClientBriefing::where('id', $id)
                        ->where('client_id', $client->id ?? 0)
                        ->first();

        if (!$briefing) {
            response()->redirect('/cliente/dashboard');
        }

        $formData = $briefing->form_data ?? []; // Mantém dados antigos para merge
        
        // Mapeamento à prova de balas: cruza MD5 de volta para o texto original
        $schema = $briefing->template->form_schema ?? [];
        $answers = request()->input('answers');
        if (is_array($schema) && is_array($answers)) {
            foreach ($schema as $field) {
                $md5Key = md5($field['label']);
                if (isset($answers[$md5Key])) {
                    $formData[$field['label']] = $answers[$md5Key];
                }
            }
        }
        
        $briefing->update([
            'form_data' => $formData,
            'status' => \App\Enums\BriefingStatus::Editando // Updates status to indicate client has interacted
        ]);

        \App\Services\NotificationService::sendToAdmins(
            "Briefing Atualizado",
            "O cliente <b>{$user->name}</b> atualizou e salvou respostas no projeto '{$briefing->title}'.",
            \App\Enums\AlertType::Success,
            "/admin/briefings/{$briefing->id}"
        );

        \App\Core\Flash::success('Suas respostas foram salvas com sucesso!');

        response()->redirect('/cliente/briefings/' . $id);
    }

    public function storeMessage($id)
    {
        if (!session()->has('client_id')) return response()->redirect('/cliente/login');
        
        $briefing = ClientBriefing::with('template')->find($id);
        if (!$briefing || $briefing->client->user_id !== session()->get('client_id')) return response()->redirect('/cliente/dashboard');

        $data = request()->all();
        
        \App\Models\BriefingMessage::create([
            'briefing_id' => $id,
            'sender_id' => session()->get('client_id'),
            'message' => $data['message'],
            'is_internal' => false
        ]);

        \App\Services\NotificationService::sendToAdmins(
            "Nova Mensagem no Projeto",
            "O cliente deixou uma nova mensagem no projeto '{$briefing->title}'.",
            \App\Enums\AlertType::Info,
            "/admin/briefings/{$briefing->id}#tab-messages"
        );

        $adminUsers = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)->get();
        foreach($adminUsers as $admin) {
            \App\Services\EmailQueueService::push(
                $admin->email,
                $admin->name,
                "Nova Mensagem - Projeto #{$briefing->id}",
                "<h4>Olá {$admin->name},</h4><p>Há uma nova mensagem do cliente no workspace do projeto <b>{$briefing->title}</b>.</p><div style='text-align: center; margin-top: 20px;'><a href='" . env('APP_URL') . "/admin/briefings/{$briefing->id}' class='button'>Acessar a War Room</a></div>"
            );
        }

        \App\Core\Flash::success('Mensagem enviada com sucesso!');
        response()->redirect('/cliente/briefings/' . $id . '#tab-messages');
    }

    public function storeCredential($id)
    {
        if (!session()->has('client_id')) return response()->redirect('/cliente/login');
        
        $briefing = ClientBriefing::with('template')->find($id);
        if (!$briefing || $briefing->client->user_id !== session()->get('client_id')) return response()->redirect('/cliente/dashboard');

        $data = request()->all();

        \App\Models\ProjectCredential::create([
            'briefing_id' => $id,
            'environment' => $data['environment'],
            'service_name' => $data['service_name'],
            'url' => $data['url'] ?? null,
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        \App\Services\NotificationService::sendToAdmins(
            "Cofre Atualizado",
            "Novos dados de credencial foram guardados no projeto '{$briefing->title}'.",
            \App\Enums\AlertType::Warning,
            "/admin/briefings/{$briefing->id}#tab-vault"
        );

        \App\Core\Flash::success('Acesso guardado no cofre do projeto!');
        response()->redirect('/cliente/briefings/' . $id . '#tab-vault');
    }
}
