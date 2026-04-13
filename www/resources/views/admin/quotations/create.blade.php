@extends('layouts.admin')

@section('title', 'Criar Orçamento - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="javascript:history.back()" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mb-0">Compositor de Cotação</h2>
        <p class="text-muted mb-0" style="color: #94a3b8 !important;">Monte a proposta financeira detalhada para apresentar ao cliente</p>
    </div>
    
    @if(count($templates) > 0)
    <div class="dropdown">
        <button class="btn btn-outline-info dropdown-toggle shadow-sm px-4" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-magic me-2"></i> Importar Modelo Pronto
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-secondary bg-dark">
            @foreach($templates as $t)
                <li><a class="dropdown-item text-white" href="#" onclick='importTemplate({!! htmlspecialchars(json_encode($t->base_items_json), ENT_QUOTES, "UTF-8") !!}, "{{ $t->title }}", event)'>{{ $t->title }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif
</header>

@if(isset($_SESSION['error']))
    <div class="alert alert-danger bg-danger text-white border-0">{{ $_SESSION['error'] }}</div>
    @php unset($_SESSION['error']); @endphp
@endif

<form action="/admin/quotations/store" method="POST" id="quotationForm">
    @if($briefing)
        <input type="hidden" name="briefing_id" value="{{ $briefing->id }}">
    @endif

    <div class="row g-4">
        <!-- Coluna Esquerda: Meta Dados -->
        <div class="col-lg-4">
            <div class="card briefing-card p-4 h-100 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
                <h5 class="text-white mb-4"><i class="bi bi-info-circle text-gold me-2"></i> Detalhes da Proposta</h5>
                
                <div class="mb-4">
                    <label class="form-label text-gold">Cliente Responsável</label>
                    <select name="client_id" class="form-select bg-dark text-white border-secondary" required {{ $selectedClientId ? 'readonly style=pointer-events:none;opacity:0.8;' : '' }}>
                        <option value="">Selecione o Cliente...</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ $selectedClientId == $c->id ? 'selected' : '' }}>{{ $c->company_name ?? $c->user->name }} - {{ $c->user->email }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label text-gold">Título do Orçamento</label>
                    <input type="text" name="title" id="quoteTitle" class="form-control bg-dark text-white border-secondary fw-bold" placeholder="Ex: Proposta SaaS V1" value="{{ $briefing ? 'Orçamento: ' . $briefing->title : '' }}" required>
                </div>

                <div class="mb-4 mt-auto border-top border-secondary pt-4">
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Quantidade de Itens</span>
                        <span id="labelItemCount" class="fw-bold">0</span>
                    </div>
                    <div class="d-flex justify-content-between text-white fs-4">
                        <span>Total (R$)</span>
                        <strong class="text-gold" id="labelGrandTotal">0,00</strong>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold w-100 py-3 fw-bold mt-2"><i class="bi bi-cloud-arrow-up-fill me-2"></i> Salvar e Gerar Cotação</button>
            </div>
        </div>

        <!-- Coluna Direita: Itens da Compra -->
        <div class="col-lg-8">
            <div class="card briefing-card p-4 min-vh-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
                    <h5 class="text-white m-0"><i class="bi bi-list-check text-gold me-2"></i> Escopo de Precificação</h5>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="addRow()"><i class="bi bi-plus"></i> Adicionar Linha Vazia</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0" id="itemsTable">
                        <thead>
                            <tr class="text-muted small">
                                <th style="width: 50%;">Descrição do Item / Serviço</th>
                                <th style="width: 15%;">Qtd.</th>
                                <th style="width: 20%;">Preço Unit. (R$)</th>
                                <th style="width: 10%;" class="text-end">Subtotal</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Inserção Dinâmica via Javascript -->
                        </tbody>
                    </table>
                </div>

                <div id="emptyState" class="text-center py-5 text-muted">
                    <i class="bi bi-inboxes mb-3 fs-1 d-block"></i>
                    Nenhum serviço adicionado ainda.<br>Clique em "Novo Item" ou Importe um Modelo no menu superior.
                </div>
            </div>
        </div>
    </div>
</form>

<template id="itemRowTemplate">
    <tr class="align-middle">
        <td><input type="text" name="descriptions[]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Nome do Serviço" required></td>
        <td><input type="number" name="quantities[]" class="form-control form-control-sm bg-dark text-white border-secondary calc-qty" value="1" min="1" required></td>
        <td><input type="text" name="unit_prices[]" class="form-control form-control-sm bg-dark text-white border-secondary calc-price" placeholder="0,00" required></td>
        <td class="text-end fw-bold text-gold row-total">0,00</td>
        <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

@endsection

@section('scripts')
<script>
    const tbody = document.getElementById('itemsBody');
    const template = document.getElementById('itemRowTemplate');
    const emptyState = document.getElementById('emptyState');
    
    function maskCurrency(val) {
        if(typeof val === 'number') val = val.toFixed(2);
        val = val.toString().replace(/\D/g, "");
        if(val === "") return "";
        val = (val / 100).toFixed(2) + "";
        val = val.replace(".", ",");
        val = val.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
        val = val.replace(/(\d)(\d{3}),/g, "$1.$2,");
        return val;
    }

    function parseCurrency(str) {
        if(!str) return 0;
        let v = str.replace(/[^\d,-]/g, '').replace(',', '.');
        return parseFloat(v) || 0;
    }

    function calculateTotals() {
        let rows = tbody.querySelectorAll('tr');
        let grandTotal = 0;
        let count = 0;

        rows.forEach(row => {
            count++;
            let qty = parseInt(row.querySelector('.calc-qty').value) || 0;
            let priceStr = row.querySelector('.calc-price').value;
            let price = parseCurrency(priceStr);
            
            let lineTotal = qty * price;
            grandTotal += lineTotal;

            row.querySelector('.row-total').innerText = maskCurrency(lineTotal.toFixed(2));
        });

        document.getElementById('labelItemCount').innerText = count;
        document.getElementById('labelGrandTotal').innerText = maskCurrency(grandTotal.toFixed(2));

        if(count > 0) {
            emptyState.style.display = 'none';
        } else {
            emptyState.style.display = 'block';
        }
    }

    function setupRowEvents(row) {
        const qtyInput = row.querySelector('.calc-qty');
        const priceInput = row.querySelector('.calc-price');

        qtyInput.addEventListener('input', calculateTotals);
        priceInput.addEventListener('input', function(e) {
            e.target.value = maskCurrency(e.target.value);
            calculateTotals();
        });
    }

    function addRow(desc = "", qty = 1, price = 0) {
        let clone = template.content.cloneNode(true);
        let tr = clone.querySelector('tr');
        
        tr.querySelector('input[name="descriptions[]"]').value = desc;
        tr.querySelector('input[name="quantities[]"]').value = qty;
        tr.querySelector('input[name="unit_prices[]"]').value = price > 0 ? maskCurrency((price * 100).toFixed(0)) : "";
        
        setupRowEvents(tr);
        tbody.appendChild(tr);
        calculateTotals();
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateTotals();
    }

    // Acessível do botão de modelo de importação
    window.importTemplate = function(items, title, event) {
        event.preventDefault();
        
        // Define title se estiver vazio
        const tInput = document.getElementById('quoteTitle');
        if(!tInput.value || tInput.value.trim() === "") {
            tInput.value = "Orçamento: " + title;
        }

        // Adiciona as Linhas
        if(items && items.length > 0) {
            items.forEach(i => {
                // items from DB: ['description', 'quantity', 'unit_price']
                addRow(i.description, i.quantity, i.unit_price);
            });
        }
    }

    // Add empty row na inicializacao caso vazio
    document.addEventListener("DOMContentLoaded", function() {
        if(tbody.children.length === 0) {
            addRow();
        }
    });

</script>
@endsection
