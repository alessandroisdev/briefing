<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddClientNotesToQuotationsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('quotations');
        $table->addColumn('client_notes', 'text', ['null' => true])
              ->update();
    }
}
