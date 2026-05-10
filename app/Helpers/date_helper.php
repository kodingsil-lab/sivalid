<?php

if (! function_exists('format_tanggal_indonesia')) {
    function format_tanggal_indonesia($date, bool $withTime = false, string $empty = '-'): string
    {
        if ($date === null || $date === '') {
            return $empty;
        }

        $timestamp = is_numeric($date) ? (int) $date : strtotime((string) $date);

        if ($timestamp === false) {
            return $empty;
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $formatted = date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);

        if ($withTime) {
            $formatted .= ' ' . date('H:i', $timestamp);
        }

        return $formatted;
    }
}

