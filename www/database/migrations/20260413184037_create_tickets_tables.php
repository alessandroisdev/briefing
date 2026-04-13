<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTicketsTables extends AbstractMigration
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
        // TICKETS TABLE
        $tickets = $this->table('tickets');
        $tickets->addColumn('client_id', 'integer', ['signed' => false]) // owner logic
                ->addColumn('subject', 'string', ['limit' => 255])
                ->addColumn('status', 'string', ['limit' => 30, 'default' => 'open'])
                ->addColumn('priority', 'string', ['limit' => 30, 'default' => 'normal'])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('client_id', 'clients', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                ->create();

        // TICKET MESSAGES TABLE
        $messages = $this->table('ticket_messages');
        $messages->addColumn('ticket_id', 'integer', ['signed' => false])
                 ->addColumn('user_id', 'integer', ['signed' => false]) // the sender, whether admin or client
                 ->addColumn('message', 'text')
                 ->addColumn('is_internal', 'boolean', ['default' => false]) // True if it's an admin private whisper
                 ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                 ->addForeignKey('ticket_id', 'tickets', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                 ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                 ->create();

        // TICKET ATTACHMENTS TABLE
        $attachments = $this->table('ticket_attachments');
        $attachments->addColumn('ticket_message_id', 'integer', ['signed' => false])
                    ->addColumn('file_name', 'string', ['limit' => 255])
                    ->addColumn('file_path', 'string', ['limit' => 500])
                    ->addColumn('file_type', 'string', ['limit' => 50, 'null' => true])
                    ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('ticket_message_id', 'ticket_messages', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                    ->create();
    }
}
