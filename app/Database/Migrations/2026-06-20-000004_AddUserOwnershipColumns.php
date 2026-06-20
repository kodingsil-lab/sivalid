<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserOwnershipColumns extends Migration
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
        foreach ($this->tables as $table) {
            if (! $this->db->tableExists($table) || $this->db->fieldExists('user_id', $table)) {
                continue;
            }

            $this->forge->addColumn($table, [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        $ownerId = $this->resolveInitialOwnerId();
        if ($ownerId <= 0) {
            return;
        }

        foreach ($this->tables as $table) {
            if (! $this->db->tableExists($table) || ! $this->db->fieldExists('user_id', $table)) {
                continue;
            }

            $this->db->table($table)
                ->groupStart()
                    ->where('user_id', null)
                    ->orWhere('user_id', 0)
                ->groupEnd()
                ->update(['user_id' => $ownerId]);
        }
    }

    public function down()
    {
        foreach (array_reverse($this->tables) as $table) {
            if (! $this->db->tableExists($table) || ! $this->db->fieldExists('user_id', $table)) {
                continue;
            }

            $this->forge->dropColumn($table, 'user_id');
        }
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
