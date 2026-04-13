<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificação do Sistema</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f6f9; padding-bottom: 60px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-top: 40px; }
        .header { background-color: #09101f; padding: 30px; text-align: center; border-bottom: 4px solid #D4AF37; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .footer { background-color: #f4f6f9; padding: 20px; text-align: center; font-size: 13px; color: #64748b; }
        .btn { display: inline-block; background-color: #D4AF37; color: #09101f !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 15px; margin-top: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn:hover { background-color: #C19B2E; }
        /* Typography */
        h2 { color: #09101f; font-size: 20px; margin-top: 0; font-weight: 600; }
        p { font-size: 15px; line-height: 1.6; color: #475569; margin: 0 0 15px 0; }
        strong { color: #334155; }
        /* Callout Box */
        .callout { background-color: #f8fafc; border-left: 4px solid #D4AF37; padding: 15px 20px; margin: 20px 0; border-radius: 0 6px 6px 0; }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <h1>NorteDev <span style="color: #D4AF37;">System</span></h1>
                </td>
            </tr>
            
            <!-- Body -->
            <tr>
                <td class="content">
                    @yield('content')
                </td>
            </tr>
            
        </table>
        
        <!-- Footer -->
        <table width="100%" style="max-width: 600px; margin: 0 auto;">
            <tr>
                <td class="footer">
                    <p style="margin:0; font-size: 12px;">© {{ date('Y') }} NorteDev Hub. Todos os direitos reservados.</p>
                    <p style="margin:5px 0 0 0; font-size: 12px; color: #94a3b8;">Este é um e-mail automático, por favor, não responda diretamente com seu cliente de e-mail.</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
