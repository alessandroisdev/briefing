<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateQuotationsSystemTables extends AbstractMigration
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
        // quotation_templates
        $qTemplates = $this->table('quotation_templates');
        $qTemplates->addColumn('title', 'string', ['limit' => 255])
                   ->addColumn('description', 'text', ['null' => true])
                   ->addColumn('base_items_json', 'json', ['comment' => 'Array of base items with default pricing'])
                   ->addColumn('is_active', 'boolean', ['default' => true])
                   ->addTimestamps()
                   ->create();

        // quotations
        $quotations = $this->table('quotations');
        $quotations->addColumn('client_id', 'integer', ['signed' => false])
                   ->addColumn('briefing_id', 'integer', ['signed' => false, 'null' => true])
                   ->addColumn('title', 'string', ['limit' => 255])
                   ->addColumn('total_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0])
                   ->addColumn('status', 'string', ['limit' => 50, 'default' => 'draft', 'comment' => 'draft, sent, accepted, rejected'])
                   ->addColumn('valid_until', 'datetime', ['null' => true])
                   ->addColumn('pdf_url', 'string', ['limit' => 255, 'null' => true])
                   ->addTimestamps()
                   ->addForeignKey('client_id', 'clients', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                   ->addForeignKey('briefing_id', 'client_briefings', 'id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                   ->create();

        // quotation_items
        $quotationItems = $this->table('quotation_items');
        $quotationItems->addColumn('quotation_id', 'integer', ['signed' => false])
                       ->addColumn('description', 'string', ['limit' => 255])
                       ->addColumn('quantity', 'integer', ['default' => 1])
                       ->addColumn('unit_price', 'decimal', ['precision' => 10, 'scale' => 2])
                       ->addColumn('total', 'decimal', ['precision' => 10, 'scale' => 2])
                       ->addTimestamps()
                       ->addForeignKey('quotation_id', 'quotations', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                       ->create();
    }
}
