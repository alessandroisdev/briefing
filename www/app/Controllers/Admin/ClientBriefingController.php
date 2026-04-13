<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\ClientBriefing;
use App\Models\Client;
use App\Models\BriefingTemplate;

class ClientBriefingController
{
    public function index()
    {
        $briefings = ClientBriefing::with(['client.user', 'template'])->orderBy('id', 'desc')->get();
        response(View::render('admin.briefings.index', ['briefings' => $briefings]))->send();
    }

    public function create()
    {
        $clients = Client::with('user')->get();
        $templates = BriefingTemplate::where('status', \App\Enums\ActiveStatus::Active->value)->get();
        response(View::render('admin.briefings.create', ['clients' => $clients, 'templates' => $templates]))->send();
    }

    public function store()
    {
        $data = request()->all();
        
        $template = BriefingTemplate::find($data['template_id']);
        if (!$template) {
            \App\Core\Flash::error('O modelo de briefing selecionado não foi encontrado!');
            response()->redirect('/admin/briefings/create');
        }

        ClientBriefing::create([
            'client_id' => $data['client_id'],
            'template_id' => $template->id,
            'title' => $data['title'] ?: $template->title,
            'status' => \App\Enums\BriefingStatus::Criado, // enum: criado, editando, executando, cancelado, finalizado
            // form_data will be populated by the client
        ]);

        \App\Core\Flash::success('Projeto/Briefing associado ao cliente com sucesso!');
        response()->redirect('/admin/briefings');
    }

    public function show($id)
    {
        $briefing = ClientBriefing::with(['client.user', 'template'])->find($id);

        if (!$briefing) {
            \App\Core\Flash::error('Projeto não encontrado!');
            response()->redirect('/admin/briefings');
        }

        response(View::render('admin.briefings.show', ['briefing' => $briefing]))->send();
    }

    public function updateStatus($id)
    {
        $data = request()->all();
        $briefing = ClientBriefing::find($id);

        if ($briefing && isset($data['status'])) {
            $oldStatus = $briefing->status;
            $briefing->update(['status' => $data['status']]);

            if ($oldStatus !== $data['status']) {
                // Notificar frontend via Redis e SSE
                \App\Core\RedisManager::publish('notifications_channel', [
                    'event' => 'status_changed',
                    'briefing_id' => $briefing->id,
                    'new_status' => $data['status'],
                    'message' => "O status do projeto #{$briefing->id} foi alterado para: " . strtoupper($data['status'])
                ]);
            }
            \App\Core\Flash::success('Status atualizado com sucesso!');
        }

        response()->redirect('/admin/briefings/' . $id);
    }
}
