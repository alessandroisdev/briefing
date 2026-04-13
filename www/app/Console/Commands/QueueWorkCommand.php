<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\EmailJob;
use App\Models\EmailSetting;
use App\Core\RedisManager;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:work', description: 'Inicia o processamento contínuo da fila (Daemon) de e-mails via Redis.')]
class QueueWorkCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('Este comando trava o terminal escutando a fila email_queue do Redis nativamente com blpop. Ideal para rodar como Daemon/Supervisor/Docker-Compose restart:unless-stopped.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>NorteDev Queue Worker iniciado.</info>');
        $output->writeln('<comment>Aguardando trabalhos de e-mail na fila...</comment>');

        $redis = RedisManager::getClient();

        // [CRASH RECOVERY] Re-enfileirar jobs 'pending' órfãos no banco (que o worker puxou do redis mas crashou antes de finalizar)
        $output->writeln('<comment>Limpando e sincronizando banco de dados...</comment>');
        $lostJobs = EmailJob::where('status', 'pending')->get();
        if(!empty($lostJobs)) {
            // Remove queue anterior para não duplicar, e recria alinhada ao Banco de origem
            $redis->del('email_queue');
            foreach($lostJobs as $ljob) {
                $redis->rpush('email_queue', $ljob->id);
            }
            $count = count($lostJobs);
            $output->writeln("<info>Recuperados {$count} Jobs pendentes e recolocados na fila veloz.</info>");
        }

        while (true) {
            try {
                // Blocks until an item is available in the 'email_queue', timeout 10s to prevent PHP socket exhaustion
                $result = $redis->blpop(['email_queue'], 10);
                
                if (empty($result)) {
                    continue;
                }

                $jobId = $result[1];
                
                $job = EmailJob::find($jobId);
                
                if (!$job || $job->status === 'sent') {
                    continue;
                }

                $output->writeln("Processando Job <info>#{$jobId}</info> para <comment>{$job->recipient_email}</comment>");
                
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
                $mail->Timeout = 15; // Set connection timeout explicitly
                
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
                        if (!empty($_ENV['MAIL_HOST'])) {
                            $mail->isSMTP();
                            $mail->Host = $_ENV['MAIL_HOST'];
                            $mail->Port = $_ENV['MAIL_PORT'] ?? 1025;
                            $mail->SMTPAuth = false;
                        } else {
                            throw new Exception("Configuração Ausente: Nenhum Servidor SMTP configurado no banco de dados e nem no .env");
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
                    
                    RedisManager::publish('notifications_channel', [
                        'event' => 'queue_updated',
                        'job_id' => $jobId,
                        'status' => 'sent'
                    ]);

                    $output->writeln("<info>Successfully sent Job #{$jobId}</info>\n");
                    
                } catch (Exception $e) {
                    $output->writeln("<error>Failed to send Job #{$jobId}. Error: {$mail->ErrorInfo}</error>\n");
                    $job->update([
                        'status' => 'failed',
                        'error_message' => $mail->ErrorInfo,
                        'attempts' => $job->attempts + 1
                    ]);

                    RedisManager::publish('notifications_channel', [
                        'event' => 'queue_updated',
                        'job_id' => $jobId,
                        'status' => 'failed'
                    ]);
                }

            } catch (\Throwable $e) {
                $output->writeln("<error>Worker Error: " . $e->getMessage() . "</error>");
                sleep(5); // prevent tight loop on DB/Redis disconnect
            }
        }

        return Command::SUCCESS;
    }
}
