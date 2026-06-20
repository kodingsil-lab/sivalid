<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UseUserScopedInstrumentCodes extends Migration
{
    public function up()
    {
        $this->dropSingleKodeUniqueIndexes();

        if (! $this->indexExists('idx_instruments_user_kode_unique')) {
            $this->db->query(
                'CREATE UNIQUE INDEX `idx_instruments_user_kode_unique` ON `'
                . $this->db->DBPrefix
                . 'instruments` (`user_id`, `kode`)'
            );
        }
    }

    public function down()
    {
        if ($this->indexExists('idx_instruments_user_kode_unique')) {
            $this->db->query('DROP INDEX `idx_instruments_user_kode_unique` ON `' . $this->db->DBPrefix . 'instruments`');
        }

        if (! $this->hasSingleKodeUniqueIndex()) {
            $this->db->query('CREATE UNIQUE INDEX `kode` ON `' . $this->db->DBPrefix . 'instruments` (`kode`)');
        }
    }

    private function dropSingleKodeUniqueIndexes(): void
    {
        foreach ($this->getIndexesByName() as $name => $index) {
            if ($name === 'PRIMARY') {
                continue;
            }

            if ((bool) $index['unique'] && $index['columns'] === ['kode']) {
                $this->db->query('DROP INDEX `' . str_replace('`', '``', $name) . '` ON `' . $this->db->DBPrefix . 'instruments`');
            }
        }
    }

    private function hasSingleKodeUniqueIndex(): bool
    {
        foreach ($this->getIndexesByName() as $name => $index) {
            if ($name !== 'PRIMARY' && (bool) $index['unique'] && $index['columns'] === ['kode']) {
                return true;
            }
        }

        return false;
    }

    private function indexExists(string $indexName): bool
    {
        return array_key_exists($indexName, $this->getIndexesByName());
    }

    private function getIndexesByName(): array
    {
        $rows = $this->db
            ->query('SHOW INDEX FROM `' . $this->db->DBPrefix . 'instruments`')
            ->getResultArray();

        $indexes = [];

        foreach ($rows as $row) {
            $name = (string) $row['Key_name'];

            if (! isset($indexes[$name])) {
                $indexes[$name] = [
                    'unique' => (int) $row['Non_unique'] === 0,
                    'columns' => [],
                ];
            }

            $indexes[$name]['columns'][(int) $row['Seq_in_index']] = (string) $row['Column_name'];
        }

        foreach ($indexes as &$index) {
            ksort($index['columns']);
            $index['columns'] = array_values($index['columns']);
        }
        unset($index);

        return $indexes;
    }
}
