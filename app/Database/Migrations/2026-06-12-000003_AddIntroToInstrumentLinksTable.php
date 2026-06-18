<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIntroToInstrumentLinksTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('pengantar_penyebaran', 'instrument_links')) {
            $this->forge->addColumn('instrument_links', [
                'pengantar_penyebaran' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'sasaran',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('pengantar_penyebaran', 'instrument_links')) {
            $this->forge->dropColumn('instrument_links', 'pengantar_penyebaran');
        }
    }
}
