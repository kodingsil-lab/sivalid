<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSumberDokumenToInstrumentItems extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('sumber_dokumen', 'instrument_items')) {
            $this->forge->addColumn('instrument_items', [
                'sumber_dokumen' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'pernyataan',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('sumber_dokumen', 'instrument_items')) {
            $this->forge->dropColumn('instrument_items', 'sumber_dokumen');
        }
    }
}
