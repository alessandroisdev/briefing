@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark border-secondary shadow-lg mt-4">
            <div class="card-header border-secondary bg-transparent py-3">
                <h4 class="mb-0 text-white"><i class="bi bi-headset me-2" style="color: #D4AF37;"></i> Abrir Novo Chamado de Suporte</h4>
            </div>
            <div class="card-body p-4">
                <form action="/cliente/suporte/store" method="POST" enctype="multipart/form-data" id="ticketForm">
                    <div class="mb-4">
                        <label class="form-label text-white">Assunto do Chamado</label>
                        <input type="text" name="subject" class="form-control form-control-lg bg-dark text-white border-secondary" required placeholder="Resumo do problema ou dúvida...">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-white">Prioridade</label>
                        <select name="priority" class="form-select bg-dark text-white border-secondary">
                            <option value="low">Baixa (Dúvida comum)</option>
                            <option value="normal" selected>Normal (Problema não bloqueante)</option>
                            <option value="high">Alta (Problema bloqueante)</option>
                            <option value="urgent">Urgente (Sistema Fora do Ar!)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white">Mensagem Detalhada</label>
                        <textarea name="message" class="form-control bg-dark text-white border-secondary" rows="6" required placeholder="Explique com detalhes o que está acontecendo..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white">Anexar Arquivos (Opcional)</label>
                        <input type="file" name="attachments[]" class="form-control bg-dark text-white border-secondary" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar">
                        <div class="form-text text-muted">Você pode selecionar múltiplos arquivos simultaneamente. Máximo 5MB por arquivo.</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <a href="/cliente/suporte" class="btn btn-outline-light">Cancelar</a>
                        <button type="submit" class="btn btn-gold px-5 fw-semibold"><i class="bi bi-send me-2"></i> Enviar Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
