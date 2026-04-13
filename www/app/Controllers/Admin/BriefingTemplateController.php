<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\BriefingTemplate;

class BriefingTemplateController
{
    public function index()
    {
        $templates = BriefingTemplate::orderBy('id', 'desc')->get();
        echo View::render('admin.templates.index', ['templates' => $templates]);
    }

    public function create()
    {
        echo View::render('admin.templates.create');
    }

    public function store()
    {
        $data = $_POST;
        BriefingTemplate::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'form_schema' => json_decode($data['form_schema'], true) ?? [],
            'status' => 'active'
        ]);

        \App\Core\Flash::success('Modelo de briefing criado com sucesso!');
        header('Location: /admin/templates');
        exit;
    }

    public function edit($id)
    {
        $template = BriefingTemplate::find($id);
        if (!$template) {
            \App\Core\Flash::error('Modelo não encontrado!');
            header('Location: /admin/templates');
            exit;
        }

        echo View::render('admin.templates.edit', ['template' => $template]);
    }

    public function update($id)
    {
        $template = BriefingTemplate::find($id);
        if ($template) {
            $data = $_POST;
            $template->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'form_schema' => json_decode($data['form_schema'], true) ?? [],
                'status' => $data['status'] ?? 'active'
            ]);
            \App\Core\Flash::success('Modelo atualizado com sucesso!');
        }

        header('Location: /admin/templates');
        exit;
    }
}
