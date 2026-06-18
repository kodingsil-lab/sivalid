<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJustificationConfigToLinksAndResponses extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('justification_config', 'instrument_links')) {
            $this->forge->addColumn('instrument_links', [
                'justification_config' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'identity_fields',
                ],
            ]);
        }

        if (! $this->db->fieldExists('justification_data', 'responses')) {
            $this->forge->addColumn('responses', [
                'justification_data' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'kesimpulan',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('justification_data', 'responses')) {
            $this->forge->dropColumn('responses', 'justification_data');
        }

        if ($this->db->fieldExists('justification_config', 'instrument_links')) {
            $this->forge->dropColumn('instrument_links', 'justification_config');
        }
    }
}
