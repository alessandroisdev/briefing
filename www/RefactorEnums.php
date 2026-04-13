<?php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/app'));
foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

$replaces = [
    "User::where('role', 'admin')" => "User::where('role', \App\Enums\UserRole::Admin->value)",
    "User::where('role', 'client')" => "User::where('role', \App\Enums\UserRole::Client->value)",
    "'role' => 'client'" => "'role' => \App\Enums\UserRole::Client",
    "'role' => 'admin'" => "'role' => \App\Enums\UserRole::Admin",
    
    "where('status', 'active')" => "where('status', \App\Enums\ActiveStatus::Active->value)",
    "'status' => 'active'" => "'status' => \App\Enums\ActiveStatus::Active",
    "'status' => 'inactive'" => "'status' => \App\Enums\ActiveStatus::Inactive",
    
    "where('status', 'pending')" => "where('status', \App\Enums\EmailJobStatus::Pending->value)",
    "update(['status' => 'pending'])" => "update(['status' => \App\Enums\EmailJobStatus::Pending])",
    "'status' => 'pending'" => "'status' => \App\Enums\EmailJobStatus::Pending",
    "'status' => 'sent'" => "'status' => \App\Enums\EmailJobStatus::Sent",
    "'status' => 'failed'" => "'status' => \App\Enums\EmailJobStatus::Failed",
    
    // ClientBriefing
    "'status' => 'criado'" => "'status' => \App\Enums\BriefingStatus::Criado",
    "'status' => 'editando'" => "'status' => \App\Enums\BriefingStatus::Editando",
    "'status' => 'executando'" => "'status' => \App\Enums\BriefingStatus::Executando",
    "'status' => 'cancelado'" => "'status' => \App\Enums\BriefingStatus::Cancelado",
    "'status' => 'finalizado'" => "'status' => \App\Enums\BriefingStatus::Finalizado",
    
    // AlertType
    "'type' => 'success'" => "'type' => \App\Enums\AlertType::Success",
    "'type' => 'info'" => "'type' => \App\Enums\AlertType::Info",
    "'type' => 'warning'" => "'type' => \App\Enums\AlertType::Warning",
    "'type' => 'danger'" => "'type' => \App\Enums\AlertType::Danger",
    "Flash::set('success'" => "Flash::set(\App\Enums\AlertType::Success->value",
    "Flash::set('danger'" => "Flash::set(\App\Enums\AlertType::Danger->value",
    "Flash::set('warning'" => "Flash::set(\App\Enums\AlertType::Warning->value",
    "Flash::set('info'" => "Flash::set(\App\Enums\AlertType::Info->value",
    "NotificationService::sendToAdmins(\n            \"Briefing Atualizado\",\n            \"O cliente <b>{\$user->name}</b> atualizou e salvou respostas no projeto '{\$briefing->title}'.\",\n            \"success\"," 
        => "NotificationService::sendToAdmins(\n            \"Briefing Atualizado\",\n            \"O cliente <b>{\$user->name}</b> atualizou e salvou respostas no projeto '{\$briefing->title}'.\",\n            \App\Enums\AlertType::Success->value,",
];

foreach ($files as $file) {
    if (strpos($file, 'RefactorEnums.php') !== false) continue; // skip this file
    if (strpos($file, 'app\Enums\\') !== false || strpos($file, 'app/Enums/') !== false) continue; // skip new enums
    
    $content = file_get_contents($file);
    $original = $content;
    
    foreach ($replaces as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Special regex replacements for dynamic statuses like $data['status']
    // Example: $briefing->update(['status' => $data['status']]);
    // The data might be a string, but the model casts it properly automatically if it's from the web.
    // However, it's safer to convert $data['status'] ?
    // No, eloquent handles string -> enum casting implicitly if defined in $casts.
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
echo "Refactor complete.\n";
