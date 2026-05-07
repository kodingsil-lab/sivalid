<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstrumentIndicatorsTable extends Migration
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
            'aspect_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'indikator' => [
                'type' => 'TEXT',
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
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
        $this->forge->addForeignKey('instrument_id', 'instruments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('aspect_id', 'instrument_aspects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('instrument_indicators');
    }

    public function down()
    {
        $this->forge->dropTable('instrument_indicators');
    }
}