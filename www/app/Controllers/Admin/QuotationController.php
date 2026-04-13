<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationTemplate;
use App\Models\Client;
use App\Models\ClientBriefing;
use App\Core\Flash;

class QuotationController
{
    public function create()
    {
        $briefingId = request()->input('briefing_id');
        $clientId = request()->input('client_id');
        
        $briefing = null;
        if ($briefingId) {
            $briefing = ClientBriefing::with('client')->find($briefingId);
            if ($briefing && !$clientId) {
                $clientId = $briefing->client_id;
            }
        }

        $clients = Client::with('user')->get();
        $templates = QuotationTemplate::where('is_active', 1)->get();

        response(View::render('admin.quotations.create', [
            'briefing' => $briefing,
            'selectedClientId' => $clientId,
            'clients' => $clients,
            'templates' => $templates
        ]))->send();
    }

    public function store()
    {
        $data = request()->all();
        
        if (empty($data['client_id']) || empty($data['title'])) {
            Flash::error('Cliente e Título são obrigatórios.');
            response()->redirect('/admin/quotations/create');
            return;
        }

        // 1. Create the base Quotation
        $quotation = Quotation::create([
            'client_id' => $data['client_id'],
            'briefing_id' => !empty($data['briefing_id']) ? $data['briefing_id'] : null,
            'title' => $data['title'],
            'status' => 'draft',
            'valid_until' => date('Y-m-d H:i:s', strtotime('+15 days')), // 15 dias de validade comercial padrão
            'total_amount' => '0' // calculated later
        ]);

        $grandTotal = 0;

        // 2. Loop through dynamic items (descriptions[], quantities[], unit_prices[])
        if (!empty($data['descriptions']) && is_array($data['descriptions'])) {
            foreach ($data['descriptions'] as $index => $desc) {
                // Ignore empty rows
                if (empty(trim($desc))) continue;

                $quantity = isset($data['quantities'][$index]) ? (int)$data['quantities'][$index] : 1;
                $unitPriceRaw = $data['unit_prices'][$index] ?? '0';
                
                // Parse BRL Mask to Float
                $unitPrice = floatval(str_replace(['.', ','], ['', '.'], str_replace(['R$', ' '], '', $unitPriceRaw)));
                $totalLined = $quantity * $unitPrice;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => trim($desc),
                    'quantity' => $quantity,
                    'unit_price' => (string)$unitPrice,
                    'total' => (string)$totalLined
                ]);

                $grandTotal += $totalLined;
            }
        }

        // 3. Update total amount
        $quotation->update(['total_amount' => (string)$grandTotal]);

        Flash::success('Orçamento / Cotação gerada com sucesso com valor total de R$ ' . number_format($grandTotal, 2, ',', '.') . '!');
        
        // Se foi criado a partir de um briefing (War Room), volta para lá na aba financeira
        if (!empty($data['briefing_id'])) {
            response()->redirect('/admin/briefings/' . $data['briefing_id'] . '#tab-financial');
        } else {
            // Caso contrario exibe a cotação
            response()->redirect('/admin/quotations/' . $quotation->id);
        }
    }

    public function show($id)
    {
        $quotation = Quotation::with(['client.user', 'briefing', 'items'])->find($id);

        if (!$quotation) {
            Flash::error('Orçamento não encontrado.');
            response()->redirect('/admin/dashboard');
            return;
        }

        response(View::render('admin.quotations.show', ['quotation' => $quotation]))->send();
    }

    public function sendToClient($id)
    {
        $quotation = Quotation::find($id);
        
        if (!$quotation) {
            Flash::error('Orçamento não encontrado.');
            response()->redirect('javascript:history.back()');
            return;
        }

        $quotation->update(['status' => 'sent']);
        
        // Notify Client
        \App\Services\NotificationService::sendToClient(
            $quotation->client->user_id,
            'Novo Orçamento: ' . $quotation->title,
            "A Agência enviou um novo escopo com valores para avaliação. Por favor verifique.",
            \App\Enums\AlertType::Info,
            '/cliente/quotations/' . $quotation->id
        );

        Flash::success('Orçamento disponibilizado e cliente notificado com sucesso!');
        response()->redirect('/admin/quotations/' . $quotation->id);
    }

    public function generatePdf($id)
    {
        $quotation = Quotation::with(['client.user', 'briefing', 'items'])->find($id);

        if (!$quotation) {
            Flash::error('Orçamento não encontrado.');
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
        $dompdf->stream($filename, ["Attachment" => true]); // true to force download, false to show in browser
        exit;
    }
}
