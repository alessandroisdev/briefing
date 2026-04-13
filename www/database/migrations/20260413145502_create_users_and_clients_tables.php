<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersAndClientsTables extends AbstractMigration
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
        $users = $this->table('users');
        $users->addColumn('name', 'string', ['limit' => 255])
              ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('phone', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('document', 'string', ['limit' => 100, 'null' => true, 'comment' => 'CPF/CNPJ or etc'])
              ->addColumn('role', 'string', ['limit' => 20, 'default' => 'client'])
              ->addColumn('password', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('magic_link_token', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('magic_link_expires', 'datetime', ['null' => true])
              ->addTimestamps()
              ->addIndex(['email'], ['unique' => true])
              ->addIndex(['phone'], ['unique' => true])
              ->addIndex(['document'], ['unique' => true])
              ->create();

        $clients = $this->table('clients');
        $clients->addColumn('user_id', 'integer', ['signed' => false])
                ->addColumn('company_name', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('address', 'text', ['null' => true])
                ->addColumn('status', 'string', ['limit' => 50, 'default' => 'active'])
                ->addColumn('pending_updates', 'json', ['null' => true])
                ->addTimestamps()
                ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                ->create();
    }
}
