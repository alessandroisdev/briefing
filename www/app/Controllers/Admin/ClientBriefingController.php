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
        echo View::render('admin.briefings.index', ['briefings' => $briefings]);
    }

    public function create()
    {
        $clients = Client::with('user')->get();
        $templates = BriefingTemplate::where('status', 'active')->get();
        echo View::render('admin.briefings.create', ['clients' => $clients, 'templates' => $templates]);
    }

    public function store()
    {
        $data = $_POST;
        
        $template = BriefingTemplate::find($data['template_id']);
        if (!$template) {
            \App\Core\Flash::error('O modelo de briefing selecionado não foi encontrado!');
            header('Location: /admin/briefings/create');
            exit;
        }

        ClientBriefing::create([
            'client_id' => $data['client_id'],
            'template_id' => $template->id,
            'title' => $data['title'] ?: $template->title,
            'status' => 'criado', // enum: criado, editando, executando, cancelado, finalizado
            // form_data will be populated by the client
        ]);

        \App\Core\Flash::success('Projeto/Briefing associado ao cliente com sucesso!');
        header('Location: /admin/briefings');
        exit;
    }

    public function show($id)
    {
        $briefing = ClientBriefing::with(['client.user', 'template'])->find($id);

        if (!$briefing) {
            \App\Core\Flash::error('Projeto não encontrado!');
            header('Location: /admin/briefings');
            exit;
        }

        echo View::render('admin.briefings.show', ['briefing' => $briefing]);
    }

    public function updateStatus($id)
    {
        $data = $_POST;
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

        header('Location: /admin/briefings/' . $id);
        exit;
    }
}
