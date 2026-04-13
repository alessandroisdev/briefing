@extends('layouts.admin')

@section('title', 'Configurações de E-mail (SMTP)')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-white mb-0"><i class="bi bi-bell-fill me-2" style="color: #D4AF37;"></i> Push & E-mail Auth</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Configure seu provedor SMTP ou API</p>
    </div>
    <div>
        <a href="/admin/queue" class="btn btn-outline-light shadow-sm px-4">
            <i class="bi bi-list-check me-2"></i> Monitorar Fila de Envios
        </a>
    </div>
</header>

<div class="card bg-dark border-0 shadow-sm">
    <div class="card-body p-4 text-white p-md-5">
        <form action="/admin/settings/email" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Host SMTP</label>
                    <input type="text" name="smtp_host" class="form-control text-white" value="{{ $settings['smtp_host'] ?? '' }}" placeholder="smtp.mailtrap.io" style="background-color: #09101f; border-color: #1a253c;">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Porta</label>
                    <input type="number" name="smtp_port" class="form-control text-white" value="{{ $settings['smtp_port'] ?? '587' }}" placeholder="587" style="background-color: #09101f; border-color: #1a253c;">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label text-muted">Segurança</label>
                    <select name="smtp_secure" class="form-select text-white" style="background-color: #09101f; border-color: #1a253c;">
                        <option value="tls" {{ ($settings['smtp_secure'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['smtp_secure'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ ($settings['smtp_secure'] ?? '') == 'none' ? 'selected' : '' }}>Nenhuma</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Usuário</label>
                    <input type="text" name="smtp_user" class="form-control text-white" value="{{ $settings['smtp_user'] ?? '' }}" placeholder="Username/Apikey" style="background-color: #09101f; border-color: #1a253c;">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Senha / API Key</label>
                    <input type="password" name="smtp_pass" class="form-control text-white" value="{{ $settings['smtp_pass'] ?? '' }}" placeholder="••••••••••••" style="background-color: #09101f; border-color: #1a253c;">
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">E-mail do Remetente</label>
                    <input type="email" name="from_email" class="form-control text-white" value="{{ $settings['from_email'] ?? '' }}" placeholder="no-reply@suaempresa.com" style="background-color: #09101f; border-color: #1a253c;">
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted">Nome do Remetente</label>
                    <input type="text" name="from_name" class="form-control text-white" value="{{ $settings['from_name'] ?? '' }}" placeholder="Sistema de Briefing" style="background-color: #09101f; border-color: #1a253c;">
                </div>
            </div>
            <h5 class="text-white mt-5 mb-4 border-bottom border-secondary pb-3"><i class="bi bi-telegram me-2 text-info"></i> API do Telegram (Mensageria Instantânea)</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="telegram_enabled" id="telegramEnabled" value="1" {{ ($settings['telegram_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-white" for="telegramEnabled">Habilitar Alertas In-System via Telegram</label>
                        <small class="d-block text-muted">Isso fará com que toda notificação gerada seja encaminhada para o celular.</small>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted">Bot Token (BotFather)</label>
                    <input type="password" name="telegram_bot_token" class="form-control text-white" value="{{ $settings['telegram_bot_token'] ?? '' }}" placeholder="7001...:AAH..." style="background-color: #09101f; border-color: #1a253c;">
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted">Chat ID (Grupo de Agência ou ID Pessoal)</label>
                    <input type="text" name="telegram_chat_id" class="form-control text-white" value="{{ $settings['telegram_chat_id'] ?? '' }}" placeholder="-1000..." style="background-color: #09101f; border-color: #1a253c;">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-2 border-top border-secondary pt-4">
                <button type="submit" class="btn btn-gold fw-bold px-4"><i class="bi bi-save me-2"></i> Salvar Configurações</button>
            </div>
        </form>
    </div>
</div>
@endsection
