<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\MessageTemplate;
use App\Core\Flash;

class MessageTemplateController
{
    public function index()
    {
        $templates = MessageTemplate::orderBy('title', 'asc')->get();
        response(View::render('admin.templates.messages.index', ['templates' => $templates]))->send();
    }

    public function create()
    {
        response(View::render('admin.templates.messages.create'))->send();
    }

    public function store()
    {
        $data = request()->all();
        
        // Basic validation
        if (empty($data['title']) || empty($data['content'])) {
            Flash::error('Título e Conteúdo são obrigatórios.');
            response()->redirect('/admin/templates/messages/create');
            return;
        }

        MessageTemplate::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'is_active' => isset($data['is_active']) ? 1 : 0
        ]);

        Flash::success('Modelo de Resposta Rápida criado com sucesso!');
        response()->redirect('/admin/templates/messages');
    }

    public function edit($id)
    {
        $template = MessageTemplate::find($id);

        if (!$template) {
            Flash::error('Modelo não encontrado.');
            response()->redirect('/admin/templates/messages');
            return;
        }

        response(View::render('admin.templates.messages.edit', ['template' => $template]))->send();
    }

    public function update($id)
    {
        $template = MessageTemplate::find($id);

        if (!$template) {
            Flash::error('Modelo não encontrado.');
            response()->redirect('/admin/templates/messages');
            return;
        }

        $data = request()->all();

        if (empty($data['title']) || empty($data['content'])) {
            Flash::error('Título e Conteúdo são obrigatórios.');
            response()->redirect('/admin/templates/messages/' . $id . '/edit');
            return;
        }

        $template->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'is_active' => isset($data['is_active']) ? 1 : 0
        ]);

        Flash::success('Modelo atualizado com sucesso!');
        response()->redirect('/admin/templates/messages');
    }
}
