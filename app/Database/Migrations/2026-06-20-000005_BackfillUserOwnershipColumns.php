<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillUserOwnershipColumns extends Migration
{
    private array $tables = [
        'instruments',
        'research_products',
        'instrument_links',
        'validation_bundles',
        'manual_valid_instruments',
        'respondents',
        'responses',
        'instrument_aspects',
        'instrument_indicators',
        'instrument_items',
    ];

    public function up()
    {
        $ownerId = $this->resolveInitialOwnerId();
        if ($ownerId <= 0) {
            return;
        }

        foreach ($this->tables as $table) {
            if (! $this->db->tableExists($table) || ! $this->db->fieldExists('user_id', $table)) {
                continue;
            }

            $this->db->query(
                sprintf('UPDATE `%s` SET `user_id` = ? WHERE `user_id` IS NULL OR `user_id` = 0', $table),
                [$ownerId]
            );
        }
    }

    public function down()
    {
        // Tidak dikosongkan kembali agar ownership data yang sudah benar tetap aman.
    }

    private function resolveInitialOwnerId(): int
    {
        $superadmin = $this->db->table('users')
            ->select('id')
            ->where('role', 'superadmin')
            ->where('status', 'aktif')
            ->orderBy('id', 'ASC')
            ->get(1)
            ->getRowArray();

        if ($superadmin) {
            return (int) $superadmin['id'];
        }

        $firstUser = $this->db->table('users')
            ->select('id')
            ->orderBy('id', 'ASC')
            ->get(1)
            ->getRowArray();

        return $firstUser ? (int) $firstUser['id'] : 0;
    }
}
