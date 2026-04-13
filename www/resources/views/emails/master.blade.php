<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notificação de Sistema' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #334155;
            line-height: 1.6;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #0d1527; /* Dark Navy Base */
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #D4AF37; /* Premium Gold Accent */
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
            font-size: 16px;
        }
        .content h2, .content h3, .content h4 {
            color: #0f172a;
            margin-top: 0;
        }
        .content p {
            margin-bottom: 20px;
        }
        .button-wrap {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #D4AF37;
            color: #000000 !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer {
            background-color: #1e293b;
            padding: 30px;
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
        }
        .footer a {
            color: #cbd5e1;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <!-- Fallback to Title if no logo image is attached via general settings -->
                <h1>{{ \App\Models\EmailSetting::getVal('from_name', 'Agência Digital') }}</h1>
            </div>
            
            <div class="content">
                {!! $content !!}
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ \App\Models\EmailSetting::getVal('from_name', 'Sua Agência') }}. Todos os direitos reservados.</p>
                <p>Esta é uma mensagem automática. Por favor, acesse seu painel para responder.</p>
            </div>
        </div>
    </div>
</body>
</html>
