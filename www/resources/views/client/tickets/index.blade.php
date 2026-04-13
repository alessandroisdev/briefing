@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="text-white fw-bold mb-1">Central de Suporte</h2>
            <p class="text-muted mb-0" style="color: #94a3b8 !important;">Acompanhe o andamento das suas solicitações</p>
        </div>
        <div class="col-auto">
            <a href="/cliente/suporte/novo" class="btn btn-gold px-4 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i>Abrir Chamado
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-dark border-secondary h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tickets Abertos</h6>
                    <h3 class="text-white mb-0">{{ $tickets->where('status', \App\Enums\TicketStatus::Open)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark border-secondary h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Aguardando Sua Resposta</h6>
                    <h3 class="text-white mb-0" style="color: #D4AF37 !important;">{{ $tickets->where('status', \App\Enums\TicketStatus::WaitingClient)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark border-secondary h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Encerrados</h6>
                    <h3 class="text-white mb-0">{{ $tickets->where('status', \App\Enums\TicketStatus::Closed)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Ticket</th>
                            <th>Assunto</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th>Última Atualização</th>
                            <th class="text-end pe-4">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4 text-muted">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <strong class="text-white">{{ $ticket->subject }}</strong>
                            </td>
                            <td>
                                @php
                                    $priorityColors = [
                                        'low' => 'bg-secondary',
                                        'normal' => 'bg-primary',
                                        'high' => 'bg-warning text-dark',
                                        'urgent' => 'bg-danger'
                                    ];
                                    $pColor = $priorityColors[$ticket->priority?->value] ?? 'bg-secondary';
                                    
                                    $statusMaps = [
                                        'open' => ['bg' => 'bg-info text-dark', 'label' => 'Aberto (Agência Analisando)'],
                                        'answered' => ['bg' => 'bg-success', 'label' => 'Respondido (Agência)'],
                                        'waiting_client' => ['bg' => 'bg-warning text-dark', 'label' => 'Aguardando Sua Resposta'],
                                        'closed' => ['bg' => 'bg-secondary', 'label' => 'Encerrado']
                                    ];
                                    $sInfo = $statusMaps[$ticket->status?->value] ?? ['bg'=>'bg-secondary', 'label'=>'Desconhecido'];
                                @endphp
                                <span class="badge {{ $pColor }}">{{ strtoupper($ticket->priority?->value) }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $sInfo['bg'] }}">{{ $sInfo['label'] }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ date('d/m/Y H:i', strtotime($ticket->updated_at)) }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="/cliente/suporte/{{ $ticket->id }}" class="btn btn-sm btn-outline-light">
                                    <i class="bi bi-chat-text"></i> Interagir
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                                Nenhum ticket de suporte encontrado.<br>
                                <a href="/cliente/suporte/novo" class="text-gold text-decoration-none mt-2 d-inline-block">Abrir um chamado agora</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
