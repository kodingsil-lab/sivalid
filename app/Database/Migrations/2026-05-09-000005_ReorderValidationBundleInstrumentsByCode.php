<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReorderValidationBundleInstrumentsByCode extends Migration
{
    public function up()
    {
        $bundleIds = $this->db->table('validation_bundle_instruments')
            ->select('bundle_id')
            ->groupBy('bundle_id')
            ->get()
            ->getResultArray();

        foreach ($bundleIds as $bundle) {
            $rows = $this->db->table('validation_bundle_instruments')
                ->select('validation_bundle_instruments.id')
                ->join('instruments', 'instruments.id = validation_bundle_instruments.instrument_id')
                ->where('validation_bundle_instruments.bundle_id', (int) $bundle['bundle_id'])
                ->orderBy('instruments.kode', 'ASC')
                ->orderBy('instruments.judul', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($rows as $index => $row) {
                $this->db->table('validation_bundle_instruments')
                    ->where('id', (int) $row['id'])
                    ->update(['urutan' => $index + 1]);
            }
        }
    }

    public function down()
    {
        // Urutan lama tidak dapat dipulihkan setelah dinormalisasi.
    }
}
