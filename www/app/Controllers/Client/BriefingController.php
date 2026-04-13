<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\ClientBriefing;

class BriefingController
{
    public function show($id)
    {
        if (empty($_SESSION['client_id'])) {
            header('Location: /cliente/login');
            exit;
        }

        $user = \App\Models\User::find($_SESSION['client_id']);
        $client = \App\Models\Client::where('user_id', $user->id)->first();

        // Ensure client only accesses their own briefings
        $briefing = ClientBriefing::with(['template', 'client.user'])
                        ->where('id', $id)
                        ->where('client_id', $client->id ?? 0)
                        ->first();

        if (!$briefing) {
            header('Location: /cliente/dashboard');
            exit;
        }

        echo View::render('client.briefings.show', ['briefing' => $briefing]);
    }

    public function save($id)
    {
        if (empty($_SESSION['client_id'])) {
            header('Location: /cliente/login');
            exit;
        }

        $user = \App\Models\User::find($_SESSION['client_id']);
        $client = \App\Models\Client::where('user_id', $user->id)->first();

        // Ensure client only accesses their own briefings
        $briefing = ClientBriefing::where('id', $id)
                        ->where('client_id', $client->id ?? 0)
                        ->first();

        if (!$briefing) {
            header('Location: /cliente/dashboard');
            exit;
        }

        $formData = $briefing->form_data ?? []; // Mantém dados antigos para merge
        
        // Mapeamento à prova de balas: cruza MD5 de volta para o texto original
        $schema = $briefing->template->form_schema ?? [];
        if (is_array($schema) && isset($_POST['answers']) && is_array($_POST['answers'])) {
            foreach ($schema as $field) {
                $md5Key = md5($field['label']);
                if (isset($_POST['answers'][$md5Key])) {
                    $formData[$field['label']] = $_POST['answers'][$md5Key];
                }
            }
        }
        
        $briefing->update([
            'form_data' => $formData,
            'status' => 'editando' // Updates status to indicate client has interacted
        ]);

        \App\Services\NotificationService::sendToAdmins(
            "Briefing Atualizado",
            "O cliente <b>{$user->name}</b> atualizou e salvou respostas no projeto '{$briefing->title}'.",
            "success",
            "/admin/briefings/{$briefing->id}"
        );

        \App\Core\Flash::success('Suas respostas foram salvas com sucesso!');

        header('Location: /cliente/briefings/' . $id);
        exit;
    }
}
