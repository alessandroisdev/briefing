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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['flash_messages'][] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('danger', $message); // Bootstrap uses 'danger' for red
    }

    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    public static function info(string $message): void
    {
        self::set('info', $message);
    }

    /**
     * Recupera todas as mensagens e limpa a sessão
     */
    public static function get(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        
        return $messages;
    }
}
