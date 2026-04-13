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
        return View::render('admin.briefings.index', ['briefings' => $briefings]);
    }

    public function create()
    {
        $clients = Client::with('user')->get();
        $templates = BriefingTemplate::where('status', 'active')->get();
        return View::render('admin.briefings.create', ['clients' => $clients, 'templates' => $templates]);
    }

    public function store()
    {
        $data = $_POST;
        
        $template = BriefingTemplate::find($data['template_id']);
        if (!$template) {
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

        header('Location: /admin/briefings');
        exit;
    }

    public function show($id)
    {
        $briefing = ClientBriefing::with(['client.user', 'template'])->find($id);

        if (!$briefing) {
            header('Location: /admin/briefings');
            exit;
        }

        return View::render('admin.briefings.show', ['briefing' => $briefing]);
    }

    public function updateStatus($id)
    {
        $data = $_POST;
        $briefing = ClientBriefing::find($id);

        if ($briefing && isset($data['status'])) {
            $briefing->update(['status' => $data['status']]);
        }

        header('Location: /admin/briefings/' . $id);
        exit;
    }
}
