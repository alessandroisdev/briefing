<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Briefing')</title>
    <!-- Incluir Vite. O ViteHelper precisará ser injetado para ler o manifesto na prod, 
         mas no momento vamos simular o dev -->
    @if($_ENV['APP_ENV'] === 'local')
        <script type="module" src="http://localhost:5173/build/@vite/client"></script>
        <script type="module" src="http://localhost:5173/build/resources/js/app.ts"></script>
        <link rel="stylesheet" href="http://localhost:5173/build/resources/sass/app.scss">
    @else
        <!-- Incluir build version do Vite futuramente -->
    @endif
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

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @yield('scripts')
</body>
</html>
