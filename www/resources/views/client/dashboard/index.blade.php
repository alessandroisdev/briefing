@extends('layouts.app')

@section('title', 'Portal do Cliente - BriefingApp')

@section('nav_links')
    <li class="nav-item">
        <a class="nav-link active" href="/cliente/dashboard">Meus Projetos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-person-circle"></i> Perfil</a>
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
                        <!-- Mock data for Phase 3 -->
                        <tr>
                            <td>
                                <strong class="text-white">Briefing de Identidade Visual</strong><br>
                                <small style="color: #94a3b8;">Criação de Logo e Manual da Marca</small>
                            </td>
                            <td><span class="badge bg-warning text-dark">Aguardando Respostas</span></td>
                            <td>Hoje às 10:45</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-gold"><i class="bi bi-pencil-square"></i> Preencher</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-white">Website Institucional</strong><br>
                                <small style="color: #94a3b8;">Protótipo Figma</small>
                            </td>
                            <td><span class="badge bg-success">Executando</span></td>
                            <td>Ontem</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-light"><i class="bi bi-eye"></i> Visualizar</button>
                            </td>
                        </tr>
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
