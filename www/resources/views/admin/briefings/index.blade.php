@extends('layouts.admin')

@section('title', 'Projetos e Briefings - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0">Projetos em Andamento</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Controle todos os briefings enviados e acompanhe o status</p>
    </div>
    <div>
        <a href="/admin/briefings/create" class="btn btn-gold shadow-sm px-4">
            <i class="bi bi-send-plus-fill"></i> Iniciar Projeto para Cliente
        </a>
    </div>
</header>

<!-- Toolbar de Busca/Filtro -->
<div class="card bg-dark border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row gx-3">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-transparent text-white border-secondary" placeholder="Buscar por projeto, cliente, template...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-select bg-transparent text-white border-secondary">
                    <option value="all" class="bg-dark text-white">Todos os Status</option>
                    <option value="CRIADO" class="bg-dark text-white">CRIADO</option>
                    <option value="EDITANDO" class="bg-dark text-white">EDITANDO</option>
                    <option value="EXECUTANDO" class="bg-dark text-white">EXECUTANDO</option>
                    <option value="CANCELADO" class="bg-dark text-white">CANCELADO</option>
                    <option value="FINALIZADO" class="bg-dark text-white">FINALIZADO</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card briefing-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Projeto / Título</th>
                        <th>Cliente</th>
                        <th>Modelo (Template)</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($briefings as $briefing)
                    <tr class="briefing-row" data-search-content="{{ strtolower($briefing->title . ' ' . ($briefing->client->company_name ?? '') . ' ' . ($briefing->client->user->name ?? '') . ' ' . ($briefing->template->title ?? '')) }}" data-status="{{ strtoupper($briefing->status?->value) }}">
                        <td><span class="text-muted">#{{ $briefing->id }}</span></td>
                        <td>
                            <strong class="text-white">{{ $briefing->title }}</strong>
                        </td>
                        <td>
                            <div class="text-white">{{ $briefing->client->company_name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $briefing->client->user->name ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-dark border border-secondary">{{ $briefing->template->title ?? 'Deletado' }}</span></td>
                        <td>
                            @php
                                $statusColors = [
                                    'criado' => 'bg-secondary',
                                    'editando' => 'bg-warning text-dark',
                                    'executando' => 'bg-primary',
                                    'cancelado' => 'bg-danger',
                                    'finalizado' => 'bg-success'
                                ];
                                $color = $statusColors[$briefing->status?->value] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $color }}">{{ strtoupper($briefing->status?->value) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="/admin/briefings/{{ $briefing->id }}" class="btn btn-sm btn-outline-gold">
                                <i class="bi bi-eye"></i> Analisar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-ui-radios text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-white">Nenhum projeto em andamento.</h5>
                            <p class="text-muted">Associe um modelo de briefing a um cliente para iniciar.</p>
                            <a href="/admin/briefings/create" class="btn btn-outline-gold mt-2">Iniciar Primeiro Projeto</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const rows = document.querySelectorAll('.briefing-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusTerm = statusFilter.value;

            rows.forEach(row => {
                const textContent = row.getAttribute('data-search-content');
                const rowStatus = row.getAttribute('data-status');
                
                const matchesSearch = textContent.includes(searchTerm);
                const matchesStatus = (statusTerm === 'all' || rowStatus === statusTerm);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);
    });
</script>
@endsection
