<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResponseAnswersTable extends Migration
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
            'response_id' => [
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
                'constraint' => 2,
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
        $this->forge->addForeignKey('response_id', 'responses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_item_id', 'instrument_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('response_answers');
    }

    public function down()
    {
        $this->forge->dropTable('response_answers');
    }
}