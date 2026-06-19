<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScaleLabelsToInstrumentsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('instruments', [
            'skala_labels' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'skala_max',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('instruments', 'skala_labels');
    }
}
