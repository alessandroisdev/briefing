@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Cabeçalho do Ticket -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="/cliente/suporte" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar à Lista</a>
                    <h2 class="text-white fw-bold mb-1">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }} - {{ $ticket->subject }}</h2>
                </div>
                <div>
                    @php
                        $statusMaps = [
                            'open' => ['bg' => 'bg-info text-dark', 'label' => 'Aberto (Em Análise)'],
                            'answered' => ['bg' => 'bg-success', 'label' => 'Respondido (Agência)'],
                            'waiting_client' => ['bg' => 'bg-warning text-dark', 'label' => 'Aguardando Sua Resposta'],
                            'closed' => ['bg' => 'bg-secondary', 'label' => 'Encerrado']
                        ];
                        $sInfo = $statusMaps[$ticket->status?->value] ?? ['bg'=>'bg-secondary', 'label'=>'Desconhecido'];
                    @endphp
                    <span class="badge {{ $sInfo['bg'] }} fs-6 px-3 py-2">{{ $sInfo['label'] }}</span>
                </div>
            </div>

            <!-- Thread de Mensagens -->
            <div class="card bg-transparent border-0 mb-4">
                <div class="card-body p-0">
                    <div class="chat-thread" style="max-height: 60vh; overflow-y: auto; padding-right: 15px;">
                        @foreach($ticket->messages as $msg)
                            @if(!$msg->is_internal)
                            <div class="message-bubble mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar bg-dark text-gold rounded-circle d-flex align-items-center justify-content-center me-3 border border-secondary" style="width:40px; height:40px; border-color: {{ $msg->sender->role?->value === 'admin' ? '#D4AF37 !important' : '#475569 !important' }};">
                                        <i class="bi {{ $msg->sender->role?->value === 'admin' ? 'bi-shield-check' : 'bi-person' }} fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 {{ $msg->sender->role?->value === 'admin' ? 'text-gold' : 'text-white' }}">
                                            {{ $msg->sender->name }}
                                            @if($msg->sender->role?->value === 'admin') <span class="badge bg-gold text-dark ms-2" style="font-size:0.6rem;">SUPORTE DA AGÊNCIA</span> @endif
                                        </h6>
                                        <small class="text-muted">{{ date('d/m/Y H:i', strtotime($msg->created_at)) }}</small>
                                    </div>
                                </div>
                                <div class="message-content ps-5 ms-3">
                                    <div class="p-3 rounded-3" style="background-color: {{ $msg->sender->role?->value === 'admin' ? 'rgba(212, 175, 55, 0.05)' : '#0f172a' }}; border: 1px solid {{ $msg->sender->role?->value === 'admin' ? 'rgba(212, 175, 55, 0.2)' : '#1e293b' }}; color:#cbd5e1;">
                                        {!! nl2br(htmlspecialchars($msg->message)) !!}
                                    </div>
                                    
                                    @if(count($msg->attachments) > 0)
                                    <div class="attachments mt-2 d-flex flex-wrap gap-2">
                                        @foreach($msg->attachments as $att)
                                        @php
                                            $attUrl = "/cliente/suporte/anexo/" . $att->id;
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
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Caixa de Resposta -->
            @if($ticket->status?->value !== 'closed')
            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-header border-secondary bg-transparent py-3">
                    <h6 class="mb-0 text-white"><i class="bi bi-reply-fill text-gold me-2"></i> Adicionar Resposta</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/cliente/suporte/{{ $ticket->id }}/reply" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <textarea name="message" class="form-control bg-dark text-white border-secondary" rows="4" required placeholder="Digite sua resposta aqui..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <input type="file" name="attachments[]" class="form-control form-control-sm bg-dark text-white border-secondary" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar" id="fileUpload" style="display:none;" onchange="updateFileLabel()">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('fileUpload').click()">
                                    <i class="bi bi-paperclip"></i> Anexar Arquivos
                                </button>
                                <span id="fileLabel" class="text-muted small ms-2">Nenhum arquivo</span>
                            </div>
                            <button type="submit" class="btn btn-gold px-4 fw-semibold"><i class="bi bi-send me-2"></i> Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-secondary border-0 bg-dark text-center py-4">
                <i class="bi bi-lock-fill fs-3 text-muted mb-2 d-block"></i>
                <h5 class="text-white mb-0">Ticket Encerrado</h5>
                <p class="text-muted mb-0">Este chamado foi marcado como resolvido. Caso necessário, abra um novo chamado informando o número deste ticket.</p>
            </div>
            @endif

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
                    <p class="text-muted small mb-4">Clique no botão abaixo para baixar ou abrir o documento de mídia seguramente.</p>
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
            label.textContent = input.files.length + " arquivo(s) selecionado(s)";
            label.classList.remove('text-muted');
            label.classList.add('text-gold');
        } else {
            label.textContent = "Nenhum arquivo";
            label.classList.add('text-muted');
            label.classList.remove('text-gold');
        }
    }
    // Auto-scroll chat to bottom
    window.onload = function() {
        var chatThread = document.querySelector('.chat-thread');
        if (chatThread) chatThread.scrollTop = chatThread.scrollHeight;
    }
</script>
@endsection
