<?php

/**
 * Returns badge CSS classes for a given status string.
 * Uses Sistem A (sivalid.css badge-status-*) classes.
 */
if (! function_exists('status_badge_class')) {
    function status_badge_class(?string $status): string
    {
        $value = strtolower(trim((string) $status));

        // Success / valid / active
        if (in_array($value, ['valid', 'aktif', 'selesai', 'valid instrumen', 'lulus'], true)) {
            return 'badge badge-status-success';
        }

        // Process / in progress
        if (in_array($value, ['dalam validasi instrumen', 'dalam validasi produk', 'sedang divalidasi', 'proses'], true)) {
            return 'badge badge-status-process';
        }

        // Warning / needs revision
        if (in_array($value, ['perlu revisi', 'direvisi', 'revisi', 'kurang layak'], true)) {
            return 'badge badge-status-warning';
        }

        // Danger / closed / inactive
        if (in_array($value, ['ditutup', 'tidak aktif', 'nonaktif', 'gagal', 'tidak layak'], true)) {
            return 'badge badge-status-danger';
        }

        // Draft / default
        return 'badge badge-status-draft';
    }
}
