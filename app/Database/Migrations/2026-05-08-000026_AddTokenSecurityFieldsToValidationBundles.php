<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTokenSecurityFieldsToValidationBundles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('validation_bundles', [
            'token_access_mode' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'single_use',
                'after'      => 'token',
            ],
            'token_expires_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'tanggal_selesai',
            ],
            'token_revoked_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'token_expires_at',
            ],
            'token_max_attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 5,
                'after'      => 'token_revoked_at',
            ],
            'token_block_minutes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 30,
                'after'      => 'token_max_attempts',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('validation_bundles', [
            'token_access_mode',
            'token_expires_at',
            'token_revoked_at',
            'token_max_attempts',
            'token_block_minutes',
        ]);
    }
}
