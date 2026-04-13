<?php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Replace ->status with ->status?->value only when used as object property accessor
    $content = preg_replace('/->status(?!\s*->value|\s*\()/i', '->status?->value', $content);
    
    // Replace ->role with ->role?->value
    $content = preg_replace('/->role(?!\s*->value|\s*\()/i', '->role?->value', $content);

    // If ->type is used on object, e.g. $notification->type
    $content = preg_replace('/->type(?!\s*->value|\s*\()/i', '->type?->value', $content);

    // Also fix Admin Dashboard 'admin' hardcoded role search
    // $adminUser = \App\Models\User::where('role', 'admin')->first(); -> \App\Enums\UserRole::Admin->value
    $content = str_replace(
        "where('role', 'admin')",
        "where('role', \App\Enums\UserRole::Admin->value)",
        $content
    );

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
echo "Blade refactor complete.\n";
