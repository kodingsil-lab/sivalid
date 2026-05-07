<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeProductStatusLegacy extends Migration
{
    /**
     * Peta status lama ke status baru.
     */
    private array $statusMap = [
        'Draft Produk'     => 'Draft',
        'Siap Divalidasi'  => 'Aktif',
        'Sedang Divalidasi' => 'Dalam Validasi Produk',
    ];

    public function up()
    {
        foreach ($this->statusMap as $oldStatus => $newStatus) {
            $this->db->table('research_products')
                ->where('status', $oldStatus)
                ->update(['status' => $newStatus]);
        }
    }

    public function down()
    {
        // Kembalikan status baru ke status lama (reverse map)
        foreach (array_flip($this->statusMap) as $newStatus => $oldStatus) {
            $this->db->table('research_products')
                ->where('status', $newStatus)
                ->update(['status' => $oldStatus]);
        }
    }
}
