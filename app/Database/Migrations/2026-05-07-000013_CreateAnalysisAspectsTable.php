<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnalysisAspectsTable extends Migration
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
            'analysis_result_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'aspect_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_skor' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'skor_maksimal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'rata_rata' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'default'    => 0,
            ],
            'persentase' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'default'    => 0,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
        $this->forge->addForeignKey('analysis_result_id', 'analysis_results', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('aspect_id', 'instrument_aspects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('analysis_aspects');
    }

    public function down()
    {
        $this->forge->dropTable('analysis_aspects');
    }
}