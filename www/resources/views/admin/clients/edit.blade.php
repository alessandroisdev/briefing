@extends('layouts.admin')

@section('title', 'Editar Cliente - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/clients" class="text-decoration-none" style="color: #94a3b8;"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mt-2 mb-0">Editar Cliente</h2>
    </div>
    <div class="d-flex gap-2">
        <form action="/admin/clients/{{ $client->id }}/generate-magic-link" method="POST">
            <button type="submit" class="btn btn-outline-info shadow-sm">
                <i class="bi bi-link-45deg"></i> Gerar Magic Link
            </button>
        </form>
    </div>
</header>

<div class="row">
    <div class="col-lg-8">
        @if(isset($_GET['magic_link']))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert" style="background-color: rgba(25, 135, 84, 0.1); border-color: #198754; color: #fff;">
            <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
            <div>
                <strong>Magic Link Gerado com Sucesso!</strong><br>
                <span>Envie o link abaixo para o cliente acessar sem senha:</span><br>
                <div class="input-group mt-2">
                    <input type="text" class="form-control form-control-sm" value="{{ $_ENV['APP_URL'] }}/cliente/magic-login?token={{ $_GET['magic_link'] }}" readonly>
                    <button class="btn btn-outline-light btn-sm" onclick="navigator.clipboard.writeText('{{ $_ENV['APP_URL'] }}/cliente/magic-login?token={{ $_GET['magic_link'] }}')"><i class="bi bi-clipboard"></i> Copiar</button>
                </div>
            </div>
        </div>
        @endif

        <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <form action="/admin/clients/{{ $client->id }}/update" method="POST">
                
                <h5 class="text-white mb-4"><i class="bi bi-building"></i> Dados da Empresa</h5>
                
                <div class="row g-3">
                    <div class="col-md-8 mb-3">
                        <label class="form-label ps-1">Nome da Empresa / Razão Social</label>
                        <input type="text" name="company_name" class="form-control" value="{{ $client->company_name }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label ps-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ $client->status == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ $client->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4" style="border-color: rgba(255,255,255,0.05);">
                
                <h5 class="text-white mb-4"><i class="bi bi-person-badge"></i> Contato Principal (Usuário do Sistema)</h5>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">Nome Completo</label>
                        <input type="text" name="name" class="form-control" value="{{ $client->user->name ?? '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">E-mail</label>
                        <input type="email" name="email" class="form-control" value="{{ $client->user->email ?? '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">Telefone / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" value="{{ $client->user->phone ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">CPF ou CNPJ</label>
                        <input type="text" name="document" class="form-control" value="{{ $client->user->document ?? '' }}">
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-end gap-3">
                    <button type="submit" class="btn btn-gold px-5 fw-semibold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
