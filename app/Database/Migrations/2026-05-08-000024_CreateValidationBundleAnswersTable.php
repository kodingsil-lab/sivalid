<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidationBundleAnswersTable extends Migration
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
            'session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'instrument_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'instrument_item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'skor' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'jawaban_teks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'komentar' => [
                'type' => 'TEXT',
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
        // UNIQUE per session per item — one answer per validator per item
        $this->forge->addUniqueKey(['session_id', 'instrument_item_id']);
        $this->forge->addForeignKey('session_id', 'validation_bundle_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_item_id', 'instrument_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('validation_bundle_answers');
    }

    public function down()
    {
        $this->forge->dropTable('validation_bundle_answers');
    }
}
