<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropInstrumentRevisionsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('instrument_revisions')) {
            $this->forge->dropTable('instrument_revisions');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('instrument_revisions')) {
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
                    'null' => true,
                ],
                'pernyataan_baru' => [
                    'type' => 'TEXT',
                ],
                'alasan_revisi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'tanggal_revisi' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'Selesai',
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
    }
}
