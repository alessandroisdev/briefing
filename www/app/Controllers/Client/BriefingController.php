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
}
