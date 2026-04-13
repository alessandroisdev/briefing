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

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 gap-2" id="briefingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active bg-dark border border-secondary text-white shadow-sm" id="tab-form-btn" data-bs-toggle="pill" data-bs-target="#tab-form" type="button" role="tab"><i class="bi bi-file-text me-2"></i>Nosso Escopo</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-dark border border-secondary text-white shadow-sm" id="tab-messages-btn" data-bs-toggle="pill" data-bs-target="#tab-messages" type="button" role="tab"><i class="bi bi-chat-left-text me-2"></i>Workspace Chat</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-dark border border-secondary text-white shadow-sm" id="tab-vault-btn" data-bs-toggle="pill" data-bs-target="#tab-vault" type="button" role="tab"><i class="bi bi-safe me-2"></i>Cofre de Acessos</button>
            </li>
        </ul>

        <div class="tab-content" id="briefingTabsContent">
            
            <!-- TAB: ESCOPO -->
            <div class="tab-pane fade show active" id="tab-form" role="tabpanel">
                <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
                    <h4 class="text-white mb-4">Formulário Inicial</h4>
                    
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

            <!-- TAB: MENSAGENS -->
            <div class="tab-pane fade" id="tab-messages" role="tabpanel">
                <div class="card briefing-card p-4 border-top border-info border-4">
                    <h4 class="text-white border-bottom border-secondary pb-3 mb-4">Chat do Projeto</h4>
                    
                    <div class="chat-thread mb-4" style="max-height: 50vh; overflow-y: auto; padding-right: 15px;">
                        @forelse($briefing->messages as $msg)
                            @if(!$msg->is_internal)
                            <div class="message-bubble mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 {{ $msg->sender->role === 'cliente' ? 'text-info' : 'text-gold' }} me-2">
                                        {{ $msg->sender->name }}
                                        @if($msg->sender->role !== 'cliente')
                                            <span class="badge bg-gold text-dark ms-1" style="font-size:0.6rem;">Agência</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ date('d/m/Y H:i', strtotime($msg->created_at)) }}</small>
                                </div>
                                <div class="p-3 rounded-3" style="background-color: {{ $msg->sender->role === 'cliente' ? '#0f172a' : '#1e1b4b' }}; border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0;">
                                    {!! nl2br(htmlspecialchars($msg->message)) !!}
                                </div>
                            </div>
                            @endif
                        @empty
                            <p class="text-muted text-center py-4">Nenhuma mensagem trocada ainda neste projeto.</p>
                        @endforelse
                    </div>

                    <form action="/cliente/briefings/{{ $briefing->id }}/message" method="POST" class="mt-3 pt-3 border-top border-secondary">
                        <div class="mb-3">
                            <label class="form-label text-white">Adicionar um comentário / dúvida</label>
                            <textarea name="message" class="form-control" rows="3" required placeholder="Digite sua mensagem para nossa equipe..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-info px-4 text-dark fw-bold"><i class="bi bi-send me-2"></i>Enviar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB: COFRE (VAULT) -->
            <div class="tab-pane fade" id="tab-vault" role="tabpanel">
                <div class="card briefing-card p-4 border-top border-success border-4">
                    <div class="d-flex justify-content-between align-items-start border-bottom border-secondary pb-3 mb-4">
                        <div>
                            <h4 class="text-white mb-1">Cofre de Credenciais</h4>
                            <p class="text-muted small mb-0">Insira aqui logins, ftp, cpanel ou urls para nossos desenvolvedores acessarem.</p>
                        </div>
                    </div>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Ambiente</th>
                                    <th>Serviço/Plataforma</th>
                                    <th>URL</th>
                                    <th>Usuário</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($briefing->credentials as $cred)
                                <tr>
                                    <td>
                                        @if($cred->environment === 'dev') <span class="badge bg-secondary">Dev</span>
                                        @elseif($cred->environment === 'homologation') <span class="badge bg-info text-dark">Homol</span>
                                        @else <span class="badge bg-success">Prod</span> @endif
                                    </td>
                                    <td class="text-white">{{ $cred->service_name }}</td>
                                    <td><a href="{{ $cred->url }}" target="_blank" class="text-info">{{ $cred->url }}</a></td>
                                    <td class="text-muted"><code class="text-white bg-dark px-2 py-1 rounded">{{ $cred->username }}</code></td>
                                </tr>
                                @if($cred->notes)
                                <tr>
                                    <td colspan="4" class="border-top-0 pt-0 text-muted small pb-3">Notas: {{ $cred->notes }}</td>
                                </tr>
                                @endif
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Você ainda não enviou nenhum acesso.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h6 class="text-white mt-4 mb-3">Enviar Acesso Seguro</h6>
                    <form action="/cliente/briefings/{{ $briefing->id }}/credential" method="POST" class="row g-3">
                        <div class="col-md-4">
                            <select name="environment" class="form-select" required>
                                <option value="production">Meu Site Final (Produção)</option>
                                <option value="homologation">Plataforma/Hospedagem</option>
                                <option value="dev">API/App de Terceiros</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="service_name" class="form-control" placeholder="Serviço (ex: Cpanel, Cloudflare, Asaas, Shopify)" required>
                        </div>
                        <div class="col-md-4">
                            <input type="url" name="url" class="form-control" placeholder="Link (URL)">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="username" class="form-control" placeholder="Usuário / Login / Email">
                        </div>
                        <div class="col-md-4">
                            <input type="password" name="password" class="form-control" placeholder="Senha / API Key">
                        </div>
                        <div class="col-12">
                            <input type="text" name="notes" class="form-control" placeholder="Notas Adicionais (opcional)">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-lock-fill me-2"></i>Guardar e Enviar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    /* Estilo ativo para pílulas Dark Premium */
    .nav-pills .nav-link {
        transition: all 0.2s ease;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: #D4AF37 !important; /* Gold */
        color: #09101f !important; /* Deep Navy */
        font-weight: bold;
        border-color: #D4AF37 !important;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(window.location.hash) {
            var hash = window.location.hash;
            var tabBtn = document.querySelector('button[data-bs-target="' + hash + '"]');
            if(tabBtn) {
                var tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }
        var tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(function(btn) {
            btn.addEventListener('shown.bs.tab', function (e) {
                var hash = e.target.getAttribute('data-bs-target');
                history.replaceState(null, null, hash);
            });
        });
    });
</script>
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
