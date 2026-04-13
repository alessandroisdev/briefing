<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Services\EmailQueueService;
use App\Models\User;

class TicketController
{
    public function index()
    {
        $tickets = Ticket::with('client.user')->orderBy('updated_at', 'desc')->get();
        response(View::render('admin.tickets.index', ['tickets' => $tickets]))->send();
    }

    public function show($id)
    {
        $ticket = Ticket::with(['client.user', 'messages.sender', 'messages.attachments'])
                        ->where('id', $id)
                        ->first();

        if (!$ticket) {
            \App\Core\Flash::error('Ticket não encontrado.');
            response()->redirect('/admin/tickets');
        }

        response(View::render('admin.tickets.show', ['ticket' => $ticket]))->send();
    }

    public function reply($id)
    {
        $adminId = session()->get('user_id');
        $adminUser = User::find($adminId);

        $ticket = Ticket::with('client.user')->where('id', $id)->first();
        
        if (!$ticket) {
            \App\Core\Flash::error('Ticket não encontrado.');
            response()->redirect('/admin/tickets');
        }

        $data = request()->all();
        $isInternal = isset($data['is_internal']) && $data['is_internal'] == '1';

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $adminId,
            'message' => $data['message'],
            'is_internal' => $isInternal
        ]);

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

        // Se não for nota interna, notifica o cliente
        if (!$isInternal) {
            $ticket->update(['status' => \App\Enums\TicketStatus::Answered]);

            $clientUser = $ticket->client->user;

            $htmlBody = View::render('emails.ticket_update', [
                'userName' => $clientUser->name,
                'ticketId' => $ticket->id,
                'ticketSubject' => $ticket->subject,
                'senderName' => $adminUser->name,
                'messageContent' => $message->message,
                'hasAttachments' => count($uploadedFiles) > 0,
                'actionUrl' => $_ENV['APP_URL'] . "/cliente/suporte/{$ticket->id}"
            ]);

            EmailQueueService::enqueue(
                $clientUser->email,
                $clientUser->name,
                "Lemos seu Ticket: #{$ticket->id}",
                $htmlBody,
                $uploadedFiles
            );
        }

        \App\Core\Flash::success('Resposta adicionada ao Ticket!');
        response()->redirect("/admin/tickets/{$ticket->id}");
    }

    public function updateStatus($id)
    {
        $ticket = Ticket::find($id);
        
        if ($ticket) {
            $data = request()->all();
            
            if (isset($data['status'])) {
                $statusEnum = \App\Enums\TicketStatus::tryFrom($data['status']);
                if ($statusEnum) $ticket->update(['status' => $statusEnum]);
            }

            if (isset($data['priority'])) {
                $priorityEnum = \App\Enums\TicketPriority::tryFrom($data['priority']);
                if ($priorityEnum) $ticket->update(['priority' => $priorityEnum]);
            }
            
            \App\Core\Flash::success('Configurações do Ticket atualizadas com sucesso.');
            response()->redirect("/admin/tickets/{$id}");
        }

        response()->redirect('/admin/tickets');
    }
}
