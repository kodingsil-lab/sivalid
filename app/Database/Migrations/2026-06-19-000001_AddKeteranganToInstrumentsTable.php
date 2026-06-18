<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganToInstrumentsTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('keterangan', 'instruments')) {
            $this->forge->addColumn('instruments', [
                'keterangan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'sasaran',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('keterangan', 'instruments')) {
            $this->forge->dropColumn('instruments', 'keterangan');
        }
    }
}
