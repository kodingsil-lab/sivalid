<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveValidationBundleTokenAttemptLimits extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('validation_bundle_token_attempts')) {
            $this->forge->dropTable('validation_bundle_token_attempts', true);
        }

        $columns = [];

        if ($this->db->fieldExists('token_max_attempts', 'validation_bundles')) {
            $columns[] = 'token_max_attempts';
        }

        if ($this->db->fieldExists('token_block_minutes', 'validation_bundles')) {
            $columns[] = 'token_block_minutes';
        }

        if ($columns !== []) {
            $this->forge->dropColumn('validation_bundles', $columns);
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('token_max_attempts', 'validation_bundles')) {
            $this->forge->addColumn('validation_bundles', [
                'token_max_attempts' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 5,
                    'after'      => 'token_revoked_at',
                ],
            ]);
        }

        if (! $this->db->fieldExists('token_block_minutes', 'validation_bundles')) {
            $this->forge->addColumn('validation_bundles', [
                'token_block_minutes' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 30,
                    'after'      => 'token_max_attempts',
                ],
            ]);
        }
    }
}
