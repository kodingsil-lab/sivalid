<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateManualValidInstrumentsTable extends Migration
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
            'instrument_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'manual',
            ],
            'source_instrument_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('instrument_id', false, true, 'uq_manual_valid_instrument');
        $this->forge->addForeignKey('instrument_id', 'instruments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('source_instrument_id', 'instruments', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('manual_valid_instruments');
    }

    public function down()
    {
        $this->forge->dropTable('manual_valid_instruments');
    }
}
