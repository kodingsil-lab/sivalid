<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidationBundleInstrumentProgressTable extends Migration
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
            'session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'instrument_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'belum',
            ],
            'kesimpulan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'komentar_umum' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'saved_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey(['session_id', 'instrument_id']);
        $this->forge->addForeignKey('session_id', 'validation_bundle_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_id', 'instruments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('validation_bundle_instrument_progress');
    }

    public function down()
    {
        $this->forge->dropTable('validation_bundle_instrument_progress');
    }
}
