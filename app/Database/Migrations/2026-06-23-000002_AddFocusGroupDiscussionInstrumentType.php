<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFocusGroupDiscussionInstrumentType extends Migration
{
    public function up()
    {
        $db = db_connect();
        $label = 'Focus Group Discussion';

        $exists = $db->table('settings')
            ->where('setting_group', 'instrument_type')
            ->where('setting_value', $label)
            ->countAllResults();

        if ($exists === 0) {
            $db->table('settings')->insert([
                'setting_key'   => 'instrument_type_official_9',
                'setting_value' => $label,
                'setting_group' => 'instrument_type',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        // Data migration is intentionally not destructive on rollback.
    }
}
