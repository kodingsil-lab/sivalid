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
}
