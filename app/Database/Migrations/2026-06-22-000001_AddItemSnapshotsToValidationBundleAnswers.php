<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddItemSnapshotsToValidationBundleAnswers extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('snapshot_nomor', 'validation_bundle_answers')) {
            $fields['snapshot_nomor'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'instrument_item_id',
            ];
        }

        if (! $this->db->fieldExists('snapshot_aspek', 'validation_bundle_answers')) {
            $fields['snapshot_aspek'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'snapshot_nomor',
            ];
        }

        if (! $this->db->fieldExists('snapshot_pernyataan', 'validation_bundle_answers')) {
            $fields['snapshot_pernyataan'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'snapshot_aspek',
            ];
        }

        if (! $this->db->fieldExists('snapshot_tipe_butir', 'validation_bundle_answers')) {
            $fields['snapshot_tipe_butir'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'snapshot_pernyataan',
            ];
        }

        if (! $this->db->fieldExists('snapshot_sumber_dokumen', 'validation_bundle_answers')) {
            $fields['snapshot_sumber_dokumen'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'snapshot_tipe_butir',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('validation_bundle_answers', $fields);
        }

        $this->db->query(
            "UPDATE validation_bundle_answers vba
             JOIN instrument_items ii ON ii.id = vba.instrument_item_id
             LEFT JOIN instrument_aspects ia ON ia.id = ii.aspect_id
             SET
                vba.snapshot_nomor = COALESCE(vba.snapshot_nomor, ii.nomor),
                vba.snapshot_aspek = COALESCE(vba.snapshot_aspek, ia.nama_aspek, '-'),
                vba.snapshot_pernyataan = COALESCE(vba.snapshot_pernyataan, ii.pernyataan),
                vba.snapshot_tipe_butir = COALESCE(vba.snapshot_tipe_butir, ii.tipe_butir),
                vba.snapshot_sumber_dokumen = COALESCE(vba.snapshot_sumber_dokumen, ii.sumber_dokumen)"
        );
    }

    public function down()
    {
        $columns = [];

        foreach ([
            'snapshot_nomor',
            'snapshot_aspek',
            'snapshot_pernyataan',
            'snapshot_tipe_butir',
            'snapshot_sumber_dokumen',
        ] as $column) {
            if ($this->db->fieldExists($column, 'validation_bundle_answers')) {
                $columns[] = $column;
            }
        }

        if ($columns !== []) {
            $this->forge->dropColumn('validation_bundle_answers', $columns);
        }
    }
}
