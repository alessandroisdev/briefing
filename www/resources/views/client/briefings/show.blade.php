@extends('layouts.app')

@section('title', 'Responder Briefing - BriefingApp')

@section('nav_links')
    <li class="nav-item">
        <a class="nav-link" href="/cliente/dashboard"><i class="bi bi-arrow-left"></i> Voltar</a>
    </li>
@endsection

@section('content')
<div class="row pt-5 justify-content-center">
    <div class="col-lg-8 mb-4">
        <h2 class="text-white fw-bold mb-1">{{ $briefing->title }}</h2>
        <span class="badge bg-primary mb-3">{{ $briefing->template->title ?? 'Modelo Personalizado' }}</span>
        
        @if(!empty($briefing->template->description))
        <div class="card bg-dark border-0 shadow-sm p-4 text-white mb-4" style="background-color: #0d1527 !important;">
            {!! $briefing->template->description !!}
        </div>
        @endif

        <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <h4 class="text-white mb-4">Formulário</h4>
            
            <form action="/cliente/briefings/{{ $briefing->id }}/save" method="POST">
                
                @if(isset($briefing->template->form_schema) && is_array($briefing->template->form_schema))
                    @foreach($briefing->template->form_schema as $field)
                        <div class="mb-4">
                            <label class="form-label text-white fw-bold">{{ $field['label'] }}</label>
                            
                            @php
                                $phpKeyFallback = str_replace([' ', '.'], '_', $field['label']);
                                $answer = $briefing->form_data[$field['label']] ?? ($briefing->form_data[$phpKeyFallback] ?? '');
                                $md5Key = md5($field['label']);
                            @endphp

                            @if($field['type'] === 'textarea')
                                <textarea name="answers[{{ $md5Key }}]" class="form-control" rows="4" placeholder="Sua resposta elaborada...">{{ $answer }}</textarea>
                            @elseif($field['type'] === 'file')
                                <input type="file" name="file_upload_mock" class="form-control text-muted">
                                <small class="text-muted">Upload de arquivo no MVP está desativado.</small>
                            @else
                                <input type="text" name="answers[{{ $md5Key }}]" class="form-control" value="{{ $answer }}" placeholder="Sua resposta...">
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-secondary">Nenhum campo definido para este escopo.</div>
                @endif

                <div class="mt-5 pt-3 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                    <button type="submit" class="btn btn-gold btn-lg px-5 w-100 fw-semibold">
                        <i class="bi bi-save"></i> Salvar Minhas Respostas
                    </button>
                    <p class="text-muted text-center mt-3 small">Você poderá alterar suas respostas até o projeto entrar em "Execução".</p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    /* Estilizar o conteúdo gerado pelo Quill (Description) no front-end para tema escuro */
    .card.bg-dark {
        font-family: 'Outfit', sans-serif;
    }
    .card.bg-dark h1, .card.bg-dark h2, .card.bg-dark h3 {
        color: #D4AF37 !important;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .card.bg-dark p {
        color: #cbd5e1;
        line-height: 1.6;
    }
    .card.bg-dark a {
        color: #D4AF37;
    }
</style>
@endsection
