@extends('layouts.app')

@section('title', 'Admin Login - BriefingApp')

@section('content')
<div class="row min-vh-100 justify-content-center align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <div class="text-center mb-5">
                <h3 class="fw-bold text-white mb-1"><span style="color: #D4AF37;">Admin</span> Portal</h3>
                <span class="badge" style="background-color: rgba(212, 175, 55, 0.2); color: #D4AF37; font-weight: 500; padding: 0.5rem 1rem;">Acesso Restrito</span>
            </div>
            <form action="/admin/login" method="POST">
                <div class="mb-4">
                    <label class="form-label ps-1">Email Corporativo</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="admin@agencia.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label ps-1 d-flex justify-content-between">
                        <span>Senha de Acesso</span>
                    </label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                </div>
                <div class="d-grid mt-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">Entrar no Sistema</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
