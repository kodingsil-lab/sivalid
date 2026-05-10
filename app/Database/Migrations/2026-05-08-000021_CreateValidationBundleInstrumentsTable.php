<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidationBundleInstrumentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bundle_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'instrument_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['bundle_id', 'instrument_id'], false, true, 'uq_bundle_instrument');
        $this->forge->addForeignKey('bundle_id', 'validation_bundles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_id', 'instruments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('validation_bundle_instruments');
    }

    public function down()
    {
        $this->forge->dropTable('validation_bundle_instruments');
    }
}
