@extends('layouts.admin')

@section('title', 'Editar Modelo - BriefingApp')

@section('admin_content')
<header class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <a href="/admin/templates" class="text-decoration-none" style="color: #94a3b8;"><i class="bi bi-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold text-white mt-2 mb-0">Editar Modelo</h2>
    </div>
</header>

<div class="row">
    <div class="col-lg-8">
        <div class="card briefing-card p-4 p-md-5 border-top border-warning border-4" style="border-top-color: #D4AF37 !important;">
            <form action="/admin/templates/{{ $template->id }}/update" method="POST" id="templateForm">
                
                <div class="row g-3">
                    <div class="col-md-8 mb-4">
                        <label class="form-label text-white">Título do Modelo</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ $template->title }}" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label text-white">Status</label>
                        <select name="status" class="form-control form-control-lg">
                            <option value="active" {{ $template->status?->value === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ $template->status?->value === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Instruções / Descrição Riquíssima</label>
                    <div id="editor-container" style="height: 200px; background: #09101f; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;">{!! $template->description !!}</div>
                    <input type="hidden" name="description" id="hiddenDescription">
                </div>
                
                <input type="hidden" name="form_schema" id="formSchemaInput" value="">

                <div class="mt-4 pt-4 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white mb-0"><i class="bi bi-ui-radios"></i> Construtor de Campos Dinâmicos</h5>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="addField()"><i class="bi bi-plus"></i> Adicionar Pergunta</button>
                    </div>

                    <div id="fieldsContainer" class="d-flex flex-column gap-3">
                        <!-- JS renders here -->
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-end gap-3">
                    <a href="/admin/templates" class="btn btn-outline-light">Cancelar</a>
                    <button type="button" onclick="submitForm()" class="btn btn-gold px-5 fw-semibold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('admin_scripts')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    });

    // Populate existing schema
    let schemaFields = {!! json_encode($template->form_schema ?? []) !!};
    
    function renderFields() {
        const container = document.getElementById('fieldsContainer');
        container.innerHTML = '';

        if(schemaFields.length === 0) {
            container.innerHTML = '<div class="text-muted small text-center p-3 border border-secondary rounded border-dashed">Nenhum campo. Adicione perguntas ao briefing.</div>';
        }

        schemaFields.forEach((field, index) => {
            const row = document.createElement('div');
            row.className = 'd-flex gap-2 align-items-center p-3 rounded';
            row.style.backgroundColor = 'rgba(255,255,255,0.02)';
            row.style.border = '1px solid rgba(255,255,255,0.05)';
            
            row.innerHTML = `
                <div class="flex-grow-1">
                    <input type="text" class="form-control form-control-sm mb-2" value="${field.label}" onchange="updateField(${index}, 'label', this.value)" placeholder="Sua pergunta / Título do Campo">
                    <select class="form-control form-control-sm text-white" style="background:#09101f;" onchange="updateField(${index}, 'type', this.value)">
                        <option value="text" ${field.type === 'text' ? 'selected' : ''}>Texto Curto</option>
                        <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>Texto Longo (Parágrafo)</option>
                        <option value="file" ${field.type === 'file' ? 'selected' : ''}>Upload de Arquivo</option>
                    </select>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(${index})"><i class="bi bi-trash"></i></button>
            `;
            container.appendChild(row);
        });
    }

    function addField() {
        schemaFields.push({ label: '', type: 'text' });
        renderFields();
    }

    function removeField(index) {
        schemaFields.splice(index, 1);
        renderFields();
    }

    function updateField(index, key, value) {
        schemaFields[index][key] = value;
    }

    function submitForm() {
        document.getElementById('hiddenDescription').value = quill.root.innerHTML;
        document.getElementById('formSchemaInput').value = JSON.stringify(schemaFields);
        document.getElementById('templateForm').submit();
    }

    renderFields();
</script>

<style>
.ql-toolbar { background-color: #101c38; border-color: rgba(255,255,255,0.1) !important; }
.ql-container { border-color: rgba(255,255,255,0.1) !important; }
.ql-snow .ql-stroke { stroke: #fff; }
.ql-snow .ql-fill { fill: #fff; }
.ql-snow .ql-picker { color: #fff; }
</style>
@endsection
