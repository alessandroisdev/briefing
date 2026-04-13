@extends('layouts.admin')

@section('title', 'Analisar Projeto - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/briefings" class="text-decoration-none" style="color: #94a3b8;"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mt-2 mb-0">Projeto: {{ $briefing->title }}</h2>
        <p class="text-muted mb-0">Cliente: {{ $briefing->client->company_name ?? 'N/A' }} | Criado em {{ $briefing->created_at->format('d/m/Y') }}</p>
    </div>
    <div>
        <form action="/admin/briefings/{{ $briefing->id }}/status" method="POST" class="d-flex gap-2">
            <select name="status" class="form-select bg-dark text-white border-secondary">
                <option value="criado" {{ $briefing->status === 'criado' ? 'selected' : '' }}>Aguardando Preenchimento</option>
                <option value="editando" {{ $briefing->status === 'editando' ? 'selected' : '' }}>Em Edição (Cliente)</option>
                <option value="executando" {{ $briefing->status === 'executando' ? 'selected' : '' }}>Em Execução (Agência)</option>
                <option value="finalizado" {{ $briefing->status === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                <option value="cancelado" {{ $briefing->status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
            <button type="submit" class="btn btn-gold">Atualizar</button>
        </form>
    </div>
</header>

<div class="row">
    <div class="col-lg-8">
        <div class="card briefing-card p-4 p-md-5">
            <h4 class="text-white border-bottom border-secondary pb-3 mb-4">Respostas do Formulário</h4>
            
            @if(empty($briefing->form_data))
                <div class="alert alert-dark text-center py-5">
                    <i class="bi bi-clock-history fs-1 text-muted mb-3 d-block"></i>
                    <p class="mb-0 text-muted">O cliente ainda não enviou as respostas para este briefing.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-4">
                    @foreach($briefing->form_data as $question => $answer)
                    <div class="card bg-dark border-secondary">
                        <div class="card-header bg-transparent border-secondary text-gold fw-bold">
                            {{ $question }}
                        </div>
                        <div class="card-body text-white">
                            {!! nl2br(htmlspecialchars($answer)) !!}
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card briefing-card p-4">
            <h5 class="text-white mb-3">Detalhes Base</h5>
            <ul class="list-group list-group-flush bg-transparent">
                <li class="list-group-item bg-transparent text-white px-0 border-secondary">
                    <small class="d-block text-muted">Modelo Usado</small>
                    {{ $briefing->template->title ?? 'Modelo Excluído' }}
                </li>
                <li class="list-group-item bg-transparent text-white px-0 border-secondary">
                    <small class="d-block text-muted">Contato</small>
                    {{ $briefing->client->user->name ?? '' }}<br>
                    <a href="mailto:{{ $briefing->client->user->email ?? '' }}" class="text-gold text-decoration-none">{{ $briefing->client->user->email ?? '' }}</a>
                </li>
                <li class="list-group-item bg-transparent text-white px-0 border-secondary">
                    <small class="d-block text-muted">Última Atualização</small>
                    {{ $briefing->updated_at->format('d/m/Y H:i') }}
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
