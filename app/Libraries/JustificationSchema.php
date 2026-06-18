<?php

namespace App\Libraries;

class JustificationSchema
{
    public static function templates(): array
    {
        return [
            'validasi_instrumen' => [
                'label' => 'Validasi Instrumen',
                'comment_label' => 'Komentar/Saran Umum Validator',
                'comment_placeholder' => 'Tuliskan komentar umum terhadap instrumen.',
                'comment_required' => false,
                'conclusion_label' => 'Kesimpulan Validasi',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Layak digunakan tanpa revisi',
                    'Layak digunakan dengan revisi kecil',
                    'Perlu revisi besar sebelum digunakan',
                    'Tidak layak digunakan',
                ],
            ],
            'validasi_produk' => [
                'label' => 'Validasi Produk',
                'comment_label' => 'Komentar/Saran Umum',
                'comment_placeholder' => 'Tuliskan komentar atau saran umum terhadap produk.',
                'comment_required' => false,
                'conclusion_label' => 'Kesimpulan Validasi Produk',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Sangat Layak',
                    'Layak',
                    'Kurang Layak',
                    'Tidak Layak',
                ],
            ],
            'responden' => [
                'label' => 'Responden / Angket',
                'comment_label' => 'Komentar, Saran, dan Catatan Akhir',
                'comment_placeholder' => 'Tuliskan komentar, saran, atau catatan akhir.',
                'comment_required' => false,
                'conclusion_label' => 'Penilaian Umum',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Sangat Baik',
                    'Baik',
                    'Cukup',
                    'Kurang',
                ],
            ],
            'observasi' => [
                'label' => 'Observasi',
                'comment_label' => 'Catatan Observasi',
                'comment_placeholder' => 'Tuliskan ringkasan temuan observasi, kejadian penting, atau catatan pelaksanaan.',
                'comment_required' => false,
                'conclusion_label' => 'Kesimpulan Observasi',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Sangat Baik',
                    'Baik',
                    'Cukup',
                    'Kurang',
                ],
            ],
            'fgd' => [
                'label' => 'FGD',
                'comment_label' => 'Rekomendasi Umum FGD',
                'comment_placeholder' => 'Tuliskan kesimpulan umum, masukan utama, atau rekomendasi FGD.',
                'comment_required' => false,
                'conclusion_label' => 'Kesimpulan FGD',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Sangat Direkomendasikan',
                    'Direkomendasikan',
                    'Direkomendasikan dengan Perbaikan',
                    'Tidak Direkomendasikan',
                ],
            ],
            'tes_kinerja' => [
                'label' => 'Tes/Rubrik Kinerja',
                'comment_label' => 'Catatan Penilai',
                'comment_placeholder' => 'Tuliskan catatan umum, kekuatan, kelemahan, atau rekomendasi tindak lanjut.',
                'comment_required' => false,
                'conclusion_label' => 'Kesimpulan Penilaian Kinerja',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Sangat Kompeten',
                    'Kompeten',
                    'Cukup Kompeten',
                    'Belum Kompeten',
                ],
            ],
            'custom' => [
                'label' => 'Custom',
                'comment_label' => 'Komentar/Saran',
                'comment_placeholder' => 'Tuliskan komentar atau saran.',
                'comment_required' => false,
                'conclusion_label' => 'Kesimpulan',
                'conclusion_required' => true,
                'conclusion_options' => [
                    'Sangat Layak',
                    'Layak',
                    'Kurang Layak',
                    'Tidak Layak',
                ],
            ],
        ];
    }

    public static function defaultTemplateForLink(array $link): string
    {
        $mode = (string) ($link['mode'] ?? '');

        if ($mode === 'validasi_instrumen' || $mode === 'validasi_produk' || $mode === 'observasi' || $mode === 'fgd' || $mode === 'tes_kinerja') {
            return $mode;
        }

        return 'responden';
    }

    public static function configForLink(array $link): array
    {
        $configured = self::decodeConfig($link['justification_config'] ?? null);

        if (!empty($configured)) {
            return self::normalizeConfig($configured);
        }

        $templateKey = self::defaultTemplateForLink($link);
        $templates = self::templates();

        return $templates[$templateKey] ?? $templates['custom'];
    }

    public static function configForTemplate(string $templateKey): array
    {
        $templates = self::templates();

        return $templates[$templateKey] ?? $templates['custom'];
    }

    public static function normalizeConfig(array $config): array
    {
        $options = $config['conclusion_options'] ?? [];

        if (is_string($options)) {
            $options = preg_split('/\r\n|\r|\n/', $options) ?: [];
        }

        $options = array_values(array_filter(array_map(static function ($option): string {
            return trim((string) $option);
        }, is_array($options) ? $options : []), static fn (string $option): bool => $option !== ''));

        if (empty($options)) {
            $options = self::templates()['custom']['conclusion_options'];
        }

        return [
            'template' => trim((string) ($config['template'] ?? 'custom')) ?: 'custom',
            'label' => trim((string) ($config['label'] ?? 'Custom')) ?: 'Custom',
            'comment_label' => trim((string) ($config['comment_label'] ?? 'Komentar/Saran')) ?: 'Komentar/Saran',
            'comment_placeholder' => trim((string) ($config['comment_placeholder'] ?? 'Tuliskan komentar atau saran.')),
            'comment_required' => !empty($config['comment_required']),
            'conclusion_label' => trim((string) ($config['conclusion_label'] ?? 'Kesimpulan')) ?: 'Kesimpulan',
            'conclusion_required' => !array_key_exists('conclusion_required', $config) || !empty($config['conclusion_required']),
            'conclusion_options' => array_slice($options, 0, 20),
        ];
    }

    public static function decodeConfig($value): array
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
}
