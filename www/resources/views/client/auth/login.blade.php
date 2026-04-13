@extends('layouts.app')

@section('title', 'Login do Cliente - BriefingApp')

@section('content')
<div class="row min-vh-100 justify-content-center align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card briefing-card p-4 p-md-5">
            <h3 class="text-center mb-4 fw-bold text-white">Acesso do Cliente</h3>
            <p class="text-center text-muted mb-4 small" style="color: #94a3b8 !important;">Utilize seus dados para acompanhar o progresso dos seus projetos.</p>
            <form action="/cliente/login" method="POST" id="loginForm">
                <div class="mb-4">
                    <label class="form-label ps-1">Identificação</label>
                    <input type="text" id="identificacao" name="identification" class="form-control form-control-lg" required placeholder="Email, Telefone ou CPF/CNPJ">
                </div>
                <!-- Código temporário ou senha -->
                <div class="mb-4" id="password-field">
                    <label class="form-label ps-1">Código Temporário ou Senha</label>
                    <input type="password" name="code" class="form-control form-control-lg" placeholder="••••••••">
                </div>
                <div class="d-grid mb-4 mt-2">
                    <button type="submit" class="btn btn-gold btn-lg fw-semibold d-flex justify-content-center align-items-center gap-2">
                        Acessar Portal
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" onclick="requestMagicLink()" class="btn btn-link text-decoration-none" style="color: #D4AF37; font-size: 0.9rem;">Receber código mágico por email</button>
                </div>
            </form>

            <form action="/cliente/login/magic-link" method="POST" id="magicLinkForm" class="d-none">
                <input type="hidden" name="identificacao" id="magicLinkInput">
                <button type="submit" id="btnMagicLinkHidden">Enviar</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function requestMagicLink() {
    const ident = document.getElementById('identificacao').value;
    if (!ident) {
        alert("Por favor, preencha o campo Identificação primeiro.");
        document.getElementById('identificacao').focus();
        return;
    }
    document.getElementById('magicLinkInput').value = ident;
    document.getElementById('btnMagicLinkHidden').click();
}
</script>
