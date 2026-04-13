@extends('layouts.admin')

@section('title', 'Novo Cliente - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/clients" class="text-decoration-none" style="color: #94a3b8;"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mt-2 mb-0">Novo Cliente</h2>
    </div>
</header>

<div class="row">
    <div class="col-lg-8">
        <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <form action="/admin/clients/store" method="POST">
                <h5 class="text-white mb-4"><i class="bi bi-building"></i> Dados da Empresa</h5>
                
                <div class="row g-3">
                    <div class="col-md-12 mb-3">
                        <label class="form-label ps-1">Nome da Empresa / Razão Social</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                </div>

                <hr class="my-4" style="border-color: rgba(255,255,255,0.05);">
                
                <h5 class="text-white mb-4"><i class="bi bi-person-badge"></i> Contato Principal (Usuário do Sistema)</h5>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">Nome Completo</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">E-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">Telefone / WhatsApp</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">CPF ou CNPJ</label>
                        <input type="text" name="document" class="form-control">
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-end gap-3">
                    <a href="/admin/clients" class="btn btn-outline-light">Cancelar</a>
                    <button type="submit" class="btn btn-gold px-5 fw-semibold">Cadastrar Cliente</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card briefing-card p-4 bg-transparent border-0 shadow-none">
            <h5 class="text-white mb-3">Informação</h5>
            <p style="color: #94a3b8;">Ao criar um cliente, será criado também uma conta de usuário vinculada ao e-mail informado.</p>
            <p style="color: #94a3b8;">Senhas não são necessárias por padrão. Você poderá gerar um <strong>Link Mágico (Magic Link)</strong> após o cadastro e enviar para o cliente acessar seu portal com um clique.</p>
        </div>
    </div>
</div>
@endsection
