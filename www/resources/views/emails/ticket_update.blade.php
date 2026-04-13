@extends('emails.layout')

@section('content')
    <h2>Olá, {{ $userName }}!</h2>
    
    <p>O ticket <strong>#{{ $ticketId }} - {{ $ticketSubject }}</strong> recebeu uma nova atualização no nosso sistema.</p>

    <div class="callout">
        <p style="font-size: 14px; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Nova Mensagem de: {{ $senderName }}</p>
        <p style="margin: 0; font-style: italic; color: #1e293b;">
            "{{ mb_strlen(strip_tags($messageContent)) > 150 ? mb_substr(strip_tags($messageContent), 0, 150) . '...' : strip_tags($messageContent) }}"
        </p>
    </div>

    @if($hasAttachments)
    <p style="font-size: 13px; color: #64748b; margin-top: 5px;">
        <span style="font-weight: 600; color: #09101f;">Anexos detectados:</span> Verifique os arquivos anexados a este e-mail.
    </p>
    @endif

    <div style="text-align: center; margin-top: 35px; margin-bottom: 10px;">
        <a href="{{ $actionUrl }}" class="btn" style="color: #000 !important; text-decoration: none;">Acessar o Ticket</a>
    </div>

    <p style="font-size: 13px; color: #94a3b8; text-align: center; margin-top: 25px;">
        Se o botão acima não funcionar, copie e cole o link abaixo em seu navegador:<br>
        <span style="color: #D4AF37;">{{ $actionUrl }}</span>
    </p>
@endsection
