<?php

namespace App\Core;

class Flash
{
    /**
     * Define uma mensagem flash para a próxima requisição
     *
     * @param string $type (success, danger, warning, info)
     * @param string $message
     */
    public static function set(string $type, string $message): void
    {
        $messages = session()->get('flash_messages', []);
        $messages[] = [
            'type'    => $type,
            'message' => $message
        ];
        session()->put('flash_messages', $messages);
    }

    public static function success(string $message): void
    {
        self::set(\App\Enums\AlertType::Success->value, $message);
    }

    public static function error(string $message): void
    {
        self::set(\App\Enums\AlertType::Danger->value, $message);
    }

    public static function warning(string $message): void
    {
        self::set(\App\Enums\AlertType::Warning->value, $message);
    }

    public static function info(string $message): void
    {
        self::set(\App\Enums\AlertType::Info->value, $message);
    }

    /**
     * Recupera todas as mensagens e limpa a sessão
     */
    public static function get(): array
    {
        $messages = session()->get('flash_messages', []);
        session()->forget('flash_messages');
        return $messages;
    }
}
