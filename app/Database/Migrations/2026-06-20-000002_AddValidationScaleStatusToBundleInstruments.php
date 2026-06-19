<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValidationScaleStatusToBundleInstruments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('validation_bundle_instruments', [
            'skala_min' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'petunjuk_validasi',
            ],
            'skala_max' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'skala_min',
            ],
            'skala_labels' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'skala_max',
            ],
            'status_validasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Siap Divalidasi',
                'after'      => 'skala_labels',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('validation_bundle_instruments', [
            'skala_min',
            'skala_max',
            'skala_labels',
            'status_validasi',
        ]);
    }
}
