@extends('layouts.admin')

@section('title', 'Fila de E-mails / Logs')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0"><i class="bi bi-envelope-paper me-2" style="color: #D4AF37;"></i> Fila de Envios (Jobs)</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Monitore os envios de emails do sistema e reenvie solicitações falhas</p>
    </div>
    <div>
        <a href="/admin/settings/email" class="btn btn-outline-light shadow-sm px-4">
            <i class="bi bi-gear me-2"></i> Configurar SMTP
        </a>
    </div>
</header>

<div class="card bg-dark border-0 shadow-sm mt-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="border-bottom: 2px solid #1a253c;">
                        <th class="py-3 px-4 text-muted">#ID</th>
                        <th class="py-3 text-muted">Destinatário</th>
                        <th class="py-3 text-muted">Assunto</th>
                        <th class="py-3 text-muted">Status</th>
                        <th class="py-3 text-muted">Data/Tentativas</th>
                        <th class="py-3 px-4 text-end text-muted">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr style="border-bottom: 1px solid #1a253c;">
                        <td class="py-3 px-4">#{{ $job->id }}</td>
                        <td class="py-3">
                            <span class="d-block text-white fw-medium">{{ $job->recipient_name }}</span>
                            <small class="text-secondary">{{ $job->recipient_email }}</small>
                        </td>
                        <td class="py-3 text-light">{{ $job->subject }}</td>
                        <td class="py-3">
                            @if($job->status?->value === 'sent')
                                <span class="badge bg-success bg-opacity-25 text-success px-2 py-1">Enviado</span>
                            @elseif($job->status?->value === 'failed')
                                <span class="badge bg-danger bg-opacity-25 text-danger px-2 py-1" title="{{ $job->error_message }}">Falhou</span>
                            @else
                                <span class="badge bg-warning bg-opacity-25 text-warning px-2 py-1">Na Fila</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <small class="text-secondary d-block">Criado: {{ date('d/m H:i', strtotime($job->created_at)) }}</small>
                            <small class="text-secondary">Tentativas: {{ $job->attempts }}</small>
                        </td>
                        <td class="py-3 px-4 text-end">
                            @if($job->status?->value === 'failed')
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#errorModal{{ $job->id }}" title="Ver detalhe do Erro">
                                    <i class="bi bi-bug"></i> Ver Erro
                                </button>
                                <form action="/admin/queue/{{ $job->id }}/retry" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-clockwise"></i> Reenviar</button>
                                </form>
                            </div>

                            <!-- Modal de Erro -->
                            <div class="modal fade" id="errorModal{{ $job->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content bg-dark text-white" style="border: 1px solid #1a253c;">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Log de Falha - Job #{{ $job->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted small mb-2">Destinatário: {{ $job->recipient_email }}</p>
                                            <div class="p-3 rounded" style="background-color: #09101f; max-height: 300px; overflow-y: auto;">
                                                <code class="text-danger" style="white-space: pre-wrap; word-break: break-all;">{{ $job->error_message ?: 'Nenhuma mensagem de erro registrada.' }}</code>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <button class="btn btn-sm btn-secondary disabled" title="Nenhuma ação disponível"><i class="bi bi-check2"></i></button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            A fila de e-mails está vazia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
