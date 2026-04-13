@extends('layouts.admin')

@section('title', 'Detalhes da Cotação - BriefingApp')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="javascript:history.back()" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mb-0">Cotação #{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <span class="badge bg-secondary mt-2">{{ $quotation->status }}</span>
    </div>
    <div class="d-flex gap-2">
        @if($quotation->status === 'draft')
            <form action="/admin/quotations/{{ $quotation->id }}/send" method="POST" class="m-0">
                <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Enviar p/ Cliente</button>
            </form>
        @endif
        <button class="btn btn-outline-light" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
        <a href="/admin/quotations/{{ $quotation->id }}/pdf" class="btn btn-gold bg-gold text-dark fw-bold"><i class="bi bi-file-pdf"></i> Baixar PDF</a>
    </div>
</div>

<div class="card briefing-card border-0 mb-5" style="border-top: 5px solid #D4AF37 !important;">
    <div class="card-body p-5">
        <!-- Cabeçalho Invoice -->
        <div class="row mb-5 border-bottom border-secondary pb-4">
            <div class="col-sm-6">
                <h3 class="text-gold fw-bold mb-0">Briefing<span class="text-white">App</span></h3>
                <p class="text-muted mt-2">Documento de Proposta Comercial</p>
            </div>
            <div class="col-sm-6 text-sm-end text-muted">
                <p class="mb-1"><strong>Data Emissão:</strong> {{ $quotation->created_at->format('d/m/Y') }}</p>
                <p class="mb-1"><strong>Válido até:</strong> {{ date('d/m/Y', strtotime($quotation->valid_until)) }}</p>
                <p class="mb-0"><strong>Ref:</strong> {{ $quotation->title }}</p>
            </div>
        </div>

        <!-- Info Cliente vs Agencia -->
        <div class="row mb-5">
            <div class="col-sm-6">
                <h6 class="text-gold mb-3">APRESENTADO PARA</h6>
                <div class="text-white">
                    <strong class="fs-5">{{ $quotation->client->company_name ?? 'Cliente Sem Empresa' }}</strong><br>
                    <span class="text-muted">{{ $quotation->client->user->name }}</span><br>
                    <span class="text-muted">{{ $quotation->client->user->email }}</span>
                </div>
            </div>
            <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                <h6 class="text-gold mb-3">EMITIDO POR</h6>
                <div class="text-white">
                    <strong>Agência Dark Premium</strong><br>
                    <span class="text-muted">Serviços de Tecnologia</span><br>
                    <span class="text-muted">contato@agencia.com</span>
                </div>
            </div>
        </div>

        <!-- Tabela Financeira -->
        <div class="table-responsive mb-5">
            <table class="table mb-0" style="color: #cbd5e1; border-color: #334155;">
                <thead style="background-color: #0f172a;">
                    <tr>
                        <th class="py-3 border-0">Descrição do Escopo / Serviço</th>
                        <th class="text-center py-3 border-0" style="width: 10%;">Qtd</th>
                        <th class="text-end py-3 border-0" style="width: 20%;">P. Unitário (R$)</th>
                        <th class="text-end py-3 border-0 text-gold" style="width: 20%;">Total (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $item)
                    <tr>
                        <td class="py-3 border-secondary text-white">{{ $item->description }}</td>
                        <td class="text-center py-3 border-secondary">{{ $item->quantity }}</td>
                        <td class="text-end py-3 border-secondary">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-end py-3 border-secondary text-gold fw-bold">{{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumo -->
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h6 class="text-gold mb-2">Termos e Condições Comerciais</h6>
                <p class="text-muted small lh-lg" style="max-width: 400px;">
                    Os valores acima estão sujeitos a alterações caso haja mudança de escopo. 
                    A assinatura eletrônica deste documento ou o primeiro depósito caracterizam aceite do escopo técnico.
                </p>
                @if($quotation->status === 'rejected' && $quotation->client_notes)
                    <div class="mt-4 p-3 rounded alert-danger bg-danger text-white border-0 bg-opacity-10 border-danger border-start border-4">
                        <span class="d-block mb-1 small fw-bold text-danger"><i class="bi bi-exclamation-triangle"></i> Observação do Cliente ao Recusar:</span>
                        {{ $quotation->client_notes }}
                    </div>
                @endif
                @if($quotation->briefing)
                    <div class="mt-4 p-3 rounded" style="background-color: #1e293b; border: 1px dashed #334155;">
                        <span class="text-white small d-block mb-1"><i class="bi bi-link-45deg text-gold"></i> Atrelado ao Projeto War Room:</span>
                        <a href="/admin/briefings/{{ $quotation->briefing_id }}" class="text-decoration-none fw-bold text-info">{{ $quotation->briefing->title }}</a>
                    </div>
                @endif
            </div>
            <div class="col-lg-6 text-end">
                <div class="d-inline-block text-start p-4 rounded" style="background-color: #0f172a; min-width: 300px;">
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($quotation->total_amount, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Desconto:</span>
                        <span>R$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between text-gold fs-4 fw-bold pt-3 border-top border-secondary">
                        <span>Total:</span>
                        <span>R$ {{ number_format($quotation->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @media print {
        body { font-family: 'Inter', sans-serif; background-color: #fff !important; }
        .sidebar, header, .btn, .badge { display: none !important; }
        .admin-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        .card { box-shadow: none !important; color: #000 !important; background-color: #fff !important; }
        .text-white { color: #000 !important; }
        .text-gold { color: #8c7322 !important; }
        .bg-dark, #0f172a, #1e293b { background-color: #f8f9fa !important; }
        .border-secondary { border-color: #dee2e6 !important; }
        .text-muted { color: #6c757d !important; }
    }
</style>
@endsection
