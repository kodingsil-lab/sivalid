<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstrumentsTable extends Migration
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
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'sasaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pengantar' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'petunjuk' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'skala_min' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
            ],
            'skala_max' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 4,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Draft',
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
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('instruments');
    }

    public function down()
    {
        $this->forge->dropTable('instruments');
    }
}