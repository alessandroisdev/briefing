@extends('layouts.admin')

@section('title', 'Notificações - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-white mb-0"><i class="bi bi-bell me-2"></i> Notificações</h2>
        <p style="color: #94a3b8;">Histórico e alertas de atividades dos clientes</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/notifications/read-all" class="btn btn-outline-secondary shadow-sm text-white">
            <i class="bi bi-check2-all"></i> Marcar todas como lidas
        </a>
    </div>
</header>

<div class="row">
    <div class="col-12">
        <div class="card briefing-card border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <div class="list-group list-group-flush rounded-bottom">
                @forelse($notifications as $notif)
                    @php
                        $isUnread = is_null($notif->read_at);
                        $bgClass = $isUnread ? 'bg-dark bg-opacity-50' : 'bg-transparent';
                        $iconClass = match($notif->type) {
                            'success' => 'bi-check-circle-fill text-success',
                            'warning' => 'bi-exclamation-triangle-fill text-warning',
                            'alert', 'error' => 'bi-x-octagon-fill text-danger',
                            default => 'bi-info-circle-fill text-info'
                        };
                    @endphp
                    <a href="{{ $notif->action_url ? '/admin/notifications/'.$notif->id.'/read' : '#' }}" 
                       class="list-group-item list-group-item-action {{ $bgClass }} px-4 py-3 border-secondary text-white" 
                       style="border-color: rgba(255,255,255,0.05) !important;">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="d-flex gap-3">
                                <i class="bi {{ $iconClass }} fs-4 mt-1"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold {{ $isUnread ? 'text-white' : 'text-secondary' }}">{{ $notif->title }}</h6>
                                    <p class="mb-1" style="color: #cbd5e1; font-size: 0.95rem;">{!! $notif->message !!}</p>
                                    <small class="text-muted"><i class="bi bi-clock"></i> {{ date('d/m/Y às H:i', strtotime($notif->created_at)) }}</small>
                                </div>
                            </div>
                            @if($isUnread)
                                <span class="badge bg-danger rounded-pill mt-2">Nova</span>
                            @else
                                <span class="badge bg-secondary rounded-pill mt-2 fw-normal"><i class="bi bi-check2"></i> Lida</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-5 text-center">
                        <i class="bi bi-bell-slash text-secondary" style="font-size: 3rem;"></i>
                        <h5 class="text-white mt-3">Você não tem notificações!</h5>
                        <p style="color: #94a3b8;">Nenhum evento recente registrado pelo painel.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
