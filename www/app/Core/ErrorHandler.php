<?php

namespace App\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class ErrorHandler
{
    /** @var Logger */
    private static $logger;

    public static function register()
    {
        // 1. Setup Monolog logger
        self::$logger = new Logger('briefing_app');
        // Register standard log file
        $logPath = __DIR__ . '/../../storage/logs/app.log';
        
        // Ensure log directory exists
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0775, true);
        }

        self::$logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));

        // 2. Setup Whoops or generic error UI depending on APP_DEBUG / APP_ENV
        $isLocal = env('APP_ENV', 'production') === 'local' || env('APP_DEBUG', 'false') === 'true';

        $whoops = new Run();

        if (\PHP_SAPI === 'cli') {
            $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
        } elseif ($isLocal) {
            if (request()->expectsJson() || str_contains(request()->header('Accept'), 'json')) {
                $whoops->pushHandler(new JsonResponseHandler());
            } else {
                $prettyPageHandler = new PrettyPageHandler();
                $prettyPageHandler->setPageTitle('BriefingApp - Erro Local');
                $whoops->pushHandler($prettyPageHandler);
            }
        } else {
            // Em Produção, configuramos o Whoops para interceptar o erro mas renderizar nossa view de Error 500
            $whoops->pushHandler(function ($exception, $inspector, $run) {
                
                // Grava no log de produção
                self::logException($exception);

                // Mostra Error 500 sem entregar stack trace pro usuário
                http_response_code(500);
                
                if (request()->expectsJson() || str_contains(request()->header('Accept'), 'json')) {
                    echo json_encode([
                        'error' => 'Erro Interno do Servidor',
                        'message' => 'Ocorreu um erro inesperado. Tente novamente mais tarde.',
                        'reference_id' => time()
                    ]);
                } else {
                    echo "<h1>Ops! Erro 500</h1><p>Uma falha inesperada ocorreu no sistema. A equipe técnica já foi notificada. Volte dentro de alguns minutos.</p>";
                }

                return \Whoops\Handler\Handler::QUIT;
            });
        }

        // 3. Registrar Whoops globalmente (agora ele irá capturar as exceptions e fatals)
        $whoops->register();
    }

    /**
     * Helper manual para gravar logs no sistema de Monolog.
     */
    public static function log($level, $message, array $context = [])
    {
        if (!self::$logger) {
            return;
        }

        self::$logger->log($level, $message, $context);
    }

    private static function logException($exception)
    {
        self::$logger->error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
