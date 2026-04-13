<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Briefing')</title>
    @php
        $manifestPath = '/var/www/html/public/build/.vite/manifest.json';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $cssFile = $manifest['sass/app.scss']['file'] ?? '';
            $jsFile = $manifest['js/app.ts']['file'] ?? '';
            echo '<link rel="stylesheet" href="/build/' . $cssFile . '">';
            echo '<script type="module" src="/build/' . $jsFile . '"></script>';
        } else {
            echo '<!-- No Manifest Found -->';
            echo '<script type="module" src="http://localhost:5173/build/@vite/client"></script>';
            echo '<script type="module" src="http://localhost:5173/build/resources/js/app.ts"></script>';
            echo '<link rel="stylesheet" href="http://localhost:5173/build/resources/sass/app.scss">';
        }
    @endphp
    @yield('styles')
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">BriefingApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if(session()->has('client_id'))
                        <li class="nav-item">
                            <a class="nav-link {{ strpos($_SERVER['REQUEST_URI'], '/cliente/dashboard') === 0 || strpos($_SERVER['REQUEST_URI'], '/cliente/briefings') === 0 ? 'active' : '' }}" href="/cliente/dashboard">Meus Projetos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ strpos($_SERVER['REQUEST_URI'], '/cliente/suporte') === 0 ? 'active' : '' }}" href="/cliente/suporte"><i class="bi bi-headset"></i> Suporte</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ strpos($_SERVER['REQUEST_URI'], '/cliente/perfil') === 0 ? 'active' : '' }}" href="/cliente/perfil"><i class="bi bi-person-circle"></i> Perfil</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="btn btn-outline-danger btn-sm mt-1" href="/">Sair</a>
                        </li>
                    @else
                        <!-- Links padrão ou dinâmicos -->
                        @yield('nav_links')
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <!-- Toast Container para Múltiplas Notificações -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        
        <!-- Toasts Flash (Sessão PHP) -->
        @php
            $flashMessages = \App\Core\Flash::get();
        @endphp
        
        @foreach($flashMessages as $msg)
            <div class="toast align-items-center text-white bg-{{ $msg['type'] }} border-0 mb-2 active-flash-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ $msg['message'] }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endforeach

        <!-- Toast de Notificações em Tempo Real (SSE) -->
        <div id="liveToast" class="toast align-items-center text-white bg-gold border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #D4AF37 !important; color: #09101f !important;">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    <i class="bi bi-bell-fill me-2"></i> <span id="toastMessage">Nova notificação!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal Global para Confirmação de Ações -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary text-white">
                    <h5 class="modal-title fw-bold" id="confirmModalLabel"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Confirmar Ação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-white" id="confirmModalBody">
                    Tem certeza que deseja continuar com esta ação irreversível?
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" class="btn btn-danger fw-bold" id="confirmModalBtn">Sim, continuar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS + Quill -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @yield('scripts')

    <!-- UI/UX Feedbacks & SSE Listener Globais -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // 1. Mostrar os Flash Toasts vindos do Backend
            const flashToasts = document.querySelectorAll('.active-flash-toast');
            flashToasts.forEach(toastEl => {
                const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                toast.show();
            });

            // 2. Bloquear botões de submit e adicionar SPINNER global (Loading State)
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (this.dataset.noLoading === 'true') return;

                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        // Guarda o texto original e troca por Spinner nativo
                        if (!btn.dataset.originalText) {
                            btn.dataset.originalText = btn.innerHTML;
                        }
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Aguarde...';
                    }
                });
            });

            // 3. Modal de Confirmação para botões perigosos (.btn-delete ou data-confirm="true")
            const confirmButtons = document.querySelectorAll('.btn-delete, [data-confirm="true"]');
            confirmButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const actionUrl = this.href;
                    const message = this.dataset.confirmMessage || 'Tem certeza que deseja continuar com esta ação irreversível?';
                    
                    document.getElementById('confirmModalBody').innerText = message;
                    
                    const confirmModalEl = document.getElementById('confirmModal');
                    const modal = new bootstrap.Toast(confirmModalEl); // workaround fallback, using Modal below
                    const bsModal = new bootstrap.Modal(confirmModalEl);
                    
                    // Adicionando href dinâmico ao botão do modal se for âncora
                    const confirmActionBtn = document.getElementById('confirmModalBtn');
                    
                    if(this.tagName === 'A') {
                        confirmActionBtn.href = actionUrl;
                        confirmActionBtn.onclick = null; // clears previous
                        confirmActionBtn.innerHTML = '<span id="confirm-spinner"></span> Sim, continuar';
                        
                        confirmActionBtn.addEventListener('click', function() {
                            this.classList.add('disabled');
                            document.getElementById('confirm-spinner').className = 'spinner-border spinner-border-sm me-2';
                        });
                    } else if (this.tagName === 'BUTTON' && this.closest('form')) {
                        const parentForm = this.closest('form');
                        confirmActionBtn.href = '#';
                        confirmActionBtn.onclick = function(e) {
                            e.preventDefault();
                            confirmActionBtn.classList.add('disabled');
                            confirmActionBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';
                            parentForm.submit();
                        }
                    }
                    
                    bsModal.show();
                });
            });

            // 4. SSE Listener para Notificações PUSH do Cliente via Redis
            const evtSource = new EventSource("/sse/stream");
            evtSource.onmessage = function(event) {
                if(!event.data) return;
                try {
                    const data = JSON.parse(event.data);
                    if (data.event === 'status_changed' || data.message || data.event === 'queue_updated') {
                        const toastEl = document.getElementById('liveToast');
                        
                        let msg = data.message;
                        if (data.event === 'queue_updated') {
                            msg = `O Evento da fila #${data.job_id} mudou para ${data.status}.`;
                        }

                        if(msg) {
                            document.getElementById('toastMessage').innerText = msg;
                            const toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        }
                        
                        // Manipulação Nativa do Sino de Notificações
                        if (data.event === 'new_notification') {
                            const bellSpan = document.getElementById('bellCounters');
                            const bellText = document.getElementById('bellCounterText');
                            const noNotifsItem = document.getElementById('noNotifsItem');
                            const listContainer = document.getElementById('notifListContainer');

                            if (bellSpan && bellText && listContainer) {
                                bellSpan.style.display = 'inline-block';
                                let count = parseInt(bellSpan.innerText || "0") + 1;
                                bellSpan.innerText = count;
                                bellText.innerText = count + ' novas';

                                if (noNotifsItem) {
                                    noNotifsItem.remove();
                                }

                                // Prepend new notification HTML to dropdown
                                const rawTime = new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
                                const newLi = `
                                <li>
                                    <a class="dropdown-item py-2 text-wrap bg-dark bg-opacity-50" href="/admin/notifications/${data.notification_id}/read" style="color: #cbd5e1; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <div class="d-flex w-100 justify-content-between">
                                            <small class="text-white fw-bold">${data.title}</small>
                                            <small style="font-size:0.65rem;" class="text-warning">Agora</small>
                                        </div>
                                        <small style="font-size:0.8rem; line-height: 1.1; display: block; margin-top: 4px;">${data.message}</small>
                                    </a>
                                </li>`;
                                listContainer.insertAdjacentHTML('afterbegin', newLi);
                            }
                        }

                        if(data.briefing_id && window.location.href.includes('briefings/' + data.briefing_id)) {
                            setTimeout(() => { window.location.reload(); }, 3500);
                        }

                        if(data.event === 'queue_updated' && window.location.href.includes('admin/queue')) {
                            setTimeout(() => { window.location.reload(); }, 1500);
                        }
                    }
                } catch(e) {
                    // Ignore parsings
                }
            };
        });
    </script>
</body>
</html>
