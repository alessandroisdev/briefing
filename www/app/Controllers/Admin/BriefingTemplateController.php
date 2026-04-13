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
        
        // Simulating JSON dynamic schema building from a frontend drag&drop or text array
        // For now, we expect "form_schema" to be a valid JSON payload string via a hidden input 
        // compiled by our JS module on the frontend
        
        $formSchema = json_decode($data['form_schema'] ?? '[]', true);

        BriefingTemplate::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'form_schema' => $formSchema,
            'status' => $data['status'] ?? 'active'
        ]);

        header('Location: /admin/templates');
        exit;
    }

    public function edit($id)
    {
        $template = BriefingTemplate::find($id);
        if (!$template) {
            header('Location: /admin/templates');
            exit;
        }

        echo View::render('admin.templates.edit', ['template' => $template]);
    }

    public function update($id)
    {
        $data = $_POST;
        $template = BriefingTemplate::find($id);

        if ($template) {
            $formSchema = json_decode($data['form_schema'] ?? '[]', true);
            $template->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'form_schema' => $formSchema,
                'status' => $data['status'] ?? 'active'
            ]);
        }

        header('Location: /admin/templates');
        exit;
    }
}
