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
                    <!-- Links padrão ou dinâmicos -->
                    @yield('nav_links')
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <!-- Toast de Notificações em Tempo Real -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-white bg-gold border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #D4AF37 !important; color: #09101f !important;">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    <i class="bi bi-bell-fill me-2"></i> <span id="toastMessage">Nova notificação!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS + Quill -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @yield('scripts')

    <!-- SSE Listener para Notificações ao Cliente -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const evtSource = new EventSource("/sse/stream");
            
            evtSource.onmessage = function(event) {
                if(!event.data) return;

                try {
                    const data = JSON.parse(event.data);
                    
                    if (data.event === 'status_changed' || data.message) {
                        const toastEl = document.getElementById('liveToast');
                        document.getElementById('toastMessage').innerText = data.message;
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
                        
                        // Atualiza a tela se o cliente estiver focado na página sendo notificada
                        if(data.briefing_id && window.location.href.includes('briefings/' + data.briefing_id)) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 3500);
                        }
                    }
                } catch(e) {
                    console.error("Non-JSON message received:", event.data);
                }
            };
        });
    </script>
</body>
</html>
