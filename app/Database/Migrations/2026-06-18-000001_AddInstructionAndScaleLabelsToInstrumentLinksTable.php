<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstructionAndScaleLabelsToInstrumentLinksTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('petunjuk_penyebaran', 'instrument_links')) {
            $this->forge->addColumn('instrument_links', [
                'petunjuk_penyebaran' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'pengantar_penyebaran',
                ],
            ]);
        }

    }

    public function down()
    {
        if ($this->db->fieldExists('petunjuk_penyebaran', 'instrument_links')) {
            $this->forge->dropColumn('instrument_links', 'petunjuk_penyebaran');
        }
    }
}
