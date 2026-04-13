@extends('layouts.admin')

@section('title', 'Nova Resposta Rápida - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/templates/messages" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mb-0">Criar Resposta Rápida</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Adicione templates de apoio para a equipe utilizar nos chats dos briefings.</p>
    </div>
</header>

@if(isset($_SESSION['error']))
    <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $_SESSION['error'] }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php unset($_SESSION['error']); @endphp
@endif

<div class="card briefing-card">
    <div class="card-body p-4">
        <form action="/admin/templates/messages/store" method="POST">
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label text-gold">Título Curto de Identificação</label>
                    <input type="text" name="title" class="form-control bg-dark text-white border-secondary" placeholder="Ex: Aviso Hospedagem, Solicitar Servidor, Boas-Vindas" required>
                    <small class="text-muted mt-1 d-block">Esse nome é visto apenas pela Agência na hora de escolher a resposta.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-gold">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                        <label class="form-check-label text-white" for="isActive">Habilitado</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label text-gold">Texto a ser inserido no Chat</label>
                    <textarea name="content" class="form-control bg-dark text-white border-secondary" rows="6" placeholder="Você pode usar HTML base (<b>, <br>)..." required></textarea>
                </div>

                <div class="col-12 text-end pt-3 border-top border-secondary mt-5">
                    <a href="/admin/templates/messages" class="btn btn-outline-light me-2">Cancelar</a>
                    <button type="submit" class="btn btn-gold px-5"><i class="bi bi-save me-2"></i> Criar Resposta</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
