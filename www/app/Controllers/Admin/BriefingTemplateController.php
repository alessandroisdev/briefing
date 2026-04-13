<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\BriefingTemplate;

class BriefingTemplateController
{
    public function index()
    {
        $templates = BriefingTemplate::orderBy('id', 'desc')->get();
        response(View::render('admin.templates.index', ['templates' => $templates]))->send();
    }

    public function create()
    {
        response(View::render('admin.templates.create'))->send();
    }

    public function store()
    {
        $data = request()->all();
        BriefingTemplate::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'form_schema' => json_decode($data['form_schema'], true) ?? [],
            'status' => \App\Enums\ActiveStatus::Active
        ]);

        \App\Core\Flash::success('Modelo de briefing criado com sucesso!');
        response()->redirect('/admin/templates');
    }

    public function edit($id)
    {
        $template = BriefingTemplate::find($id);
        if (!$template) {
            \App\Core\Flash::error('Modelo não encontrado!');
            response()->redirect('/admin/templates');
        }

        response(View::render('admin.templates.edit', ['template' => $template]))->send();
    }

    public function update($id)
    {
        $template = BriefingTemplate::find($id);
        if ($template) {
            $data = request()->all();
            
            // Try to map status enum safely
            $statusEnum = \App\Enums\ActiveStatus::tryFrom($data['status'] ?? 'active') 
                          ?? \App\Enums\ActiveStatus::Active;
                          
            $template->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'form_schema' => json_decode($data['form_schema'], true) ?? [],
                'status' => $statusEnum
            ]);
            \App\Core\Flash::success('Modelo atualizado com sucesso!');
        }

        response()->redirect('/admin/templates');
    }
}
