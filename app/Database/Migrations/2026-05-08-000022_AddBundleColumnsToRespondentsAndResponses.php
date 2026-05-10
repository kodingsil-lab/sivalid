<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBundleColumnsToRespondentsAndResponses extends Migration
{
    public function up()
    {
        // Make instrument_link_id nullable in respondents so bundle submissions don't need it
        $this->forge->modifyColumn('respondents', [
            'instrument_link_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // Add bundle_id to respondents
        $this->forge->addColumn('respondents', [
            'bundle_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'instrument_link_id',
            ],
        ]);

        // Make instrument_link_id nullable in responses so bundle submissions don't need it
        $this->forge->modifyColumn('responses', [
            'instrument_link_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // Add bundle_id to responses
        $this->forge->addColumn('responses', [
            'bundle_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'instrument_link_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('respondents', 'bundle_id');
        $this->forge->dropColumn('responses', 'bundle_id');

        // Restore NOT NULL (may fail if null rows exist — acceptable for rollback purposes)
        $this->forge->modifyColumn('respondents', [
            'instrument_link_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        $this->forge->modifyColumn('responses', [
            'instrument_link_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
