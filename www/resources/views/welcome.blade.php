@extends('layouts.app')

@section('title', 'Página Inicial - BriefingApp')

@section('nav_links')
    <li class="nav-item">
        <a class="nav-link" href="/cliente/login">Área do Cliente</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin/login">Painel Admin</a>
    </li>
@endsection

@section('content')
<div class="row min-vh-100 align-items-center justify-content-center">
    <div class="col-md-7 text-center">
        <h1 class="display-3 fw-bold text-white mb-3">Eleve a Gestão dos Seus Projetos</h1>
        <p class="lead mb-5" style="color: #94a3b8;">Plataforma premium para acompanhamento de escopos, coleta de briefings elegantes e suporte VIP para seus melhores clientes.</p>
        
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-4">
            <a href="/cliente/login" class="btn btn-primary px-5 py-3 fs-5 rounded-pill shadow-lg d-flex align-items-center justify-content-center">
                Portal do Cliente
            </a>
            <a href="/admin/login" class="btn btn-outline-gold px-5 py-3 fs-5 rounded-pill shadow-lg d-flex align-items-center justify-content-center">
                Acesso Restrito
            </a>
        </div>
    </div>
</div>
@endsection
