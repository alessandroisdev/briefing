@extends('layouts.app')

@section('title', 'Portal do Cliente - BriefingApp')

@section('nav_links')
    <li class="nav-item">
        <a class="nav-link active" href="/cliente/dashboard">Meus Projetos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/cliente/perfil"><i class="bi bi-person-circle"></i> Perfil</a>
    </li>
    <li class="nav-item ms-3">
        <a class="btn btn-outline-danger btn-sm mt-1" href="/">Sair</a>
    </li>
@endsection

@section('content')
<div class="row pt-5">
    <div class="col-12 mb-4">
        <h2 class="text-white fw-bold">Meus Painéis & Briefings</h2>
        <p style="color: #94a3b8;">Veja o andamento dos seus projetos e responda aos formulários de briefing solicitados.</p>
    </div>

    <!-- Tabela do Cliente -->
    <div class="col-12">
        
        <!-- Toolbar de Busca/Filtro -->
        <div class="card bg-dark border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row gx-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control bg-transparent text-white border-secondary" placeholder="Buscar por nome do projeto ou status...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="statusFilter" class="form-select bg-transparent text-white border-secondary">
                            <option value="all" class="bg-dark text-white">Todos os Status</option>
                            <option value="Aguardando Respostas" class="bg-dark text-white">Aguardando Respostas</option>
                            <option value="Em Preenchimento" class="bg-dark text-white">Em Preenchimento</option>
                            <option value="Enviado para Análise" class="bg-dark text-white">Enviado para Análise</option>
                            <option value="Aprovado" class="bg-dark text-white">Aprovado</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card briefing-card border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Projeto / Briefing</th>
                            <th>Status</th>
                            <th>Última Atualização</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($briefings as $briefing)
                        @php
                            $statusInfo = match($briefing->status?->value) {
                                'pending' => ['bg' => 'bg-warning text-dark', 'text' => 'Aguardando Respostas', 'btn' => 'Preencher', 'btn_class' => 'btn-gold', 'icon' => 'bi-pencil-square'],
                                'in_progress' => ['bg' => 'bg-info text-dark', 'text' => 'Em Preenchimento', 'btn' => 'Continuar', 'btn_class' => 'btn-outline-info', 'icon' => 'bi-pencil'],
                                'submitted' => ['bg' => 'bg-success', 'text' => 'Enviado para Análise', 'btn' => 'Visualizar', 'btn_class' => 'btn-outline-light', 'icon' => 'bi-eye'],
                                'approved' => ['bg' => 'bg-primary', 'text' => 'Aprovado', 'btn' => 'Acessar', 'btn_class' => 'btn-outline-light', 'icon' => 'bi-check2-circle'],
                                default => ['bg' => 'bg-secondary', 'text' => ucfirst($briefing->status?->value), 'btn' => 'Ver', 'btn_class' => 'btn-outline-light', 'icon' => 'bi-eye'],
                            };
                        @endphp
                        <tr class="briefing-row" data-search-content="{{ strtolower($briefing->title . ' ' . ($briefing->template->name ?? 'Formulário Personalizado')) }}" data-status="{{ $statusInfo['text'] }}">
                            <td>
                                <strong class="text-white searchable">{{ $briefing->title }}</strong><br>
                                <small style="color: #94a3b8;" class="searchable">{{ $briefing->template->name ?? 'Formulário Personalizado' }}</small>
                            </td>
                            <td><span class="badge {{ $statusInfo['bg'] }}">{{ $statusInfo['text'] }}</span></td>
                            <td>{{ date('d/m/Y H:i', strtotime($briefing->updated_at)) }}</td>
                            <td class="text-end">
                                <a href="/cliente/briefings/{{ $briefing->id }}" class="btn btn-sm {{ $statusInfo['btn_class'] }}">
                                    <i class="bi {{ $statusInfo['icon'] }}"></i> {{ $statusInfo['btn'] }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-secondary">
                                <i class="bi bi-folder-x fs-1"></i><br>
                                Nenhum projeto de briefing disponível no momento.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <h4 class="text-white mb-4"><i class="bi bi-receipt text-gold me-2"></i> Orçamentos e Faturas</h4>
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead style="background-color: #0f172a;">
                        <tr>
                            <th>Nº Orçamento</th>
                            <th>Status</th>
                            <th>Valor Total</th>
                            <th>Validade</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $q)
                        <tr>
                            <td class="text-white">
                                <strong class="fs-6">#{{ str_pad($q->id, 5, '0', STR_PAD_LEFT) }}</strong><br>
                                <small class="text-muted">{{ $q->title }}</small>
                            </td>
                            <td>
                                @if($q->status === 'sent') <span class="badge bg-info text-dark mt-2">Aguardando Avaliação</span>
                                @elseif($q->status === 'accepted') <span class="badge bg-success mt-2">Aprovado</span>
                                @else <span class="badge bg-secondary mt-2">{{ $q->status }}</span> @endif
                            </td>
                            <td class="text-gold fw-bold align-middle">R$ {{ number_format($q->total_amount, 2, ',', '.') }}</td>
                            <td class="text-muted small align-middle">{{ date('d/m/Y', strtotime($q->valid_until)) }}</td>
                            <td class="text-end align-middle">
                                <a href="/cliente/quotations/{{ $q->id }}" class="btn btn-sm btn-outline-light">
                                    <i class="bi bi-file-earmark-pdf"></i> Visualizar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="bi bi-inbox fs-1"></i><br>
                                Você ainda não possui orçamentos pendentes.
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

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
