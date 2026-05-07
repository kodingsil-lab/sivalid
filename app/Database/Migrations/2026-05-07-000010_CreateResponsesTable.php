<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResponsesTable extends Migration
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
            'instrument_link_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'respondent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'validasi_instrumen',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Terkirim',
            ],
            'komentar_umum' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kesimpulan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'submitted_at' => [
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
        $this->forge->addForeignKey('instrument_id', 'instruments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_link_id', 'instrument_links', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('respondent_id', 'respondents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('responses');
    }

    public function down()
    {
        $this->forge->dropTable('responses');
    }
}