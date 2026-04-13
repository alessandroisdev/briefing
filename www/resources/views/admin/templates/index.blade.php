@extends('layouts.admin')

@section('title', 'Modelos de Briefing - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0">Modelos de Briefing</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Crie e gerencie templates dinâmicos de formulários</p>
    </div>
    <div>
        <a href="/admin/templates/create" class="btn btn-gold shadow-sm px-4">
            <i class="bi bi-file-earmark-plus"></i> Novo Modelo
        </a>
    </div>
</header>

<div class="card briefing-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Título do Modelo</th>
                        <th>Campos Dinâmicos</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr>
                        <td><span class="text-muted">#{{ $template->id }}</span></td>
                        <td>
                            <strong class="text-white">{{ $template->title }}</strong>
                        </td>
                        <td><span class="badge bg-dark border border-secondary">{{ count($template->form_schema ?? []) }} campos</span></td>
                        <td>
                            @if($template->status?->value === 'active')
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">{{ $template->status?->value }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="/admin/templates/{{ $template->id }}/edit" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-journal-x text-muted fs-1 d-block mb-3"></i>
                            <h5 class="text-white">Nenhum modelo criado.</h5>
                            <p class="text-muted">Comece criando um formulário de briefing padrão.</p>
                            <a href="/admin/templates/create" class="btn btn-outline-gold mt-2">Criar Modelo</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
