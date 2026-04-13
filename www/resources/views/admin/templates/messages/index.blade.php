@extends('layouts.admin')

@section('title', 'Respostas Rápidas - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0">Respostas Rápidas</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Ferramentas de chat para agilizar mensagens padrão</p>
    </div>
    <div>
        <a href="/admin/templates/messages/create" class="btn btn-gold shadow-sm px-4">
            <i class="bi bi-plus-lg"></i> Novo Modelo
        </a>
    </div>
</header>

@if(isset($_SESSION['success']))
    <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['success'] }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php unset($_SESSION['success']); @endphp
@endif

<div class="card briefing-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Título de Identificação</th>
                        <th>Conteúdo Previsto</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr>
                        <td><strong class="text-white">{{ $template->title }}</strong></td>
                        <td>
                            <div class="text-muted small" style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ strip_tags($template->content) }}
                            </div>
                        </td>
                        <td>
                            @if($template->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="/admin/templates/messages/{{ $template->id }}/edit" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="bi bi-chat-square-quote text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-white">Nenhuma resposta rápida cadastrada.</h5>
                            <p class="text-muted">Crie templates para agilizar a documentação e respostas de dúvidas comuns.</p>
                            <a href="/admin/templates/messages/create" class="btn btn-outline-gold mt-2">Criar Modelo Base</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
