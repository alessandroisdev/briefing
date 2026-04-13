<?php

namespace App\Core;

use eftec\bladeone\BladeOne;

class View
{
    private static ?BladeOne $blade = null;

    public static function boot(): void
    {
        if (self::$blade !== null) return;

        $views = __DIR__ . '/../../resources/views';
        $cache = __DIR__ . '/../../storage/cache/views';

        // Create directories if they don't exist
        if (!is_dir($views)) mkdir($views, 0777, true);
        if (!is_dir($cache)) mkdir($cache, 0777, true);

        self::$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        // Pass Vite directive handler here if needed, or simply handle it in template
    }

    public static function render(string $view, array $data = []): string
    {
        self::boot();
        try {
            return self::$blade->run($view, $data);
        } catch (\Exception $e) {
            return "Erro ao renderizar view: " . $e->getMessage();
        }
    }
}
