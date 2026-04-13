<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\ClientBriefing;
use App\Models\Client;
use App\Models\BriefingTemplate;
use App\Models\BriefingMessage;
use App\Models\ProjectCredential;
use App\Models\MessageTemplate;

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
        $briefing = ClientBriefing::with(['client.user', 'template', 'messages.sender', 'credentials'])->find($id);

        if (!$briefing) {
            \App\Core\Flash::error('Projeto não encontrado!');
            response()->redirect('/admin/briefings');
            exit;
        }

        $messageTemplates = MessageTemplate::orderBy('title', 'asc')->get();

        response(View::render('admin.briefings.show', [
            'briefing' => $briefing,
            'messageTemplates' => $messageTemplates
        ]))->send();
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

    public function storeMessage($id)
    {
        $data = request()->all();
        $adminUser = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)->first();
        $adminUserId = $adminUser ? $adminUser->id : 1; 

        $isInternal = isset($data['is_internal']) ? true : false;

        BriefingMessage::create([
            'briefing_id' => $id,
            'sender_id' => $adminUserId,
            'message' => $data['message'],
            'is_internal' => $isInternal
        ]);

        if (!$isInternal) {
            $briefing = ClientBriefing::with('client.user')->find($id);
            if ($briefing && $briefing->client && $briefing->client->user) {
                \App\Services\EmailQueueService::push(
                    $briefing->client->user->email,
                    $briefing->client->user->name,
                    "Atualização no Projeto: {$briefing->title}",
                    "<h4>Olá {$briefing->client->user->name},</h4><p>Há uma nova mensagem da agência no seu projeto <b>{$briefing->title}</b>.</p><div style='text-align: center; margin-top: 20px;'><a href='" . env('APP_URL') . "/cliente/briefings/{$briefing->id}' class='button'>Acessar o Painel</a></div>"
                );
            }
        }

        \App\Core\Flash::success('Mensagem enviada no painel do projeto!');
        response()->redirect('/admin/briefings/' . $id . '#tab-messages');
    }

    public function storeCredential($id)
    {
        $data = request()->all();

        ProjectCredential::create([
            'briefing_id' => $id,
            'environment' => $data['environment'],
            'service_name' => $data['service_name'],
            'url' => $data['url'] ?? null,
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        \App\Core\Flash::success('Acesso guardado no cofre do projeto!');
        response()->redirect('/admin/briefings/' . $id . '#tab-vault');
    }

    public function updateAgreedValue($id)
    {
        $data = request()->all();
        $briefing = ClientBriefing::find($id);
        
        if ($briefing && !empty($data['agreed_value'])) {
            $val = str_replace(['R$', '.', ' '], '', $data['agreed_value']);
            $val = str_replace(',', '.', $val);
            
            $briefing->update(['agreed_value' => $val]);
            \App\Core\Flash::success('Valor comercial interno salvo!');
        }

        response()->redirect('/admin/briefings/' . $id . '#tab-financial');
    }
}
