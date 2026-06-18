<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropScaleLabelsFromInstrumentLinksTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('keterangan_skala_penyebaran', 'instrument_links')) {
            $this->forge->dropColumn('instrument_links', 'keterangan_skala_penyebaran');
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('keterangan_skala_penyebaran', 'instrument_links')) {
            $this->forge->addColumn('instrument_links', [
                'keterangan_skala_penyebaran' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'petunjuk_penyebaran',
                ],
            ]);
        }
    }
}
