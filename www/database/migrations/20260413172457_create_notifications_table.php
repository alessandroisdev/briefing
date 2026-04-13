<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateNotificationsTable extends AbstractMigration
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
        $table = $this->table('notifications');
        $table->addColumn('user_id', 'integer', ['default' => null, 'null' => true, 'signed' => false])
              ->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('message', 'text', ['null' => true])
              ->addColumn('type', 'string', ['limit' => 50, 'default' => 'info']) // info, success, warning, error
              ->addColumn('action_url', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('read_at', 'datetime', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->addIndex('user_id')
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
              ->create();
    }
}
