@extends('layouts.admin')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <a href="/admin/tickets" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar para Fila</a>
        <h2 class="fw-bold mb-1" style="color: #09101f;">Ticket #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <p class="text-muted mb-0">{{ $ticket->subject }}</p>
    </div>
</div>

<div class="row">
    <!-- Coluna de Chat -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="chat-thread" style="max-height: 60vh; overflow-y: auto; padding-right: 15px;">
                    @foreach($ticket->messages as $msg)
                        <div class="message-bubble mb-5 {{ $msg->is_internal ? 'opacity-75' : '' }}">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px; background-color: {{ $msg->sender->role?->value === 'admin' ? '#D4AF37' : '#09101f' }};">
                                    <i class="bi {{ $msg->sender->role?->value === 'admin' ? 'bi-shield-check' : 'bi-person' }} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0" style="color: #09101f; font-weight: 600;">
                                        {{ $msg->sender->name }} 
                                        @if($msg->sender->role?->value === 'admin') <span class="badge bg-light text-dark ms-1">Agência</span> @endif
                                        @if($msg->is_internal) <span class="badge bg-danger ms-1"><i class="bi bi-eye-slash"></i> Nota Interna Secreta</span> @endif
                                    </h6>
                                    <small class="text-muted">{{ date('d/m/Y H:i', strtotime($msg->created_at)) }}</small>
                                </div>
                            </div>
                            <div class="message-content ps-5 ms-3">
                                <div class="p-3 rounded-3" style="background-color: {{ $msg->sender->role?->value === 'admin' ? '#f8fafc' : '#f1f5f9' }}; border: 1px solid {{ $msg->is_internal ? '#fca5a5' : '#e2e8f0' }}; color: #334155; font-size: 15px; line-height: 1.6;">
                                    {!! nl2br(htmlspecialchars($msg->message)) !!}
                                </div>
                                
                                @if(count($msg->attachments) > 0)
                                <div class="attachments mt-2 d-flex flex-wrap gap-2">
                                    @foreach($msg->attachments as $att)
                                        <div class="bg-light border px-3 py-2 rounded-2 d-flex align-items-center">
                                            <i class="bi bi-paperclip text-muted me-2"></i>
                                            <span class="text-dark small fw-semibold me-3">{{ $att->file_name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($ticket->status?->value !== 'closed')
        <!-- Responder -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="mb-0"><i class="bi bi-reply text-gold me-2"></i> Adicionar Resposta</h5>
            </div>
            <div class="card-body p-4">
                <form action="/admin/tickets/{{ $ticket->id }}/reply" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="4" required placeholder="Digite a resposta do suporte..."></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <input type="file" name="attachments[]" class="form-control d-none" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar" id="fileUpload" onchange="updateFileLabel()">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="document.getElementById('fileUpload').click()">
                                <i class="bi bi-paperclip"></i> Anexar
                            </button>
                            <span id="fileLabel" class="text-muted small">Sem anexos</span>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch me-4">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_internal" value="1" id="isInternalCheckbox">
                                <label class="form-check-label text-muted small" for="isInternalCheckbox">Nota Interna (Oculto pro cliente)</label>
                            </div>
                            <button type="submit" class="btn btn-dark px-4 fw-semibold"><i class="bi bi-send me-2" style="color: #D4AF37;"></i> Enviar Resposta</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Coluna Lateral Info -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark text-white rounded-top-4 py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle text-gold me-2"></i> Detalhes e Status</h6>
            </div>
            <div class="card-body p-4">
                <form action="/admin/tickets/{{ $ticket->id }}/status" method="POST">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">STATUS DO TICKET</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="open" {{ $ticket->status?->value === 'open' ? 'selected' : '' }}>Aberto (Nova)</option>
                            <option value="waiting_client" {{ $ticket->status?->value === 'waiting_client' ? 'selected' : '' }}>Aguardando Cliente</option>
                            <option value="answered" {{ $ticket->status?->value === 'answered' ? 'selected' : '' }}>Respondido</option>
                            <option value="closed" {{ $ticket->status?->value === 'closed' ? 'selected' : '' }}>Encerrado (Resolvido)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">PRIORIDADE</label>
                        <select name="priority" class="form-select">
                            <option value="low" {{ $ticket->priority?->value === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="normal" {{ $ticket->priority?->value === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ $ticket->priority?->value === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ $ticket->priority?->value === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-dark w-100">Atualizar Status</button>
                </form>

                <hr class="my-4">
                
                <h6 class="text-muted small fw-bold mb-3">SOBRE O CLIENTE</h6>
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-person text-muted me-2"></i>
                    <span style="color: #09101f; font-weight:500;">{{ $ticket->client->user->name }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-building text-muted me-2"></i>
                    <span class="text-muted">{{ $ticket->client->company_name ?? 'Não informada' }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-envelope text-muted me-2"></i>
                    <span class="text-muted">{{ $ticket->client->user->email }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileLabel() {
        const input = document.getElementById('fileUpload');
        const label = document.getElementById('fileLabel');
        if (input.files && input.files.length > 0) {
            label.textContent = input.files.length + " arquivo(s)";
            label.classList.remove('text-muted');
            label.classList.add('text-primary');
        } else {
            label.textContent = "Sem anexos";
            label.classList.add('text-muted');
            label.classList.remove('text-primary');
        }
    }
    // Auto-scroll chat to bottom
    window.onload = function() {
        var chatThread = document.querySelector('.chat-thread');
        if (chatThread) chatThread.scrollTop = chatThread.scrollHeight;
    }
</script>
@endsection
