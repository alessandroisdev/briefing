@extends('layouts.admin')

@section('admin_content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <a href="/admin/tickets" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar para Fila</a>
        <h2 class="fw-bold text-white mb-1">Ticket #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <p class="text-muted mb-0">{{ $ticket->subject }}</p>
    </div>
</div>

<div class="row">
    <!-- Coluna de Chat -->
    <div class="col-md-8">
        <div class="card briefing-card bg-dark border-secondary shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="chat-thread" style="max-height: 60vh; overflow-y: auto; padding-right: 15px;">
                    @foreach($ticket->messages as $msg)
                        <div class="message-bubble mb-5 {{ $msg->is_internal ? 'opacity-75' : '' }}">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px; background-color: {{ $msg->sender->role?->value === 'admin' ? '#D4AF37' : '#1e293b' }}; border: 1px solid #334155;">
                                    <i class="bi {{ $msg->sender->role?->value === 'admin' ? 'bi-shield-check text-dark' : 'bi-person' }} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white" style="font-weight: 600;">
                                        {{ $msg->sender->name }} 
                                        @if($msg->sender->role?->value === 'admin') <span class="badge bg-gold text-dark ms-1">Agência</span> @endif
                                        @if($msg->is_internal) <span class="badge bg-danger ms-1"><i class="bi bi-eye-slash"></i> Nota Interna Secreta</span> @endif
                                    </h6>
                                    <small class="text-muted">{{ date('d/m/Y H:i', strtotime($msg->created_at)) }}</small>
                                </div>
                            </div>
                            <div class="message-content ps-5 ms-3">
                                <div class="p-3 rounded-3" style="background-color: {{ $msg->sender->role?->value === 'admin' ? 'rgba(212, 175, 55, 0.05)' : '#0f172a' }}; border: 1px solid {{ $msg->is_internal ? '#fca5a5' : ($msg->sender->role?->value === 'admin' ? 'rgba(212, 175, 55, 0.2)' : '#1e293b') }}; color: #cbd5e1; font-size: 15px; line-height: 1.6;">
                                    {!! nl2br(htmlspecialchars($msg->message)) !!}
                                </div>
                                
                                @if(count($msg->attachments) > 0)
                                <div class="attachments mt-2 d-flex flex-wrap gap-2">
                                    @foreach($msg->attachments as $att)
                                        @php
                                            $attUrl = "/admin/tickets/anexo/" . $att->id;
                                            $ext = strtolower(pathinfo($att->file_name, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        <a href="javascript:void(0)" onclick="openAttachmentModal('{{ $attUrl }}', {{ $isImage ? 'true' : 'false' }}, '{{ htmlspecialchars($att->file_name, ENT_QUOTES) }}')" class="text-decoration-none bg-dark border border-secondary px-3 py-2 rounded-2 d-flex align-items-center" style="transition:all 0.2s;">
                                            <i class="bi {{ $isImage ? 'bi-image' : 'bi-paperclip' }} text-gold me-2"></i>
                                            <span class="text-white small fw-semibold">{{ $att->file_name }}</span>
                                        </a>
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
        <div class="card briefing-card bg-dark border-secondary shadow-sm mb-4">
            <div class="card-header bg-transparent border-secondary pt-4 pb-0">
                <h5 class="mb-0 text-white"><i class="bi bi-reply text-gold me-2"></i> Adicionar Resposta</h5>
            </div>
            <div class="card-body p-4">
                <form action="/admin/tickets/{{ $ticket->id }}/reply" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea name="message" class="form-control bg-dark text-white border-secondary" rows="4" required placeholder="Digite a resposta do suporte..."></textarea>
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
                            <button type="submit" class="btn btn-gold px-4 fw-semibold"><i class="bi bi-send me-2 text-dark"></i> <span class="text-dark">Enviar Resposta</span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Coluna Lateral Info -->
    <div class="col-md-4">
        <div class="card briefing-card bg-dark border-secondary shadow-sm mb-4">
            <div class="card-header bg-transparent border-secondary py-3">
                <h6 class="mb-0 text-white"><i class="bi bi-info-circle text-gold me-2"></i> Detalhes e Status</h6>
            </div>
            <div class="card-body p-4">
                <form action="/admin/tickets/{{ $ticket->id }}/status" method="POST">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">STATUS DO TICKET</label>
                        <select name="status" class="form-select border-secondary bg-dark text-white form-select-lg">
                            <option value="open" {{ $ticket->status?->value === 'open' ? 'selected' : '' }}>Aberto (Nova)</option>
                            <option value="waiting_client" {{ $ticket->status?->value === 'waiting_client' ? 'selected' : '' }}>Aguardando Cliente</option>
                            <option value="answered" {{ $ticket->status?->value === 'answered' ? 'selected' : '' }}>Respondido</option>
                            <option value="closed" {{ $ticket->status?->value === 'closed' ? 'selected' : '' }}>Encerrado (Resolvido)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">PRIORIDADE</label>
                        <select name="priority" class="form-select border-secondary bg-dark text-white">
                            <option value="low" {{ $ticket->priority?->value === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="normal" {{ $ticket->priority?->value === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ $ticket->priority?->value === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ $ticket->priority?->value === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-gold w-100">Atualizar Status</button>
                </form>

                <hr class="my-4 border-secondary">
                
                <h6 class="text-muted small fw-bold mb-3">SOBRE O CLIENTE</h6>
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-person text-gold me-2"></i>
                    <span class="text-white" style="font-weight:500;">{{ $ticket->client->user->name }}</span>
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

<!-- Attachment Modal -->
<div class="modal fade" id="attachmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="attachmentModalLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0 bg-black" style="min-height: 250px; display:flex; align-items:center; justify-content:center;">
                <img id="attachmentImage" src="" style="max-width: 100%; max-height: 70vh; display: none; object-fit: contain;">
                <div id="attachmentGeneric" class="p-5 w-100" style="display:none;">
                    <i class="bi bi-file-earmark-arrow-down fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-white mb-2">Este arquivo não é uma imagem nativa</h5>
                    <p class="text-muted small mb-4">Clique no botão abaixo para baixar ou abrir o documento com segurança.</p>
                    <a id="attachmentDownloadBtn" href="" target="_blank" class="btn btn-gold px-4 fw-bold text-dark">Abrir / Baixar Arquivo</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openAttachmentModal(url, isImage, filename) {
        document.getElementById('attachmentModalLabel').innerText = filename;
        const img = document.getElementById('attachmentImage');
        const generic = document.getElementById('attachmentGeneric');
        const downloadBtn = document.getElementById('attachmentDownloadBtn');

        if (isImage) {
            img.src = url;
            img.style.display = 'block';
            generic.style.display = 'none';
        } else {
            img.style.display = 'none';
            generic.style.display = 'block';
            downloadBtn.href = url;
            // Also append a target blank to force download / external view
        }

        if(window.bootstrap) {
            const modal = new bootstrap.Modal(document.getElementById('attachmentModal'));
            modal.show();
        }
    }
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
