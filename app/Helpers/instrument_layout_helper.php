<?php

if (! function_exists('sivalid_default_instrument_types')) {
    function sivalid_default_instrument_types(): array
    {
        return [
            'Panduan Analisis Perangkat Pembelajaran',
            'Pedoman Wawancara',
            'Pedoman Observasi',
            'Angket',
            'Angket Validasi Produk',
            'Angket Respon Pengguna',
            'Focus Group Discussion',
            'Tes Unjuk Kerja',
            'Rubrik Penilaian',
        ];
    }
}

if (! function_exists('instrument_type_key')) {
    function instrument_type_key(?string $jenis): string
    {
        $jenis = strtolower(trim((string) $jenis));
        $jenis = preg_replace('/\s+/', ' ', $jenis) ?? $jenis;

        if (str_contains($jenis, 'panduan analisis perangkat pembelajaran')) {
            return 'panduan_analisis_perangkat_pembelajaran';
        }

        if (str_contains($jenis, 'pedoman wawancara') || $jenis === 'wawancara') {
            return 'pedoman_wawancara';
        }

        if (str_contains($jenis, 'pedoman observasi') || $jenis === 'observasi') {
            return 'pedoman_observasi';
        }

        if (
            str_contains($jenis, 'focus group discussion')
            || str_contains($jenis, 'pedoman fgd')
            || $jenis === 'fgd'
        ) {
            return 'focus_group_discussion';
        }

        if (str_contains($jenis, 'angket validasi produk')) {
            return 'angket_validasi_produk';
        }

        if (str_contains($jenis, 'angket respon pengguna')) {
            return 'angket_respon_pengguna';
        }

        if (str_contains($jenis, 'angket')) {
            return 'angket';
        }

        if (str_contains($jenis, 'tes unjuk kerja') || str_contains($jenis, 'tes kinerja')) {
            return 'tes_unjuk_kerja';
        }

        if (str_contains($jenis, 'rubrik penilaian')) {
            return 'rubrik_penilaian';
        }

        return 'umum';
    }
}

if (! function_exists('sivalid_sort_instrument_type_rows')) {
    function sivalid_sort_instrument_type_rows(array $rows): array
    {
        $defaults = sivalid_default_instrument_types();

        usort($rows, static function (array $left, array $right) use ($defaults): int {
            $leftValue = (string) ($left['setting_value'] ?? '');
            $rightValue = (string) ($right['setting_value'] ?? '');
            $leftIndex = null;
            $rightIndex = null;

            foreach ($defaults as $index => $defaultType) {
                if (mb_strtolower($leftValue) === mb_strtolower($defaultType)) {
                    $leftIndex = $index;
                }

                if (mb_strtolower($rightValue) === mb_strtolower($defaultType)) {
                    $rightIndex = $index;
                }
            }

            if ($leftIndex !== null && $rightIndex !== null) {
                return $leftIndex <=> $rightIndex;
            }

            if ($leftIndex !== null) {
                return -1;
            }

            if ($rightIndex !== null) {
                return 1;
            }

            return strnatcasecmp($leftValue, $rightValue);
        });

        return $rows;
    }
}

if (! function_exists('instrument_uses_document_review_layout')) {
    function instrument_uses_document_review_layout(?string $jenis): bool
    {
        return instrument_type_key($jenis) === 'panduan_analisis_perangkat_pembelajaran';
    }
}

if (! function_exists('instrument_preview_layout')) {
    function instrument_preview_layout(?string $jenis): array
    {
        return match (instrument_type_key($jenis)) {
            'panduan_analisis_perangkat_pembelajaran' => [
                'type' => 'document_review',
                'title' => 'Panduan Analisis Perangkat Pembelajaran',
                'aspect' => 'Aspek yang Dianalisis',
                'item' => 'Item Telaah',
                'score' => 'Skor',
                'comment' => 'Komentar',
            ],
            'pedoman_wawancara' => [
                'type' => 'interview_guide',
                'title' => 'Pedoman Wawancara',
                'aspect' => 'Aspek yang Ditanyakan',
                'item' => 'Pertanyaan Wawancara',
                'answer' => 'Jawaban',
            ],
            'pedoman_observasi' => [
                'type' => 'observation_guide',
                'title' => 'Pedoman Observasi',
                'aspect' => 'Aspek yang Diobservasi',
                'item' => 'Fokus Pengamatan',
                'result' => 'Catatan Aktivitas',
            ],
            'focus_group_discussion' => [
                'type' => 'focus_group_discussion',
                'title' => 'Format Pedoman FGD',
                'aspect' => 'Aspek yang Didiskusikan',
                'item' => 'Pertanyaan Pemandu/Fokus Diskusi',
                'comment' => 'Komentar',
                'general_note' => 'Catatan Umum FGD',
            ],
            'angket' => [
                'type' => 'questionnaire',
                'title' => 'Angket',
                'aspect' => 'Aspek',
                'item' => 'Butir Pernyataan',
                'score' => 'Skor',
            ],
            'angket_validasi_produk' => [
                'type' => 'product_validation_questionnaire',
                'title' => 'Angket Validasi Produk',
                'aspect' => 'Aspek',
                'item' => 'Butir Pernyataan',
                'score' => 'Skor',
            ],
            'angket_respon_pengguna' => [
                'type' => 'user_response_questionnaire',
                'title' => 'Angket Respon Pengguna',
                'aspect' => 'Aspek',
                'item' => 'Butir Pernyataan',
                'score' => 'Skor',
            ],
            'tes_unjuk_kerja' => [
                'type' => 'performance_test',
                'title' => 'Tes Unjuk Kerja',
                'aspect' => 'Aspek yang Dinilai',
                'item' => 'Fokus Penilaian',
            ],
            'rubrik_penilaian' => [
                'type' => 'rubric_assessment',
                'title' => 'Rubrik Penilaian',
                'aspect' => 'Aspek',
                'item' => 'Indikator',
                'score' => 'Skor',
            ],
            default => [
                'type' => 'standard',
                'title' => 'Tabel Instrumen',
                'aspect' => 'Aspek',
                'item' => 'Butir Pernyataan',
                'score' => 'Skor',
                'comment' => 'Komentar',
            ],
        };
    }
}

if (! function_exists('document_review_source_label')) {
    function document_review_source_label(?string $value): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : '-';
    }
}

if (! function_exists('instrument_item_entry_layout')) {
    function instrument_item_entry_layout(?string $jenis): array
    {
        $preview = instrument_preview_layout($jenis);
        $type = (string) ($preview['type'] ?? 'standard');
        $usesRubricScores = $type === 'rubric_assessment';

        return [
            'type' => $type,
            'title' => (string) ($preview['title'] ?? 'Tabel Instrumen'),
            'aspect_label' => (string) ($preview['aspect'] ?? 'Aspek'),
            'indicator_label' => $type === 'rubric_assessment' ? 'Indikator Kisi-Kisi' : 'Indikator',
            'item_label' => (string) ($preview['item'] ?? 'Butir Pernyataan'),
            'item_placeholder' => match ($type) {
                'document_review' => 'Tuliskan item telaah.',
                'interview_guide' => 'Tuliskan pertanyaan wawancara.',
                'observation_guide' => 'Tuliskan fokus pengamatan.',
                'focus_group_discussion' => 'Tuliskan pertanyaan pemandu atau fokus diskusi.',
                'performance_test' => 'Tuliskan fokus penilaian.',
                'rubric_assessment' => 'Tuliskan indikator rubrik penilaian.',
                default => 'Tuliskan butir pernyataan instrumen.',
            },
            'show_source_document' => $type === 'document_review',
            'show_rubric_scores' => $usesRubricScores,
            'default_item_type' => $usesRubricScores || in_array($type, [
                'document_review',
                'questionnaire',
                'product_validation_questionnaire',
                'user_response_questionnaire',
                'performance_test',
            ], true) ? 'skala' : 'isian',
            'excel_item_header' => (string) ($preview['item'] ?? 'Butir Pernyataan'),
        ];
    }
}

if (! function_exists('instrument_public_justification_config')) {
    function instrument_public_justification_config(?string $jenis): array
    {
        return match (instrument_type_key($jenis)) {
            'angket_validasi_produk' => [
                'template' => 'instrument_type',
                'label' => 'Angket Validasi Produk',
                'show_comment' => true,
                'comment_label' => 'Komentar/Saran',
                'comment_placeholder' => 'Tuliskan komentar atau saran.',
                'comment_required' => false,
                'show_conclusion' => true,
                'conclusion_label' => 'Kesimpulan Validasi',
                'conclusion_required' => true,
                'conclusion_options' => ['Sangat Layak', 'Layak', 'Kurang Layak', 'Tidak Layak'],
            ],
            'angket_respon_pengguna' => [
                'template' => 'instrument_type',
                'label' => 'Angket Respon Pengguna',
                'show_comment' => true,
                'comment_label' => 'Catatan/Saran Pengguna',
                'comment_placeholder' => 'Tuliskan catatan atau saran pengguna.',
                'comment_required' => false,
                'show_conclusion' => false,
                'conclusion_label' => '',
                'conclusion_required' => false,
                'conclusion_options' => [],
            ],
            'rubrik_penilaian' => [
                'template' => 'instrument_type',
                'label' => 'Rubrik Penilaian',
                'show_comment' => true,
                'comment_label' => 'Catatan Penilai',
                'comment_placeholder' => 'Tuliskan catatan penilai.',
                'comment_required' => false,
                'show_conclusion' => true,
                'conclusion_label' => 'Kesimpulan Penilaian',
                'conclusion_required' => true,
                'conclusion_options' => ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang'],
            ],
            'focus_group_discussion' => [
                'template' => 'instrument_type',
                'label' => 'Focus Group Discussion',
                'show_comment' => true,
                'comment_label' => 'Catatan Umum FGD',
                'comment_placeholder' => 'Tuliskan catatan umum, masukan utama, atau rekomendasi dari FGD.',
                'comment_required' => false,
                'show_conclusion' => false,
                'conclusion_label' => '',
                'conclusion_required' => false,
                'conclusion_options' => [],
            ],
            default => [
                'template' => 'none',
                'label' => 'Tidak ada',
                'show_comment' => false,
                'comment_label' => '',
                'comment_placeholder' => '',
                'comment_required' => false,
                'show_conclusion' => false,
                'conclusion_label' => '',
                'conclusion_required' => false,
                'conclusion_options' => [],
            ],
        };
    }
}
