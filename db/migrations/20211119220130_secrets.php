<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Secrets extends AbstractMigration {

    public function change() {
        // create the table
        $table = $this->table( 'secrets', [ 'id' => false, 'primary_key' => [ 'keyid', 'fingerprint' ] ] );
        $table->addColumn('keyid',       'string', ['limit' => 64])
              ->addColumn('fingerprint', 'string', ['limit' => 64])
              ->addColumn('time',        'timestamp')
              ->create();
    }
}
