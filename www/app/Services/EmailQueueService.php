<?php

namespace App\Services;

use App\Models\EmailJob;
use App\Core\RedisManager;

class EmailQueueService
{
    /**
     * Adiciona um e-mail a fila do banco de dados e notifica o Redis worker
     */
    public static function enqueue(string $recipientEmail, string $recipientName, string $subject, string $body, array $attachments = []): bool
    {
        try {
            // First, persist the intent in DB
            $job = EmailJob::create([
                'recipient_email' => $recipientEmail,
                'recipient_name'  => $recipientName,
                'subject'         => $subject,
                'body'            => $body,
                'status'          => \App\Enums\EmailJobStatus::Pending,
                'attempts'        => 0,
                'attachments'     => !empty($attachments) ? json_encode($attachments) : null
            ]);

            // Now, push the Job ID to the Redis List so the worker picks it up immediately
            $redis = RedisManager::getClient();
            $redis->rpush('email_queue', $job->id);

            return true;
        } catch (\Exception $e) {
            error_log("Failed to enqueue email: " . $e->getMessage());
            return false;
        }
    }
}
