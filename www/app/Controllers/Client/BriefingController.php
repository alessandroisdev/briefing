<?php

namespace App\Controllers\Client;

use App\Core\View;
use App\Models\ClientBriefing;

class BriefingController
{
    public function show($id)
    {
        // Ideally we should verify if this briefing belongs to the currently authenticated client
        // For Phase 4, we assume access is granted by ID for the mockup or using simple logic
        $briefing = ClientBriefing::with(['template', 'client.user'])->find($id);

        if (!$briefing) {
            header('Location: /cliente/dashboard');
            exit;
        }

        echo View::render('client.briefings.show', ['briefing' => $briefing]);
    }

    public function save($id)
    {
        $briefing = ClientBriefing::find($id);

        if (!$briefing) {
            header('Location: /cliente/dashboard');
            exit;
        }

        $data = $_POST;
        
        // Dynamic fields come inside the POST mapped by their label/question
        // We will just store everything that is not a reserved keyword in form_data JSON
        $formData = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['_token', 'status'])) {
                // To avoid dots being replaced by underscores in PHP POST keys for some reason, 
                // we assume dynamic inputs passed as they are or base64 encoded keys if complex.
                // For simplicity, we just use the raw POST keys as questions.
                $formData[$key] = $value;
            }
        }

        // If the client submitted files, we would handle $_FILES here
        
        $briefing->update([
            'form_data' => $formData,
            'status' => 'editando' // Updates status to indicate client has interacted
        ]);

        header('Location: /cliente/briefings/' . $id . '?success=1');
        exit;
    }
}
