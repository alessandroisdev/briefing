<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Proposta Comercial - {{ $quotation->title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; padding: 30px; box-sizing: border-box; }
        .header { border-bottom: 2px solid #D4AF37; padding-bottom: 20px; margin-bottom: 30px; width: 100%; }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .text-right { text-align: right; }
        .text-gold { color: #D4AF37; font-weight: bold; }
        .text-muted { color: #666; font-size: 12px; }
        h1 { margin: 0; font-size: 24px; color: #1e293b; }
        h3 { margin: 0; font-size: 18px; text-transform: uppercase; }
        
        .info-section { width: 100%; margin-bottom: 40px; }
        .info-section table { width: 100%; border-collapse: collapse; }
        .info-section td { vertical-align: top; width: 50%; }

        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table-items th { background-color: #0f172a; color: #fff; padding: 12px; text-align: left; font-size: 13px; }
        .table-items td { border-bottom: 1px solid #ddd; padding: 12px; }
        .table-items th.center, .table-items td.center { text-align: center; }
        .table-items th.right, .table-items td.right { text-align: right; }

        .summary { width: 100%; }
        .summary-box { float: right; width: 350px; background-color: #f8f9fa; padding: 20px; border-radius: 5px; }
        .summary-box table { width: 100%; }
        .summary-box td { padding: 5px 0; }
        .summary-total { border-top: 1px solid #ddd; font-size: 18px; font-weight: bold; color: #D4AF37; padding-top: 10px; margin-top: 5px; }
        
        .footer { clear: both; margin-top: 50px; font-size: 11px; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <h1 class="text-gold">Briefing<span style="color:#1e293b;">App</span></h1>
                        <p class="text-muted" style="margin-top: 5px;">Documento de Proposta Comercial</p>
                    </td>
                    <td class="text-right" style="width: 50%;">
                        <p style="margin:0;"><strong>Data Emissão:</strong> {{ $quotation->created_at->format('d/m/Y') }}</p>
                        <p style="margin:0;"><strong>Válido até:</strong> {{ date('d/m/Y', strtotime($quotation->valid_until)) }}</p>
                        <p style="margin:0;"><strong>Referência:</strong> #{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Info -->
        <div class="info-section">
            <table>
                <tr>
                    <td>
                        <h3 class="text-gold" style="margin-bottom: 10px;">Apresentado Para</h3>
                        <strong>{{ $quotation->client->company_name ?? 'Cliente Registrado' }}</strong><br>
                        {{ $quotation->client->user->name }}<br>
                        {{ $quotation->client->user->email }}
                    </td>
                    <td class="text-right">
                        <h3 class="text-gold" style="margin-bottom: 10px;">Emitido Por</h3>
                        <strong>Agência Dark Premium</strong><br>
                        Serviços de Desenvolvimento e Mídia<br>
                        contato@agencia.com
                    </td>
                </tr>
            </table>
        </div>

        <!-- Table -->
        <table class="table-items">
            <thead>
                <tr>
                    <th>DESCRIÇÃO DO ESCOPO / SERVIÇO</th>
                    <th class="center" style="width: 10%;">QTD</th>
                    <th class="right" style="width: 25%;">PREÇO UNITÁRIO (R$)</th>
                    <th class="right" style="width: 20%;">TOTAL (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="right"><strong>{{ number_format($item->total, 2, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div style="float: left; width: 50%; padding-right: 30px;">
                <h3 class="text-gold" style="margin-bottom: 10px;">Termos e Condições</h3>
                <p class="text-muted" style="line-height: 1.5;">
                    Os valores acima estão sujeitos a alterações caso haja mudança de escopo. 
                    O aceite eletrônico através da plataforma ou primeiro depósito bancário formaliza a autorização para execução dos serviços.
                    <br><br>
                    <strong>Situação no Sistema:</strong> 
                    @if($quotation->status === 'draft') Rascunho
                    @elseif($quotation->status === 'sent') Aguardando Avaliação
                    @elseif($quotation->status === 'accepted') APROVADO PELO CLIENTE
                    @else {{ $quotation->status }} @endif
                </p>
            </div>
            
            <div class="summary-box">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">R$ {{ number_format($quotation->total_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Desconto:</td>
                        <td class="text-right">- R$ 0,00</td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="summary-total"></div></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px; font-weight:bold;">T O T A L :</td>
                        <td class="text-right" style="font-size:18px; font-weight:bold; color:#D4AF37;">R$ {{ number_format($quotation->total_amount, 2, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            Documento gerado automaticamente pelo sistema de gerenciamento BriefingApp. 
            Não existem assinaturas físicas pois a autenticação é validada no portal do cliente (Auth ID: {{ $quotation->client_id }} / Proposta: {{ $quotation->id }}).
        </div>
    </div>
</body>
</html>
