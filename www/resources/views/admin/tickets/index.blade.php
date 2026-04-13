@extends('layouts.admin')

@section('content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0">Help Desk Tickets</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Gerencie e responda as dúvidas e problemas dos clientes</p>
    </div>
</header>

<div class="card briefing-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Cliente</th>
                        <th>Assunto</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th>Atualizado em</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><span class="text-muted">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 0.8rem; border: 1px solid #D4AF37;">
                                    {{ strtoupper(substr($ticket->client->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white" style="font-weight: 600;">{{ $ticket->client->user->name }}</h6>
                                    <small class="text-muted">{{ $ticket->client->company_name ?? 'Pessoa Física' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong class="text-white">{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</strong>
                        </td>
                        <td>
                            @php
                                $statusMaps = [
                                    'open' => ['bg' => 'bg-danger text-white', 'label' => 'Aberto (Nova)'],
                                    'answered' => ['bg' => 'bg-success text-white', 'label' => 'Respondido (Aguardando)'],
                                    'waiting_client' => ['bg' => 'bg-warning text-dark', 'label' => 'Aguardando Cliente'],
                                    'closed' => ['bg' => 'bg-secondary text-white', 'label' => 'Encerrado']
                                ];
                                $sInfo = $statusMaps[$ticket->status?->value] ?? ['bg'=>'bg-secondary', 'label'=>'Desconhecido'];
                            @endphp
                            <span class="badge {{ $sInfo['bg'] }} px-2 py-1">{{ $sInfo['label'] }}</span>
                        </td>
                        <td>
                            @php
                                $priorityColors = [
                                    'low' => 'bg-secondary',
                                    'normal' => 'bg-info text-dark',
                                    'high' => 'bg-warning text-dark',
                                    'urgent' => 'bg-danger'
                                ];
                                $pColor = $priorityColors[$ticket->priority?->value] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $pColor }} px-2 py-1"><i class="bi bi-flag-fill me-1"></i> {{ strtoupper($ticket->priority?->value) }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ date('d/m/Y H:i', strtotime($ticket->updated_at)) }}
                        </td>
                        <td class="pe-4 text-end">
                            <a href="/admin/tickets/{{ $ticket->id }}" class="btn btn-sm btn-outline-gold">
                                <i class="bi bi-eye"></i> Visualizar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-ui-radios text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-white">Nenhum chamado de suporte na fila. Viva!</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
