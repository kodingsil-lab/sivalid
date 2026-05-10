<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeInstrumentTypeDefaults extends Migration
{
    public function up()
    {
        $db = db_connect();
        $legacyLabels = [
            'Validasi Instrumen',
            'Validasi Produk',
            'Angket Respon',
        ];

        foreach ($legacyLabels as $label) {
            $used = $db->table('instruments')->where('jenis', $label)->countAllResults();

            if ($used === 0) {
                $db->table('settings')
                    ->where('setting_group', 'instrument_type')
                    ->where('setting_value', $label)
                    ->delete();
            }
        }

        $defaults = [
            'Angket',
            'Wawancara',
            'Observasi',
            'FGD',
            'Tes Kinerja',
            'Rubrik Penilaian',
            'Dokumentasi',
        ];

        foreach ($defaults as $index => $label) {
            $exists = $db->table('settings')
                ->where('setting_group', 'instrument_type')
                ->where('setting_value', $label)
                ->countAllResults();

            if ($exists === 0) {
                $db->table('settings')->insert([
                    'setting_key'   => 'instrument_type_academic_' . ($index + 1),
                    'setting_value' => $label,
                    'setting_group' => 'instrument_type',
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // Data migration is intentionally not destructive on rollback.
    }
}
