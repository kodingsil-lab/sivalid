<?php

namespace App\Libraries;

class RespondentIdentitySchema
{
    private const TYPES = ['text', 'email', 'number', 'date', 'tel', 'textarea'];

    public static function templates(): array
    {
        return [
            'mahasiswa' => [
                'label' => 'Mahasiswa',
                'fields' => [
                    self::field('nama', 'Nama Lengkap', 'text', true),
                    self::field('nim', 'NIM', 'text', true),
                    self::field('program_studi', 'Program Studi', 'text', true),
                    self::field('semester', 'Semester', 'text', false),
                    self::field('kelas', 'Kelas', 'text', false),
                    self::field('email', 'Email', 'email', false),
                ],
            ],
            'validator' => [
                'label' => 'Validator / Ahli',
                'fields' => [
                    self::field('nama', 'Nama Validator', 'text', true),
                    self::field('instansi', 'Instansi', 'text', false),
                    self::field('bidang_keahlian', 'Bidang Keahlian', 'text', false),
                    self::field('email', 'Email', 'email', false),
                ],
            ],
            'dosen' => [
                'label' => 'Dosen',
                'fields' => [
                    self::field('nama', 'Nama Dosen', 'text', true),
                    self::field('instansi', 'Perguruan Tinggi/Instansi', 'text', false),
                    self::field('program_studi', 'Program Studi/Unit', 'text', false),
                    self::field('bidang_keahlian', 'Jabatan/Bidang Keahlian', 'text', false),
                    self::field('email', 'Email', 'email', false),
                ],
            ],
            'guru' => [
                'label' => 'Guru / Praktisi',
                'fields' => [
                    self::field('nama', 'Nama Lengkap', 'text', true),
                    self::field('instansi', 'Sekolah/Instansi', 'text', false),
                    self::field('bidang_keahlian', 'Jabatan/Mata Pelajaran', 'text', false),
                    self::field('email', 'Email', 'email', false),
                ],
            ],
            'umum' => [
                'label' => 'Umum',
                'fields' => [
                    self::field('nama', 'Nama Lengkap', 'text', true),
                    self::field('instansi', 'Instansi/Unit', 'text', false),
                    self::field('bidang_keahlian', 'Peran/Jabatan', 'text', false),
                    self::field('email', 'Email', 'email', false),
                ],
            ],
            'custom' => [
                'label' => 'Custom',
                'fields' => [
                    self::field('nama', 'Nama Lengkap', 'text', true),
                ],
            ],
        ];
    }

    public static function templateOptions(): array
    {
        return array_map(static fn (array $template): string => $template['label'], self::templates());
    }

    public static function defaultTemplateForLink(array $link): string
    {
        $mode = strtolower((string) ($link['mode'] ?? ''));
        $text = strtolower((string) (($link['sasaran'] ?? '') . ' ' . ($link['judul_link'] ?? '') . ' ' . ($link['judul'] ?? '')));

        if (str_contains($mode, 'validasi') || str_contains($text, 'validator') || str_contains($text, 'ahli')) {
            return 'validator';
        }

        if (str_contains($text, 'dosen')) {
            return 'dosen';
        }

        if (str_contains($text, 'guru')) {
            return 'guru';
        }

        if ($mode === 'respon_mahasiswa' || $mode === 'tes_kinerja' || str_contains($text, 'mahasiswa')) {
            return 'mahasiswa';
        }

        return 'umum';
    }

    public static function fieldsForLink(array $link): array
    {
        $configured = self::decodeFields($link['identity_fields'] ?? null);

        if (!empty($configured)) {
            return self::normalizeFields($configured);
        }

        $templateKey = (string) ($link['identity_template'] ?? '');
        $templates = self::templates();

        if (!isset($templates[$templateKey])) {
            $templateKey = self::defaultTemplateForLink($link);
        }

        return $templates[$templateKey]['fields'];
    }

    public static function fieldsForTemplate(string $templateKey): array
    {
        $templates = self::templates();

        return $templates[$templateKey]['fields'] ?? $templates['umum']['fields'];
    }

    public static function normalizeFields(array $fields): array
    {
        $normalized = [];
        $usedKeys = [];

        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }

            $key = self::normalizeKey((string) ($field['key'] ?? ''));
            $label = trim((string) ($field['label'] ?? ''));
            $type = (string) ($field['type'] ?? 'text');

            if ($key === '' || $label === '') {
                continue;
            }

            if (isset($usedKeys[$key])) {
                continue;
            }

            if (!in_array($type, self::TYPES, true)) {
                $type = 'text';
            }

            $normalized[] = self::field($key, $label, $type, !empty($field['required']));
            $usedKeys[$key] = true;

            if (count($normalized) >= 20) {
                break;
            }
        }

        if (!isset($usedKeys['nama'])) {
            array_unshift($normalized, self::field('nama', 'Nama Lengkap', 'text', true));
        }

        foreach ($normalized as &$field) {
            if ($field['key'] === 'nama') {
                $field['required'] = true;
                break;
            }
        }

        unset($field);

        return $normalized;
    }

    public static function decodeFields($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    public static function knownKeys(): array
    {
        return [
            'nama',
            'email',
            'bidang_keahlian',
            'instansi',
            'nim',
            'program_studi',
            'semester',
            'kelas',
        ];
    }

    private static function field(string $key, string $label, string $type = 'text', bool $required = false): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'type' => in_array($type, self::TYPES, true) ? $type : 'text',
            'required' => $required,
        ];
    }

    private static function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = preg_replace('/[^a-z0-9_]+/', '_', $key) ?? '';
        $key = trim($key, '_');

        return substr($key, 0, 60);
    }
}
