<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstrumentItemsTable extends Migration
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
            'indicator_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nomor' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'pernyataan' => [
                'type' => 'TEXT',
            ],
            'tipe_butir' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'skala',
            ],
            'wajib' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Aktif',
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
        $this->forge->addForeignKey('indicator_id', 'instrument_indicators', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('instrument_items');
    }

    public function down()
    {
        $this->forge->dropTable('instrument_items');
    }
}