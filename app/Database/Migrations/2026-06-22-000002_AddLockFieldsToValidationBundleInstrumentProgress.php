<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLockFieldsToValidationBundleInstrumentProgress extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('completed_at', 'validation_bundle_instrument_progress')) {
            $fields['completed_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'saved_at',
            ];
        }

        if (! $this->db->fieldExists('locked_at', 'validation_bundle_instrument_progress')) {
            $fields['locked_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'completed_at',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('validation_bundle_instrument_progress', $fields);
        }

        $this->db->query(
            "UPDATE validation_bundle_instrument_progress
             SET completed_at = COALESCE(completed_at, saved_at),
                 locked_at = COALESCE(locked_at, saved_at)
             WHERE status = 'selesai'"
        );
    }

    public function down()
    {
        $columns = [];

        foreach (['locked_at', 'completed_at'] as $column) {
            if ($this->db->fieldExists($column, 'validation_bundle_instrument_progress')) {
                $columns[] = $column;
            }
        }

        if ($columns !== []) {
            $this->forge->dropColumn('validation_bundle_instrument_progress', $columns);
        }
    }
}
