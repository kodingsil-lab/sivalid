<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeOfficialInstrumentTypes extends Migration
{
    public function up()
    {
        $db = db_connect();
        $officialTypes = [
            'Panduan Analisis Perangkat Pembelajaran',
            'Pedoman Wawancara',
            'Pedoman Observasi',
            'Angket',
            'Angket Validasi Produk',
            'Angket Respon Pengguna',
            'Tes Unjuk Kerja',
            'Rubrik Penilaian',
        ];
        $legacyDefaults = [
            'Dokumentasi',
            'FGD',
            'Observasi',
            'Tes Kinerja',
            'Wawancara',
        ];

        foreach ($legacyDefaults as $label) {
            $used = $db->table('instruments')->where('jenis', $label)->countAllResults();

            if ($used === 0) {
                $db->table('settings')
                    ->where('setting_group', 'instrument_type')
                    ->where('setting_value', $label)
                    ->delete();
            }
        }

        foreach ($officialTypes as $index => $label) {
            $exists = $db->table('settings')
                ->where('setting_group', 'instrument_type')
                ->where('setting_value', $label)
                ->countAllResults();

            if ($exists === 0) {
                $db->table('settings')->insert([
                    'setting_key'   => 'instrument_type_official_' . ($index + 1),
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
