@extends('layouts.admin')

@section('title', 'Admin Dashboard - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0">Visão Geral</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Acompanhe as métricas e acessos da plataforma</p>
    </div>
    <div>
        <a href="/admin/clients/create" class="btn btn-gold shadow-sm px-4">
            <i class="bi bi-plus-lg"></i> Novo Cliente
        </a>
    </div>
</header>

<!-- Métricas -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card briefing-card h-100 p-4">
            <h6 style="color: #94a3b8;">Clientes Totais</h6>
            <h2 class="display-5 fw-bold text-white mb-0">{{ $metrics['total_clients'] }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card briefing-card h-100 p-4 border-start border-warning border-4" style="border-left-color: #D4AF37 !important;">
            <h6 style="color: #94a3b8;">Aprovações Pendentes</h6>
            <h2 class="display-5 fw-bold text-white mb-0">{{ $metrics['pending_approvals'] }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card briefing-card h-100 p-4">
            <h6 style="color: #94a3b8;">Briefings Ativos</h6>
            <h2 class="display-5 fw-bold text-white mb-0">{{ $metrics['active_briefings'] }}</h2>
        </div>
    </div>
</div>

<!-- Tabela Rápida -->
<div class="card briefing-card">
    <div class="card-header border-0 bg-transparent p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-white mb-0">Últimos Clientes</h5>
        <a href="/admin/clients" class="btn btn-sm btn-outline-gold">Ver Todos</a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark-custom mb-0">
            <thead>
                <tr>
                    <th>Nome/Empresa</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>{{ $client->company_name ?? 'N/A' }} <br> <small class="text-muted">{{ $client->user->name ?? '' }}</small></td>
                    <td>{{ $client->user->email ?? '' }}</td>
                    <td>
                        @if($client->status?->value === 'active')
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">{{ $client->status?->value }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="/admin/clients/{{ $client->id }}/edit" class="btn btn-sm btn-outline-light"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Nenhum cliente cadastrado ainda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
