<?php

namespace App\Core;

use Predis\Client;

class RedisManager
{
    private static $client = null;

    public static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host'   => $_ENV['REDIS_HOST'] ?? 'redis',
                'port'   => $_ENV['REDIS_PORT'] ?? 6379,
            ]);
        }

        return self::$client;
    }

    /**
     * Publishes an event to a specific Redis channel
     */
    public static function publish(string $channel, array $data)
    {
        $client = self::getClient();
        $payload = json_encode($data);
        $client->publish($channel, $payload);
    }
}
