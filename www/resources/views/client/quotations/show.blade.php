@extends('layouts.app')

@section('title', 'Cotação Comercial - BriefingApp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="/cliente/dashboard" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mb-0">Cotação Comercial</h2>
        <span class="badge bg-secondary mt-2">{{ $quotation->status === 'sent' ? 'Aguardando Aprovação' : ($quotation->status === 'accepted' ? 'Aprovado' : $quotation->status) }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="/cliente/quotations/{{ $quotation->id }}/pdf" class="btn btn-outline-light"><i class="bi bi-printer"></i> Baixar PDF</a>
        @if($quotation->status === 'sent')
            <button type="button" class="btn btn-outline-danger m-0" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle"></i> Recusar
            </button>
            <form action="/cliente/quotations/{{ $quotation->id }}/approve" method="POST" class="m-0" onsubmit="return confirm('Confirma o aceite dos valores? Um consultor da agência entrará em contato para o faturamento.')">
                <button type="submit" class="btn btn-gold bg-gold text-dark fw-bold"><i class="bi bi-check-circle-fill"></i> Aprovar Cotação</button>
            </form>
        @endif
    </div>
</div>

@if(isset($_SESSION['success']))
    <div class="alert alert-success bg-success text-white border-0">{{ $_SESSION['success'] }}</div>
    @php unset($_SESSION['success']); @endphp
@endif

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
                    O aceite dos valores via botão indicará a autorização para a emissão de nota e faturamento.
                </p>
                @if($quotation->status === 'rejected' && $quotation->client_notes)
                    <div class="mt-4 p-3 rounded alert-danger bg-danger text-white border-0 bg-opacity-10 border-danger border-start border-4">
                        <span class="d-block mb-1 small fw-bold text-danger"><i class="bi bi-exclamation-triangle"></i> Motivo da Recusa / Notas do Cliente:</span>
                        {{ $quotation->client_notes }}
                    </div>
                @endif
                @if($quotation->briefing)
                    <div class="mt-4 p-3 rounded" style="background-color: #1e293b; border: 1px dashed #334155;">
                        <span class="text-white small d-block mb-1"><i class="bi bi-link-45deg text-gold"></i> Atrelado ao Projeto:</span>
                        <a href="/cliente/briefings/{{ $quotation->briefing_id }}" class="text-decoration-none fw-bold text-info">{{ $quotation->briefing->title }}</a>
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

<!-- Reject Modal -->
@if($quotation->status === 'sent')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <form action="/cliente/quotations/{{ $quotation->id }}/reject" method="POST">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white"><i class="bi bi-x-circle text-danger me-2"></i> Recusar Orçamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-white">
                    <p class="small text-muted mb-4">Caso deseje, deixe um comentário para a Agência relatando o motivo de não prosseguir com esta proposta (ex: Orçamento acima do esperado, desejo remover o Item 3 do escopo para baratear, etc).</p>
                    
                    <div class="mb-3">
                        <label class="form-label text-gold">Comentário / Solicitação de Ajuste (Opcional)</label>
                        <textarea name="client_notes" class="form-control bg-dark border-secondary text-white" rows="4" placeholder="Escreva sua mensagem..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Recusa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    @media print {
        body { font-family: 'Inter', sans-serif; background-color: #fff !important; }
        .sidebar, header, .btn, .badge, nav { display: none !important; }
        .content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; margin-top: 0 !important; }
        .card { box-shadow: none !important; color: #000 !important; background-color: #fff !important; }
        .text-white { color: #000 !important; }
        .text-gold { color: #8c7322 !important; }
        .bg-dark, #0f172a, #1e293b { background-color: #f8f9fa !important; }
        .border-secondary { border-color: #dee2e6 !important; }
        .text-muted { color: #6c757d !important; }
    }
</style>
@endsection
