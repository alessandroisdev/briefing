<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProjectMessagesAndCredentialsTables extends AbstractMigration
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
        // Alter client_briefings to add agreed_value
        $briefings = $this->table('client_briefings');
        $briefings->addColumn('agreed_value', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true, 'comment' => 'Valor comercial interno acertado para controle'])
                  ->update();

        // client_briefing_messages
        $messages = $this->table('client_briefing_messages');
        $messages->addColumn('briefing_id', 'integer', ['signed' => false])
                 ->addColumn('sender_id', 'integer', ['signed' => false])
                 ->addColumn('message', 'text')
                 ->addColumn('is_internal', 'boolean', ['default' => false, 'comment' => 'Mensagem visível apenas para admins no contexto do projeto'])
                 ->addTimestamps()
                 ->addForeignKey('briefing_id', 'client_briefings', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                 ->addForeignKey('sender_id', 'users', 'id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
                 ->create();

        // message_templates (Canned Responses)
        $msgTemplates = $this->table('message_templates');
        $msgTemplates->addColumn('title', 'string', ['limit' => 255])
                     ->addColumn('body', 'text')
                     ->addTimestamps()
                     ->create();

        // project_credentials
        $credentials = $this->table('project_credentials');
        $credentials->addColumn('briefing_id', 'integer', ['signed' => false])
                    ->addColumn('environment', 'string', ['limit' => 50, 'default' => 'production', 'comment' => 'dev, homologation, production'])
                    ->addColumn('service_name', 'string', ['limit' => 255])
                    ->addColumn('url', 'string', ['limit' => 255, 'null' => true])
                    ->addColumn('username', 'string', ['limit' => 255, 'null' => true])
                    ->addColumn('password', 'string', ['limit' => 255, 'null' => true])
                    ->addColumn('notes', 'text', ['null' => true])
                    ->addTimestamps()
                    ->addForeignKey('briefing_id', 'client_briefings', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                    ->create();
    }
}
