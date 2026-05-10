<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidationBundleTokenAttemptsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bundle_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],
            'failed_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'last_failed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'blocked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['bundle_id', 'ip_address']);
        $this->forge->addKey('blocked_until');
        $this->forge->addForeignKey('bundle_id', 'validation_bundles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('validation_bundle_token_attempts');
    }

    public function down()
    {
        $this->forge->dropTable('validation_bundle_token_attempts');
    }
}
