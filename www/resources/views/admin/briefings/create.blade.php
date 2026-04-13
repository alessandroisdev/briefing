@extends('layouts.admin')

@section('title', 'Iniciar Projeto - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/briefings" class="text-decoration-none" style="color: #94a3b8;"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mt-2 mb-0">Iniciar Projeto / Briefing</h2>
    </div>
</header>

<div class="row">
    <div class="col-lg-8">
        <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <form action="/admin/briefings/store" method="POST">
                
                <h5 class="text-white mb-4"><i class="bi bi-link"></i> Associações</h5>
                
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">Selecione o Cliente</label>
                        <select name="client_id" class="form-control form-control-lg text-white" style="background:#09101f;" required>
                            <option value="">-- Escolha --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name ?? 'N/A' }} ({{ $client->user->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label ps-1">Modelo de Briefing</label>
                        <select name="template_id" class="form-control form-control-lg text-white" style="background:#09101f;" required>
                            <option value="">-- Escolha um Formulário --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-4" style="border-color: rgba(255,255,255,0.05);">
                
                <h5 class="text-white mb-4"><i class="bi bi-pencil-square"></i> Identificação Única</h5>

                <div class="row g-3">
                    <div class="col-md-12 mb-3">
                        <label class="form-label ps-1">Título do Projeto (Opcional)</label>
                        <input type="text" name="title" class="form-control" placeholder="Deixe em branco para usar o nome do Modelo selecionado">
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-end gap-3">
                    <a href="/admin/briefings" class="btn btn-outline-light">Cancelar</a>
                    <button type="submit" class="btn btn-gold px-5 fw-semibold"><i class="bi bi-send-fill"></i> Iniciar e Disponibilizar ao Cliente</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card briefing-card p-4 bg-transparent border-0 shadow-none">
            <h5 class="text-white mb-3">Como funciona?</h5>
            <p style="color: #94a3b8;">Ao salvar, um novo escopo será aberto imediatamente no portal do respectivo cliente.</p>
            <p style="color: #94a3b8;">O cliente verá o aviso de pendência e poderá acessá-lo para preencher e responder às perguntas formuladas pelo Modelo que você selecionou.</p>
        </div>
    </div>
</div>
@endsection
