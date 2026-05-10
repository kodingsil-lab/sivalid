<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValidationIntroToBundleInstruments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('validation_bundle_instruments', [
            'pengantar_validasi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'urutan',
            ],
            'petunjuk_validasi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'pengantar_validasi',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('validation_bundle_instruments', ['pengantar_validasi', 'petunjuk_validasi']);
    }
}
