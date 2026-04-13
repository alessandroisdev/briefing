<?php

namespace App\Controllers;

use App\Core\RedisManager;

class SSEController
{
    public function stream()
    {
        // Required Headers for Server-Sent Events (SSE)
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        // Prevent buffering in Nginx/PHP-FPM
        header('X-Accel-Buffering: no');
        ini_set('output_buffering', 0);
        ini_set('implicit_flush', 1);

        // Turn off execution time limit so script runs continuously
        set_time_limit(0);

        // Optionally, ensure session writes are committed so we don't lock the session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Subscribe to a generic channel for this MVP
        $redis = RedisManager::getClient();
        $pubsub = $redis->pubSubLoop();
        $pubsub->subscribe('notifications_channel');

        // Initial connection message to establish stream
        echo "event: connected\n";
        echo "data: {\"status\":\"ok\"}\n\n";
        echo str_pad('', 4096) . "\n"; // flush buffer initially
        flush();

        foreach ($pubsub as $message) {
            switch ($message->kind) {
                case 'subscribe':
                    // Successfully registered to channel
                    break;
                case 'message':
                    // Forward redis message directly to the browser via SSE format
                    echo "data: {$message->payload}\n\n";
                    flush();
                    break;
            }

            // Client disconnected gracefully check
            if (connection_aborted()) {
                $pubsub->unsubscribe();
                break;
            }
        }
    }
}
