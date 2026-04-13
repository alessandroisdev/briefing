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
                        <tr>
                            <td>
                                <strong class="text-white">{{ $briefing->title }}</strong><br>
                                <small style="color: #94a3b8;">{{ $briefing->template->name ?? 'Formulário Personalizado' }}</small>
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
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection
