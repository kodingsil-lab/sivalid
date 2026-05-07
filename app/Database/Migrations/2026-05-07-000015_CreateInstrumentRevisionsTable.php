<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstrumentRevisionsTable extends Migration
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
            'instrument_item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'analysis_result_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'pernyataan_lama' => [
                'type' => 'TEXT',
            ],
            'pernyataan_baru' => [
                'type' => 'TEXT',
            ],
            'alasan_revisi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sumber_revisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Hasil validasi instrumen',
            ],
            'tanggal_revisi' => [
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
        $this->forge->addForeignKey('instrument_item_id', 'instrument_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('analysis_result_id', 'analysis_results', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('instrument_revisions');
    }

    public function down()
    {
        $this->forge->dropTable('instrument_revisions');
    }
}