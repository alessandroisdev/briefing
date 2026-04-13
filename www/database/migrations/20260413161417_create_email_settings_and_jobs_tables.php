<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmailSettingsAndJobsTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Email Settings Table
        $settings = $this->table('email_settings');
        $settings->addColumn('key', 'string', ['limit' => 100])
                 ->addColumn('value', 'text', ['null' => true])
                 ->addTimestamps()
                 ->addIndex(['key'], ['unique' => true])
                 ->create();

        // Populate default settings rows
        $settings->insert([
            ['key' => 'smtp_host', 'value' => ''],
            ['key' => 'smtp_port', 'value' => '587'],
            ['key' => 'smtp_user', 'value' => ''],
            ['key' => 'smtp_pass', 'value' => ''],
            ['key' => 'smtp_secure', 'value' => 'tls'], // tls, ssl, none
            ['key' => 'from_email', 'value' => 'no-reply@agencia.com'],
            ['key' => 'from_name', 'value' => 'Sistema de Briefing'],
        ])->saveData();

        // Email Jobs Queue Table
        $jobs = $this->table('email_jobs');
        $jobs->addColumn('recipient_email', 'string', ['limit' => 255])
             ->addColumn('recipient_name', 'string', ['limit' => 255, 'null' => true])
             ->addColumn('subject', 'string', ['limit' => 255])
             ->addColumn('body', 'text')
             ->addColumn('status', 'string', ['limit' => 50, 'default' => 'pending']) // pending, sent, failed
             ->addColumn('error_message', 'text', ['null' => true])
             ->addColumn('attempts', 'integer', ['default' => 0])
             ->addColumn('sent_at', 'datetime', ['null' => true])
             ->addColumn('created_at', 'datetime')
             ->addColumn('updated_at', 'datetime', ['null' => true])
             ->addIndex(['status'])
             ->create();
    }
}
