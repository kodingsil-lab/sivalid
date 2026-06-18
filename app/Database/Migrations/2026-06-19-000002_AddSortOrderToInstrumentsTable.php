<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSortOrderToInstrumentsTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('sort_order', 'instruments')) {
            $this->forge->addColumn('instruments', [
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => false,
                    'default'    => 0,
                    'after'      => 'keterangan',
                ],
            ]);
        }

        $rows = $this->db->table('instruments')
            ->select('id')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as $index => $row) {
            $this->db->table('instruments')
                ->where('id', (int) $row['id'])
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('sort_order', 'instruments')) {
            $this->forge->dropColumn('instruments', 'sort_order');
        }
    }
}
