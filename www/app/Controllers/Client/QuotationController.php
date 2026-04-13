<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\Quotation;
use App\Models\Client;
use App\Core\Flash;

class QuotationController
{
    private $clientId;

    public function __construct()
    {
        if (!session()->has('client_id')) {
            response()->redirect('/cliente/login');
            exit;
        }

        $user = \App\Models\User::find(session()->get('client_id'));
        $client = Client::where('user_id', $user->id)->first();
        
        if (!$client) {
            response()->redirect('/');
            exit;
        }

        $this->clientId = $client->id;
    }

    public function show($id)
    {
        // Must belong to the logged-in client AND status must not be 'draft' (or maybe they can't even see it if it's draft, but admin wouldn't send link anyway, but for security, check).
        $quotation = Quotation::with(['client.user', 'briefing', 'items'])
            ->where('id', $id)
            ->where('client_id', $this->clientId)
            ->where('status', '!=', 'draft')
            ->first();

        if (!$quotation) {
            Flash::error('Orçamento não encontrado ou indisponível.');
            response()->redirect('/cliente/dashboard');
            return;
        }

        response(View::render('client.quotations.show', ['quotation' => $quotation]))->send();
    }

    public function approve($id)
    {
        $quotation = Quotation::where('id', $id)
            ->where('client_id', $this->clientId)
            ->first();

        if (!$quotation) {
            Flash::error('Orçamento não encontrado.');
            response()->redirect('/cliente/dashboard');
            return;
        }

        $quotation->update(['status' => 'accepted']);

        // Notificar Admins
        \App\Services\NotificationService::sendToAdmins(
            'Cotação Aprovada!',
            "O cliente {$quotation->client->user->name} aprovou a Cotação #{$quotation->id}.",
            \App\Enums\AlertType::Success,
            '/admin/quotations/' . $quotation->id
        );

        Flash::success('Orçamento Aprovado com sucesso! Entraremos em contato em breve para os próximos passos.');
        response()->redirect('/cliente/quotations/' . $quotation->id);
    }

    public function reject($id)
    {
        $quotation = Quotation::where('id', $id)
            ->where('client_id', $this->clientId)
            ->first();

        if (!$quotation) {
            Flash::error('Orçamento não encontrado.');
            response()->redirect('/cliente/dashboard');
            return;
        }

        $notes = $_POST['client_notes'] ?? null;

        $quotation->update([
            'status' => 'rejected',
            'client_notes' => !empty($notes) ? trim($notes) : null
        ]);

        // Notificar Admins
        \App\Services\NotificationService::sendToAdmins(
            'Cotação Recusada',
            "O cliente {$quotation->client->user->name} não aprovou a Cotação #{$quotation->id}.",
            \App\Enums\AlertType::Warning,
            '/admin/quotations/' . $quotation->id
        );

        Flash::success('Orçamento recusado. Agradecemos pelo feedback!');
        response()->redirect('/cliente/quotations/' . $quotation->id);
    }

    public function generatePdf($id)
    {
        $quotation = Quotation::with(['client.user', 'briefing', 'items'])
            ->where('id', $id)
            ->where('client_id', $this->clientId)
            ->where('status', '!=', 'draft')
            ->first();

        if (!$quotation) {
            Flash::error('Orçamento não encontrado ou indisponível.');
            response()->redirect('javascript:history.back()');
            return;
        }

        $html = View::render('shared.quotations.pdf', ['quotation' => $quotation]);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Proposta_Comercial_No_" . str_pad($quotation->id, 4, '0', STR_PAD_LEFT) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]); // true to force download
        exit;
    }
}
