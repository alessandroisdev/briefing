@extends('layouts.app')

@section('styles')
<style>
    /* Ocultar navbar padrão no layout admin para usar a sidebar */
    .navbar { display: none !important; }
</style>
@endsection

@section('content')
<div class="admin-layout mx-n3" style="max-width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); height: 100vh;">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="mb-5 px-3 mt-3">
            <h4 class="fw-bold text-white m-0">Briefing<span style="color: #D4AF37;">App</span></h4>
            <small style="color: #94a3b8;">Admin Panel</small>
        </div>
        
        <nav class="d-flex flex-column gap-2 mb-auto">
            <a href="/admin/dashboard" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') === 0 ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <a href="/admin/clients" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/clients') === 0 ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clientes
            </a>
            <a href="/admin/templates" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/templates') === 0 ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> Modelos de Briefing
            </a>
            <a href="/admin/briefings" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/briefings') === 0 ? 'active' : '' }}">
                <i class="bi bi-ui-checks"></i> Projetos em Andamento
            </a>
            <div class="mt-4 mb-2 small text-uppercase text-muted fw-bold px-3">Sistema</div>
            <a href="/admin/queue" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/queue') === 0 ? 'active' : '' }}">
                <i class="bi bi-cloud-arrow-up"></i> Fila de E-mails
            </a>
            <a href="/admin/settings/email" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/settings/email') === 0 ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Config. Servidor SMTP
            </a>
        </nav>
        
        <div class="mt-auto">
            <a href="/" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('admin_content')
    </main>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@yield('admin_scripts')
@endsection
