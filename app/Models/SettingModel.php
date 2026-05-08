<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_group',
    ];

    protected $useTimestamps = true;

    public function getValue(string $key, ?string $default = null): ?string
    {
        $row = $this->where('setting_key', $key)->first();

        return $row['setting_value'] ?? $default;
    }

    public function setValue(string $key, ?string $value, string $group = 'general'): void
    {
        $row = $this->where('setting_key', $key)->first();

        if ($row) {
            $this->update((int) $row['id'], [
                'setting_value' => $value,
                'setting_group' => $group,
            ]);

            return;
        }

        $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
            'setting_group' => $group,
        ]);
    }

    public function getGroupValues(string $group): array
    {
        $rows = $this->where('setting_group', $group)->findAll();

        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    public function getGroupRows(string $group): array
    {
        return $this->where('setting_group', $group)
            ->orderBy('setting_value', 'ASC')
            ->findAll();
    }

    public function getInstrumentTypes(array $fallback = []): array
    {
        $rows = $this->getGroupRows('instrument_type');
        $types = [];

        foreach ($rows as $row) {
            $value = trim((string) ($row['setting_value'] ?? ''));
            if ($value !== '') {
                $types[] = $value;
            }
        }

        if ($types === []) {
            $types = $fallback;
        }

        $types = array_values(array_unique(array_filter(array_map(static fn ($item) => trim((string) $item), $types))));
        sort($types, SORT_NATURAL | SORT_FLAG_CASE);

        return $types;
    }

    public function getProductTypes(array $fallback = []): array
    {
        $rows = $this->getGroupRows('product_type');
        $types = [];

        foreach ($rows as $row) {
            $value = trim((string) ($row['setting_value'] ?? ''));
            if ($value !== '') {
                $types[] = $value;
            }
        }

        if ($types === []) {
            $types = $fallback;
        }

        $types = array_values(array_unique(array_filter(array_map(static fn ($item) => trim((string) $item), $types))));
        sort($types, SORT_NATURAL | SORT_FLAG_CASE);

        return $types;
    }
}
