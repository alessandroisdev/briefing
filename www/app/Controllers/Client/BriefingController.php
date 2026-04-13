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

        $data = $_POST;
        
        // Dynamic fields come inside the POST mapped by their label/question
        // We will just store everything that is not a reserved keyword in form_data JSON
        $formData = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['_token', 'status'])) {
                // To avoid dots being replaced by underscores in PHP POST keys for some reason, 
                // we assume dynamic inputs passed as they are or base64 encoded keys if complex.
                // For simplicity, we just use the raw POST keys as questions.
                $formData[$key] = $value;
            }
        }

        // If the client submitted files, we would handle $_FILES here
        
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
