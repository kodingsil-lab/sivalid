<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsureInitialSuperadmin extends Migration
{
    public function up()
    {
        $db = db_connect();

        $hasSuperadmin = $db->table('users')
            ->where('role', 'superadmin')
            ->countAllResults() > 0;

        if ($hasSuperadmin) {
            return;
        }

        $firstUser = $db->table('users')
            ->select('id')
            ->orderBy('id', 'ASC')
            ->get(1)
            ->getRowArray();

        if (! $firstUser) {
            return;
        }

        $db->table('users')
            ->where('id', (int) $firstUser['id'])
            ->update([
                'role' => 'superadmin',
                'status' => 'aktif',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down()
    {
        // Role user tidak dikembalikan otomatis agar tidak menurunkan akses admin secara tidak sengaja.
    }
}
