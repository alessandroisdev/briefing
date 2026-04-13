@extends('layouts.admin')

@section('title', 'Gestão de Clientes - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0">Gestão de Clientes</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Administre contatos, acessos mágicos e status de projetos</p>
    </div>
        <a href="/admin/clients/create" class="btn btn-gold shadow-sm px-4">
            <i class="bi bi-person-plus-fill"></i> Novo Cliente
        </a>
    </div>
</header>

<div class="row mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="input-group">
            <span class="input-group-text bg-black border-secondary text-muted border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="liveSearchInput" class="form-control bg-black border-secondary border-start-0 text-white shadow-none ps-0" placeholder="Buscar cliente por nome ou email...">
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
                        <th>Empresa / Cliente</th>
                        <th>Contato</th>
                        <th>Token Acesso</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr class="client-row" data-searchable="{{ strtolower(htmlspecialchars(($client->company_name ?? '') . ' ' . ($client->user->name ?? '') . ' ' . ($client->user->email ?? '') . ' ' . ($client->user->phone ?? ''))) }}">
                        <td><span class="text-muted">#{{ $client->id }}</span></td>
                        <td>
                            <strong class="text-white">{{ $client->company_name ?? 'Não Informado' }}</strong>
                            <br>
                            <small style="color: #94a3b8;">{{ $client->user->name ?? '' }}</small>
                        </td>
                        <td>
                            <div><i class="bi bi-envelope text-muted"></i> {{ $client->user->email ?? 'N/A' }}</div>
                            @if($client->user->phone)
                            <div><i class="bi bi-telephone text-muted"></i> {{ $client->user->phone }}</div>
                            @endif
                        </td>
                        <td>
                            @if($client->user->magic_link_token)
                                <span class="badge bg-primary">Ativo</span>
                                <small class="d-block text-muted mt-1">{{ $client->user->magic_link_expires ? $client->user->magic_link_expires->format('d/m H:i') : '' }}</small>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </td>
                        <td>
                            @if($client->status?->value === 'active')
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">{{ $client->status?->value }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="/admin/clients/{{ $client->id }}/magic-link" class="btn btn-sm btn-outline-info" title="Gerar Magic Link">
                                    <i class="bi bi-link-45deg"></i>
                                </a>
                                <a href="/admin/clients/{{ $client->id }}/edit" class="btn btn-sm btn-outline-light" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-people text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-white">Nenhum cliente cadastrado</h5>
                            <p class="text-muted">Comece adicionando seu primeiro cliente para gerenciar projetos.</p>
                            <a href="/admin/clients/create" class="btn btn-outline-gold mt-2">Criar Cliente</a>
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearchInput');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                const term = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('.client-row');
                
                rows.forEach(row => {
                    const searchableText = row.getAttribute('data-searchable') || '';
                    if (searchableText.toLowerCase().includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection
