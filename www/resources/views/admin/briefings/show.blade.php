@extends('layouts.admin')

@section('title', 'Analisar Projeto - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/briefings" class="text-decoration-none" style="color: #94a3b8;"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-dark mt-2 mb-0">Projeto: {{ $briefing->title }}</h2>
        <p class="text-muted mb-0">Cliente: {{ $briefing->client->company_name ?? 'N/A' }} | Criado em {{ $briefing->created_at->format('d/m/Y') }}</p>
    </div>
    <div>
        <form action="/admin/briefings/{{ $briefing->id }}/status" method="POST" class="d-flex gap-2">
            <select name="status" class="form-select bg-dark text-white border-secondary">
                <option value="criado" {{ $briefing->status?->value === 'criado' ? 'selected' : '' }}>Aguardando Preenchimento</option>
                <option value="editando" {{ $briefing->status?->value === 'editando' ? 'selected' : '' }}>Em Edição (Cliente)</option>
                <option value="executando" {{ $briefing->status?->value === 'executando' ? 'selected' : '' }}>Em Execução (Agência)</option>
                <option value="finalizado" {{ $briefing->status?->value === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                <option value="cancelado" {{ $briefing->status?->value === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
            <button type="submit" class="btn btn-gold">Atualizar</button>
        </form>
    </div>
</header>

<div class="row">
    <div class="col-lg-8">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 gap-2" id="briefingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active bg-dark border border-secondary text-white shadow-sm" id="tab-form-btn" data-bs-toggle="pill" data-bs-target="#tab-form" type="button" role="tab"><i class="bi bi-file-text me-2"></i>Escopo</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-dark border border-secondary text-white shadow-sm" id="tab-messages-btn" data-bs-toggle="pill" data-bs-target="#tab-messages" type="button" role="tab"><i class="bi bi-chat-left-text me-2"></i>Mensagens</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-dark border border-secondary text-white shadow-sm" id="tab-vault-btn" data-bs-toggle="pill" data-bs-target="#tab-vault" type="button" role="tab"><i class="bi bi-safe me-2"></i>Cofre</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-dark border border-secondary text-white shadow-sm" id="tab-financial-btn" data-bs-toggle="pill" data-bs-target="#tab-financial" type="button" role="tab"><i class="bi bi-currency-dollar me-2"></i>Financeiro Interno</button>
            </li>
        </ul>

        <div class="tab-content" id="briefingTabsContent">
            <!-- TAB: ESCOPO / FORMULARIO -->
            <div class="tab-pane fade show active" id="tab-form" role="tabpanel">
                <div class="card briefing-card p-4 p-md-5">
                    <h4 class="text-white border-bottom border-secondary pb-3 mb-4">Respostas do Formulário</h4>
                    @if(empty($briefing->form_data))
                        <div class="alert alert-dark text-center py-5">
                            <i class="bi bi-clock-history fs-1 text-muted mb-3 d-block"></i>
                            <p class="mb-0 text-muted">O cliente ainda não enviou as respostas para este briefing.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-4">
                            @foreach($briefing->template->form_schema as $field)
                            @php
                                $question = $field['label'];
                                $phpKeyFallback = str_replace([' ', '.'], '_', $question);
                                $answer = $briefing->form_data[$question] ?? ($briefing->form_data[$phpKeyFallback] ?? 'Ainda não respondido.');
                            @endphp
                            <div class="card bg-dark border-secondary shadow-sm">
                                <div class="card-header bg-transparent border-secondary text-gold fw-bold">
                                    {{ $question }}
                                </div>
                                <div class="card-body text-white" style="background: rgba(0,0,0,0.2);">
                                    {!! nl2br(htmlspecialchars($answer)) !!}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB: MENSAGENS -->
            <div class="tab-pane fade" id="tab-messages" role="tabpanel">
                <div class="card briefing-card p-4">
                    <h4 class="text-white border-bottom border-secondary pb-3 mb-4">Chat do Projeto</h4>
                    
                    <div class="chat-thread mb-4" style="max-height: 50vh; overflow-y: auto; padding-right: 15px;">
                        @forelse($briefing->messages as $msg)
                            <div class="message-bubble mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 text-gold me-2">{{ $msg->sender->name ?? 'Admin' }}</h6>
                                    <small class="text-muted">{{ date('d/m/Y H:i', strtotime($msg->created_at)) }}</small>
                                    @if($msg->is_internal)
                                        <span class="badge bg-danger ms-2"><i class="bi bi-eye-slash"></i> Nota Interna Secreta</span>
                                    @endif
                                </div>
                                <div class="p-3 rounded-3" style="background-color: {{ $msg->is_internal ? '#450a0a' : '#1e293b' }}; border: 1px solid {{ $msg->is_internal ? '#fca5a5' : 'rgba(212, 175, 55, 0.3)' }}; color: #cbd5e1;">
                                    {!! nl2br(htmlspecialchars($msg->message)) !!}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">Nenhuma mensagem registrada no projeto.</p>
                        @endforelse
                    </div>

                    <form action="/admin/briefings/{{ $briefing->id }}/message" method="POST" class="mt-3 pt-3 border-top border-secondary">
                        <div class="mb-3">
                            <label class="form-label text-white">Nova Mensagem para o Cliente</label>
                            
                            @if(count($messageTemplates) > 0)
                            <div class="mb-2">
                                <select class="form-select form-select-sm bg-dark text-white border-secondary" onchange="if(this.value) document.getElementById('messageBox').value = this.value;">
                                    <option value="">-- Usar um Canned Response (Modelo Rápido) --</option>
                                    @foreach($messageTemplates as $mt)
                                        <option value="{{ htmlspecialchars($mt->body) }}">{{ $mt->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <textarea id="messageBox" name="message" class="form-control bg-dark text-white border-secondary" rows="4" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_internal" id="internalCheck">
                                <label class="form-check-label text-muted" for="internalCheck">Nota Interna (Oculto pro Cliente)</label>
                            </div>
                            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-send me-2"></i>Enviar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB: COFRE (VAULT) -->
            <div class="tab-pane fade" id="tab-vault" role="tabpanel">
                <div class="card briefing-card p-4">
                    <h4 class="text-white border-bottom border-secondary pb-3 mb-4">Cofre de Credenciais</h4>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Ambiente</th>
                                    <th>Serviço/Plataforma</th>
                                    <th>URL</th>
                                    <th>Usuário</th>
                                    <th>Senha</th>
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
                                    <td><a href="{{ $cred->url }}" target="_blank" class="text-gold">{{ $cred->url }}</a></td>
                                    <td class="text-muted"><code class="text-white bg-dark px-2 py-1 rounded">{{ $cred->username }}</code></td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="password" class="form-control bg-dark text-white border-secondary" value="{{ $cred->password }}" readonly id="pw-{{ $cred->id }}">
                                            <button class="btn btn-outline-secondary" type="button" onclick="const p=document.getElementById('pw-{{ $cred->id }}'); p.type=p.type==='password'?'text':'password';"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @if($cred->notes)
                                <tr>
                                    <td colspan="5" class="border-top-0 pt-0 text-muted small pb-3">Notas: {{ $cred->notes }}</td>
                                </tr>
                                @endif
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma credencial cadastrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h6 class="text-gold mt-4 mb-3">Adicionar Nova Credencial</h6>
                    <form action="/admin/briefings/{{ $briefing->id }}/credential" method="POST" class="row g-3">
                        <div class="col-md-4">
                            <select name="environment" class="form-select bg-dark text-white border-secondary" required>
                                <option value="production">Produção</option>
                                <option value="homologation">Homologação / Staging</option>
                                <option value="dev">Desenvolvimento</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="service_name" class="form-control bg-dark text-white border-secondary" placeholder="Nome do Serviço (ex: Cpanel, Cloudflare, Asaas)" required>
                        </div>
                        <div class="col-md-4">
                            <input type="url" name="url" class="form-control bg-dark text-white border-secondary" placeholder="URL de Acesso">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="username" class="form-control bg-dark text-white border-secondary" placeholder="Usuário / Login">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="password" class="form-control bg-dark text-white border-secondary" placeholder="Senha">
                        </div>
                        <div class="col-12">
                            <input type="text" name="notes" class="form-control bg-dark text-white border-secondary" placeholder="Notas Adicionais (opcional)">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-lock-fill me-2"></i>Salvar no Cofre</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB: FINANCEIRO -->
            <div class="tab-pane fade" id="tab-financial" role="tabpanel">
                <div class="card briefing-card p-4">
                    <h4 class="text-white border-bottom border-secondary pb-3 mb-4">Fechamento Financeiro Interno</h4>
                    <p class="text-muted">Somente a administração enxerga isso. Define o valor fechado em contrato para controle e extração de LTV/Receita futura.</p>
                    
                    <form action="/admin/briefings/{{ $briefing->id }}/agreed-value" method="POST" class="d-flex align-items-end gap-3 mt-4" style="max-width: 400px;">
                        <div class="flex-grow-1">
                            <label class="form-label text-gold">Valor Acertado (R$)</label>
                            <input type="text" name="agreed_value" class="form-control fw-bold bg-dark text-white border-secondary" placeholder="0.00" value="{{ $briefing->agreed_value }}">
                        </div>
                        <button type="submit" class="btn btn-success px-4">Salvar</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Sidebar Secundária da View de Briefing -->
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
                    <small class="d-block text-muted">Aprovado por Finanças</small>
                    @if($briefing->agreed_value)
                        <span class="badge bg-success mt-1">Sim (R$ {{ number_format((float)$briefing->agreed_value, 2, ',', '.') }})</span>
                    @else
                        <span class="badge bg-secondary mt-1">Não Acertado</span>
                    @endif
                </li>
                <li class="list-group-item bg-transparent text-white px-0 border-secondary">
                    <small class="d-block text-muted">Última Atualização</small>
                    {{ $briefing->updated_at->format('d/m/Y H:i') }}
                </li>
            </ul>
        </div>
    </div>
</div>

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
        // Restaurar aba pela hash da URL
        if(window.location.hash) {
            var hash = window.location.hash;
            var tabBtn = document.querySelector('button[data-bs-target="' + hash + '"]');
            if(tabBtn) {
                var tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }
        
        // Mudar URL ao clicar numa aba (para não perder estado num F5 ou após form submit)
        var tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(function(btn) {
            btn.addEventListener('shown.bs.tab', function (e) {
                var hash = e.target.getAttribute('data-bs-target');
                history.replaceState(null, null, hash);
            });
        });
    });
</script>
@endsection
