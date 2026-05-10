<?php

/**
 * Returns Tabler badge classes for a given status string.
 */
if (! function_exists('status_badge_class')) {
    function status_badge_class(?string $status): string
    {
        $value = strtolower(trim((string) $status));

        // Success / valid / active / no action needed
        if (in_array($value, [
            'valid',
            'selesai',
            'valid instrumen',
            'lulus',
            'sangat valid',
            'layak',
            'sangat layak',
            'siap disebar',
            'tetap',
            'ada file',
        ], true)) {
            return 'badge bg-green text-green-fg';
        }

        // Process / in progress
        if (in_array($value, [
            'aktif',
            'dalam validasi instrumen',
            'dalam validasi produk',
            'direvisi',
            'layak ditetapkan valid',
            'sedang divalidasi',
            'proses',
            'diproses',
            'menunggu respon',
            'terbuka',
        ], true)) {
            return 'badge bg-blue text-blue-fg';
        }

        // Warning / needs revision
        if (in_array($value, [
            'perlu revisi',
            'revisi',
            'revisi kecil',
            'revisi besar',
            'cukup valid',
            'kurang valid',
            'kurang layak',
            'belum dianalisis',
            'belum tersedia',
        ], true)) {
            return 'badge bg-orange text-orange-fg';
        }

        // Danger / closed / inactive
        if (in_array($value, [
            'ditutup',
            'tidak aktif',
            'nonaktif',
            'gagal',
            'tidak valid',
            'tidak layak',
            'ganti atau hapus',
        ], true)) {
            return 'badge bg-red text-red-fg';
        }

        // Draft / default
        return 'badge bg-secondary text-secondary-fg';
    }
}

if (! function_exists('status_display_label')) {
    function status_display_label(?string $status): string
    {
        $value = trim((string) $status);

        if ($value === '') {
            return '-';
        }

        $map = [
            'Draft' => 'Disiapkan',
            'Aktif' => 'Siap Divalidasi',
            'Dalam Validasi Instrumen' => 'Sedang Divalidasi',
            'Perlu Revisi' => 'Perlu Revisi',
            'Direvisi' => 'Sudah Direvisi',
            'Layak Ditetapkan Valid' => 'Siap Ditetapkan Valid',
            'Valid' => 'Valid',
            'Siap Disebar' => 'Valid dan Siap Disebar',
            'Tidak Aktif' => 'Tidak Aktif',
            'Arsip' => 'Diarsipkan',
        ];

        return $map[$value] ?? $value;
    }
}

if (! function_exists('title_case_label')) {
    function title_case_label(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '-';
        }

        $value = str_replace('_', ' ', $value);

        return preg_replace_callback('/[A-Za-z]+(?:-[A-Za-z]+)*/', static function (array $matches): string {
            $word = $matches[0];

            if (strlen($word) > 1 && strtoupper($word) === $word) {
                return $word;
            }

            return implode('-', array_map(static function (string $part): string {
                if (strlen($part) > 1 && strtoupper($part) === $part) {
                    return $part;
                }

                return ucfirst(strtolower($part));
            }, explode('-', $word)));
        }, strtolower($value)) ?? $value;
    }
}
