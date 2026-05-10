<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropInstrumentDescription extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('deskripsi', 'instruments')) {
            $this->forge->dropColumn('instruments', 'deskripsi');
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('deskripsi', 'instruments')) {
            $this->forge->addColumn('instruments', [
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'sasaran',
                ],
            ]);
        }
    }
}
