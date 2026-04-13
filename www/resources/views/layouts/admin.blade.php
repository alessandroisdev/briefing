@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    /* Ocultar navbar padrão no layout admin para usar a sidebar */
    .navbar { display: none !important; }
</style>
@endsection

@section('content')
<div class="admin-layout mx-n3" style="max-width: 100vw; margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%); height: 100vh; background-color: #09101f;">
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="/admin/dashboard" class="text-decoration-none d-block mb-5 px-3 mt-3">
            <h4 class="fw-bold text-white m-0">Briefing<span style="color: #D4AF37;">App</span></h4>
            <small style="color: #94a3b8;">Admin Panel</small>
        </a>
        
        <nav class="d-flex flex-column gap-2 mb-auto">
            <a href="/admin/dashboard" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') === 0 ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <a href="/admin/clients" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/clients') === 0 ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clientes
            </a>
            <a href="/admin/templates" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/templates') === 0 && strpos($_SERVER['REQUEST_URI'], '/admin/templates/messages') === false ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> Modelos de Briefing
            </a>
            <a href="/admin/templates/messages" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/templates/messages') === 0 ? 'active' : '' }}">
                <i class="bi bi-chat-square-quote"></i> Respostas Rápidas
            </a>
            <a href="/admin/briefings" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/briefings') === 0 ? 'active' : '' }}">
                <i class="bi bi-ui-checks"></i> Projetos em Andamento
            </a>
            <a href="/admin/tickets" class="sidebar-link {{ strpos($_SERVER['REQUEST_URI'], '/admin/tickets') === 0 ? 'active' : '' }}">
                <i class="bi bi-headset"></i> Tickets de Suporte
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
            // Correção absoluta para pegar Notificações do ADMIN (Nível MVP)
            $adminUser = \App\Models\User::where('role', \App\Enums\UserRole::Admin->value)->first();
            $adminUserId = $adminUser ? $adminUser->id : 1; 

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
                <button class="btn btn-link p-0 text-decoration-none position-relative text-white" type="button" id="notificationDropdown" onclick="toggleBellDropdown(event)" aria-expanded="false" data-bs-display="static" style="font-size:1.3rem; border: none; box-shadow: none;">
                    <!-- Inline SVG para garantir visualização independente de CDNs e imports de CSS -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                      <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                    </svg>
                    
                    <span id="bellCounters" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem; padding: 0.3em 0.5em; {{ $notifCount == 0 ? 'display:none;' : '' }}">
                        {{ $notifCount }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 450px; overflow-y: auto; background-color: #1e293b; z-index: 1050 !important;">
                    <li class="px-4 py-3 border-bottom border-secondary d-flex justify-content-between align-items-center" style="border-color: rgba(255,255,255,0.05) !important;">
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
<script>
    function toggleBellDropdown(e) {
        e.preventDefault();
        e.stopPropagation();
        var bellBtn = document.getElementById('notificationDropdown');
        if (window.bootstrap) {
            var bsDropdown = bootstrap.Dropdown.getInstance(bellBtn) || new bootstrap.Dropdown(bellBtn);
            bsDropdown.toggle();
        }
    }
</script>
@yield('admin_scripts')
@endsection
