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
    <main class="main-content d-flex flex-column" style="overflow-y: auto;">
        
        @php
            $adminUserId = $_SESSION['admin_id'] ?? ($_SESSION['client_id'] ?? 0); // Temporary fallback while true admin auth config is fuzzy
            $unreadNotifs = \App\Models\Notification::where('user_id', $adminUserId)
                                ->whereNull('read_at')
                                ->orderBy('id', 'desc')
                                ->get();
            $notifCount = count($unreadNotifs);
            $previewNotifs = $unreadNotifs->take(5);
        @endphp

        <!-- Topbar for Admin -->
        <header class="d-flex justify-content-end align-items-center mb-4 px-4 pt-3 w-100">
            <div class="dropdown">
                <a class="text-decoration-none position-relative text-white dropdown-toggle" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.3rem;">
                    <i class="bi bi-bell"></i>
                    <span id="bellCounters" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem; padding: 0.3em 0.5em; {{ $notifCount == 0 ? 'display:none;' : '' }}">
                        {{ $notifCount }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; background-color: #212529;">
                    <li class="px-3 py-2 border-bottom border-secondary d-flex justify-content-between align-items-center">
                        <span class="text-white fw-bold">Notificações</span>
                        <span class="badge bg-danger" id="bellCounterText">{{ $notifCount }} novas</span>
                    </li>
                    <div id="notifListContainer">
                        @forelse($previewNotifs as $notif)
                        <li>
                            <a class="dropdown-item py-2 text-wrap" href="/admin/notifications/{{ $notif->id }}/read" style="color: #cbd5e1; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-white fw-bold">{{ $notif->title }}</small>
                                    <small style="font-size:0.65rem;" class="text-muted">{{ date('d/m H:i', strtotime($notif->created_at)) }}</small>
                                </div>
                                <small style="font-size:0.8rem; line-height: 1.1; display: block; margin-top: 4px;">{!! $notif->message !!}</small>
                            </a>
                        </li>
                        @empty
                        <li id="noNotifsItem" class="text-center py-4 px-3">
                            <i class="bi bi-bell-slash text-secondary fs-3 mb-2 d-block"></i>
                            <small class="text-muted">Nenhuma notificação nova.</small>
                        </li>
                        @endforelse
                    </div>
                    <li><hr class="dropdown-divider border-secondary my-1"></li>
                    <li><a class="dropdown-item text-center fw-bold py-2" href="/admin/notifications" style="color: #D4AF37;"><i class="bi bi-list-check"></i> Ver todas</a></li>
                </ul>
            </div>
        </header>

        <div class="px-4 pb-4">
            @yield('admin_content')
        </div>
    </main>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@yield('admin_scripts')
@endsection
