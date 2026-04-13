@extends('layouts.app')

@section('title', 'Meu Perfil - BriefingApp')

@section('nav_links')
    <li class="nav-item">
        <a class="nav-link" href="/cliente/dashboard">Meus Projetos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="/cliente/perfil"><i class="bi bi-person-circle"></i> Perfil</a>
    </li>
    <li class="nav-item ms-3">
        <a class="btn btn-outline-danger btn-sm mt-1" href="/">Sair</a>
    </li>
@endsection

@section('content')
<div class="row pt-5 justify-content-center">
    <div class="col-lg-8 mb-4">
        <h2 class="text-white fw-bold">Meu Perfil</h2>
        <p style="color: #94a3b8;">Gerencie as informações da sua conta e credenciais de acesso.</p>
    </div>

    <div class="col-lg-8">
        <!-- Conta Information -->
        <div class="card briefing-card border-top border-secondary border-4 mb-4">
            <div class="card-body p-4">
                <h5 class="text-white mb-4"><i class="bi bi-person-vcard text-secondary"></i> Dados da Conta</h5>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-secondary">Nome Completo</div>
                    <div class="col-md-8 text-white fw-medium">{{ $user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-secondary">E-mail Cadastrado</div>
                    <div class="col-md-8 text-white fw-medium">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-secondary">Documento</div>
                    <div class="col-md-8 text-white fw-medium">{{ $user->document ?? 'Não informado' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-secondary">Empresa</div>
                    <div class="col-md-8 text-white fw-medium">{{ $client->company_name ?? 'Não informada' }}</div>
                </div>
            </div>
            <div class="card-footer border-secondary mt-0 py-3" style="background: rgba(255,255,255,0.02)">
                <small class="text-secondary"><i class="bi bi-info-circle"></i> Para alterar dados de faturamento converse com nossa equipe.</small>
            </div>
        </div>

        <!-- Mudar Senha -->
        <div class="card briefing-card border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <div class="card-body p-4 p-md-5">
                <h5 class="text-white mb-4"><i class="bi bi-shield-lock" style="color: #D4AF37;"></i> Redefinição de Senha</h5>
                <p style="color: #94a3b8; font-size: 0.95rem;">Se preferir, você pode configurar uma senha de acesso fixa. Dessa forma, você pode fazer login usando seu E-mail ou Documento acompanhado desta senha como alternativa ao "Código Mágico".</p>
                
                <form action="/cliente/perfil/senha" method="POST" class="mt-4">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #94a3b8;">Nova Senha</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password" minlength="4" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: #94a3b8;">Confirmar Nova Senha</label>
                            <input type="password" name="password_confirm" class="form-control" autocomplete="new-password" minlength="4" required>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-gold px-4 fw-semibold"><i class="bi bi-save"></i> Salvar Nova Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection
