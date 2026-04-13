<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBriefingsAndTemplatesTables extends AbstractMigration
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
        // Briefing Templates (Admins create these)
        $templates = $this->table('briefing_templates');
        $templates->addColumn('title', 'string', ['limit' => 255])
                  ->addColumn('description', 'text', ['null' => true])
                  ->addColumn('form_schema', 'json', ['comment' => 'JSON defines the dynamic fields for this briefing'])
                  ->addColumn('status', 'string', ['limit' => 50, 'default' => 'active'])
                  ->addTimestamps()
                  ->create();

        // Client Briefings (Instances of templates assigned to clients)
        $briefings = $this->table('client_briefings');
        $briefings->addColumn('client_id', 'integer', ['signed' => false])
                  ->addColumn('template_id', 'integer', ['signed' => false])
                  ->addColumn('title', 'string', ['limit' => 255, 'null' => true, 'comment' => 'Custom title for this specific project/briefing'])
                  ->addColumn('status', 'string', ['limit' => 50, 'default' => 'criado', 'comment' => 'criado, editando, executando, cancelado'])
                  ->addColumn('form_data', 'json', ['null' => true, 'comment' => 'Client answers saved in JSON'])
                  ->addColumn('comments', 'text', ['null' => true, 'comment' => 'Optional rich text comments/notes over the briefing'])
                  ->addTimestamps()
                  ->addForeignKey('client_id', 'clients', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                  ->addForeignKey('template_id', 'briefing_templates', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION'])
                  ->create();
    }
}
