<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Services\EmailQueueService;
use App\Models\User;

class TicketController
{
    public function __construct()
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login')->send();
            exit;
        }
    }
    public function index()
    {
        $clientId = session()->get('client_id');
        $client = \App\Models\Client::where('user_id', $clientId)->first();

        $tickets = \Illuminate\Database\Eloquent\Collection::make([]);
        
        $q = request()->input('q');
        $status = request()->input('status');

        if ($client) {
            $query = Ticket::where('client_id', $client->id);
            
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            if (!empty($q)) {
                $query->where(function($builder) use ($q) {
                    $builder->where('subject', 'like', "%{$q}%")
                            ->orWhere('id', 'like', "%{$q}%");
                });
            }
            
            $tickets = $query->orderBy('updated_at', 'desc')->get();
        }

        response(View::render('client.tickets.index', [
            'tickets' => $tickets,
            'filters' => ['q' => $q, 'status' => $status]
        ]))->send();
    }

    public function create()
    {
        response(View::render('client.tickets.create'))->send();
    }

    public function store()
    {
        $userId = session()->get('client_id');
        $client = \App\Models\Client::where('user_id', $userId)->first();
        $user = User::find($userId);

        if (!$client) {
            \App\Core\Flash::error('Sua conta não possui um perfil de empresa associado para abrir chamados.');
            response()->redirect('/cliente/suporte');
            exit;
        }

        $data = request()->all();
        
        $ticket = Ticket::create([
            'client_id' => $client->id,
            'subject' => $data['subject'],
            'status' => \App\Enums\TicketStatus::Open,
            'priority' => \App\Enums\TicketPriority::tryFrom($data['priority'] ?? 'normal') ?? \App\Enums\TicketPriority::Normal
        ]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'message' => $data['message'],
            'is_internal' => false
        ]);

        $uploadedFiles = [];
        // Handle Attachments
        if (!empty($_FILES['attachments']['name'][0])) {
            $files = $_FILES['attachments'];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $name = basename($files['name'][$i]);
                    // Clean name
                    $nameClean = preg_replace('/[^a-zA-Z0-9.\-_]/', '', $name);
                    $destPath = __DIR__ . '/../../../storage/uploads/tickets/' . uniqid() . '_' . $nameClean;
                    
                    if (move_uploaded_file($tmpName, $destPath)) {
                        TicketAttachment::create([
                            'ticket_message_id' => $message->id,
                            'file_name' => $name,
                            'file_path' => $destPath,
                            'file_type' => mime_content_type($destPath)
                        ]);
                        $uploadedFiles[] = $destPath;
                    }
                }
            }
        }

        // Notify Admins
        $adminUsers = User::where('role', \App\Enums\UserRole::Admin->value)->get();
        foreach ($adminUsers as $admin) {
            $htmlBody = View::render('emails.ticket_update', [
                'userName' => $admin->name,
                'ticketId' => $ticket->id,
                'ticketSubject' => $ticket->subject,
                'senderName' => $user->name,
                'messageContent' => $message->message,
                'hasAttachments' => count($uploadedFiles) > 0,
                'actionUrl' => $_ENV['APP_URL'] . "/admin/tickets/{$ticket->id}"
            ]);

            EmailQueueService::enqueue(
                $admin->email,
                $admin->name,
                "Novo Ticket de Suporte: #{$ticket->id}",
                $htmlBody,
                $uploadedFiles
            );
        }

        \App\Services\NotificationService::sendToAdmins(
            "Novo Chamado de Suporte",
            "O cliente <b>{$user->name}</b> abriu o Ticket #{$ticket->id}: {$ticket->subject}",
            \App\Enums\AlertType::Warning,
            "/admin/tickets/{$ticket->id}"
        );

        \App\Core\Flash::success('Seu ticket de suporte foi aberto! Responderemos em breve.');
        response()->redirect('/cliente/suporte');
    }

    public function show($id)
    {
        $clientId = session()->get('client_id');
        $client = \App\Models\Client::where('user_id', $clientId)->first();
        
        if (!$client) {
            \App\Core\Flash::error('Perfil de cliente ausente.');
            response()->redirect('/cliente/dashboard');
            exit;
        }

        $ticket = Ticket::with(['messages.sender', 'messages.attachments'])
                        ->where('id', $id)
                        ->where('client_id', $client->id)
                        ->first();

        if (!$ticket) {
            \App\Core\Flash::error('Ticket não encontrado.');
            response()->redirect('/cliente/suporte');
        }

        response(View::render('client.tickets.show', ['ticket' => $ticket]))->send();
    }

    public function reply($id)
    {
        $userId = session()->get('client_id');
        $client = \App\Models\Client::where('user_id', $userId)->first();
        $user = User::find($userId);

        if (!$client) {
            \App\Core\Flash::error('Acesso negado.');
            response()->redirect('/cliente/suporte');
            exit;
        }

        $ticket = Ticket::where('id', $id)->where('client_id', $client->id)->first();
        
        if (!$ticket) {
            \App\Core\Flash::error('Ticket não encontrado.');
            response()->redirect('/cliente/suporte');
        }

        $data = request()->all();

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'message' => $data['message'],
            'is_internal' => false
        ]);

        // Status always becomes "waiting on admin" if client replies
        $ticket->update(['status' => \App\Enums\TicketStatus::Open]);

        $uploadedFiles = [];
        if (!empty($_FILES['attachments']['name'][0])) {
            $files = $_FILES['attachments'];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $name = basename($files['name'][$i]);
                    $nameClean = preg_replace('/[^a-zA-Z0-9.\-_]/', '', $name);
                    $destPath = __DIR__ . '/../../../storage/uploads/tickets/' . uniqid() . '_' . $nameClean;
                    
                    if (move_uploaded_file($tmpName, $destPath)) {
                        TicketAttachment::create([
                            'ticket_message_id' => $message->id,
                            'file_name' => $name,
                            'file_path' => $destPath,
                            'file_type' => mime_content_type($destPath)
                        ]);
                        $uploadedFiles[] = $destPath;
                    }
                }
            }
        }

        // Notify Admins
        $adminUsers = User::where('role', \App\Enums\UserRole::Admin->value)->get();
        foreach ($adminUsers as $admin) {
            $htmlBody = View::render('emails.ticket_update', [
                'userName' => $admin->name,
                'ticketId' => $ticket->id,
                'ticketSubject' => $ticket->subject,
                'senderName' => $user->name,
                'messageContent' => $message->message,
                'hasAttachments' => count($uploadedFiles) > 0,
                'actionUrl' => $_ENV['APP_URL'] . "/admin/tickets/{$ticket->id}"
            ]);

            EmailQueueService::enqueue(
                $admin->email,
                $admin->name,
                "Nova mensagem no Ticket: #{$ticket->id}",
                $htmlBody,
                $uploadedFiles
            );
        }

        \App\Services\NotificationService::sendToAdmins(
            "Nova Resposta em Suporte",
            "O cliente <b>{$user->name}</b> enviou uma mensagem no Ticket #{$ticket->id}",
            \App\Enums\AlertType::Info,
            "/admin/tickets/{$ticket->id}"
        );

        \App\Core\Flash::success('Mensagem enviada com sucesso!');
        response()->redirect("/cliente/suporte/{$ticket->id}");
    }

    public function attachment($attachmentId)
    {
        $userId = session()->get('client_id');
        $client = \App\Models\Client::where('user_id', $userId)->first();
        
        if (!$client) {
            response()->setStatusCode(403)->setContent('Acesso negado ou perfil ausente.')->send();
            exit;
        }
        
        $attachment = TicketAttachment::with('message.ticket')->find($attachmentId);
        
        if (!$attachment || $attachment->message->ticket->client_id !== $client->id) {
            response()->setStatusCode(403)->setContent('Acesso negado ou anexo não encontrado.')->send();
            exit;
        }

        $filePath = $attachment->file_path;
        
        if (!file_exists($filePath)) {
            response()->setStatusCode(404)->setContent('Arquivo físico não encontrado no servidor.')->send();
            exit;
        }

        $mimeType = mime_content_type($filePath);
        $fileName = $attachment->file_name;

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=86400');
        
        readfile($filePath);
        exit;
    }
}
