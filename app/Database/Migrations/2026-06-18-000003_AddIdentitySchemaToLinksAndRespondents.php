<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdentitySchemaToLinksAndRespondents extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('identity_template', 'instrument_links')) {
            $this->forge->addColumn('instrument_links', [
                'identity_template' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'sasaran',
                ],
            ]);
        }

        if (! $this->db->fieldExists('identity_fields', 'instrument_links')) {
            $this->forge->addColumn('instrument_links', [
                'identity_fields' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'identity_template',
                ],
            ]);
        }

        if (! $this->db->fieldExists('identity_data', 'respondents')) {
            $this->forge->addColumn('respondents', [
                'identity_data' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'kelas',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('identity_data', 'respondents')) {
            $this->forge->dropColumn('respondents', 'identity_data');
        }

        if ($this->db->fieldExists('identity_fields', 'instrument_links')) {
            $this->forge->dropColumn('instrument_links', 'identity_fields');
        }

        if ($this->db->fieldExists('identity_template', 'instrument_links')) {
            $this->forge->dropColumn('instrument_links', 'identity_template');
        }
    }
}
