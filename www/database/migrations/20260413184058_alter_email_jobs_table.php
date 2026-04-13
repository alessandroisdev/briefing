<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterEmailJobsTable extends AbstractMigration
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
        $table = $this->table('email_jobs');
        
        // Use text or json depending on DBMS support. In Phinx, 'text' is safer for JSON arrays in SQLite/older MySQL
        $table->addColumn('attachments', 'text', ['null' => true, 'comment' => 'JSON array of file paths'])
              ->update();
    }
}
