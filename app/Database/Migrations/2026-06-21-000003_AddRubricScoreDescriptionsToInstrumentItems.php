<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRubricScoreDescriptionsToInstrumentItems extends Migration
{
    public function up()
    {
        $fields = [];

        for ($score = 1; $score <= 5; $score++) {
            $column = 'skor_' . $score . '_deskripsi';

            if (! $this->db->fieldExists($column, 'instrument_items')) {
                $fields[$column] = [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => $score === 1 ? 'sumber_dokumen' : 'skor_' . ($score - 1) . '_deskripsi',
                ];
            }
        }

        if ($fields !== []) {
            $this->forge->addColumn('instrument_items', $fields);
        }
    }

    public function down()
    {
        for ($score = 5; $score >= 1; $score--) {
            $column = 'skor_' . $score . '_deskripsi';

            if ($this->db->fieldExists($column, 'instrument_items')) {
                $this->forge->dropColumn('instrument_items', $column);
            }
        }
    }
}
