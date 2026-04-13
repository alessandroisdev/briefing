<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;
use App\Models\EmailJob;
use App\Models\EmailSetting;
use App\Core\RedisManager;

// Load Env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Boot Database
require_once __DIR__ . '/app/Core/Database.php';

echo "Worker started. Waiting for email jobs...\n";

$redis = RedisManager::client();

while (true) {
    try {
        // Blocks until an item is available in the 'email_queue', timeout 0 (infinite)
        $result = $redis->blpop(['email_queue'], 0);
        
        if (empty($result)) {
            continue;
        }

        $jobId = $result[1];
        
        $job = EmailJob::find($jobId);
        
        if (!$job || $job->status === 'sent') {
            continue;
        }

        echo "Processing Email Job #{$jobId} for {$job->recipient_email}\n";
        
        // Fetch Settings
        $host = EmailSetting::getVal('smtp_host');
        $port = EmailSetting::getVal('smtp_port');
        $user = EmailSetting::getVal('smtp_user');
        $pass = EmailSetting::getVal('smtp_pass');
        $secure = EmailSetting::getVal('smtp_secure'); // tls, ssl, none
        $fromEmail = EmailSetting::getVal('from_email', 'no-reply@agencia.com');
        $fromName = EmailSetting::getVal('from_name', 'Sistema de Briefing');

        // PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            if (!empty($host)) {
                $mail->isSMTP();
                $mail->Host       = $host;
                $mail->SMTPAuth   = !empty($user);
                $mail->Username   = $user;
                $mail->Password   = $pass;
                if ($secure !== 'none') {
                    $mail->SMTPSecure = $secure === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
                }
                $mail->Port       = $port;
            } else {
                // Se as configurações estiverem vazias, simular envio para ambiente local/desenvolvimento
                echo "No SMTP host configured. Simulating mail send...\n";
                // Optionally route to mailpit/mailtrap if env is defined but no DB settings
                if (!empty($_ENV['MAIL_HOST'])) {
                    $mail->isSMTP();
                    $mail->Host = $_ENV['MAIL_HOST'];
                    $mail->Port = $_ENV['MAIL_PORT'] ?? 1025;
                    $mail->SMTPAuth = false;
                }
            }

            // Recipients
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($job->recipient_email, $job->recipient_name);

            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $job->subject;
            $mail->Body    = $job->body;

            $mail->send();
            
            // Success
            $job->update([
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
                'attempts' => $job->attempts + 1
            ]);
            
            echo "Successfully sent Email Job #{$jobId}\n";
            
        } catch (Exception $e) {
            echo "Failed to send Email Job #{$jobId}. Error: {$mail->ErrorInfo}\n";
            $job->update([
                'status' => 'failed',
                'error_message' => $mail->ErrorInfo,
                'attempts' => $job->attempts + 1
            ]);
        }

    } catch (\Throwable $e) {
        echo "Worker Error: " . $e->getMessage() . "\n";
        sleep(5); // prevent tight loop on DB/Redis disconnect
    }
}
