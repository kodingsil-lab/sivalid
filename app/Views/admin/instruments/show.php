<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$currentInstrument = isset($instrument) && is_array($instrument) ? $instrument : [];
$items = isset($items) && is_array($items) ? $items : [];
$instrumentId = (int) ($currentInstrument['id'] ?? 0);
$scaleMin = (int) ($currentInstrument['skala_min'] ?? 1);
$scaleMax = (int) ($currentInstrument['skala_max'] ?? 4);
$scaleRange = $scaleMin <= $scaleMax ? range($scaleMin, $scaleMax) : range(1, 4);
$previewLayout = instrument_preview_layout(
    (string) ($currentInstrument['jenis'] ?? '') . ' ' . (string) ($currentInstrument['judul'] ?? '')
);
$usesDocumentReviewLayout = ($previewLayout['type'] ?? 'standard') === 'document_review';
$usesInterviewGuideLayout = ($previewLayout['type'] ?? 'standard') === 'interview_guide';
$usesObservationGuideLayout = ($previewLayout['type'] ?? 'standard') === 'observation_guide';
$usesQuestionnaireLayout = ($previewLayout['type'] ?? 'standard') === 'questionnaire';
$usesProductValidationQuestionnaireLayout = ($previewLayout['type'] ?? 'standard') === 'product_validation_questionnaire';
$usesUserResponseQuestionnaireLayout = ($previewLayout['type'] ?? 'standard') === 'user_response_questionnaire';
$usesPerformanceTestLayout = ($previewLayout['type'] ?? 'standard') === 'performance_test';
$usesRubricAssessmentLayout = ($previewLayout['type'] ?? 'standard') === 'rubric_assessment';
$rubricScaleRange = range(1, 5);

$renderDetailText = static function (?string $value): string {
    $value = trim((string) $value);

    if ($value === '') {
        return '<span class="text-muted">-</span>';
    }

    if (preg_match('/<[a-z][\s\S]*>/i', $value) === 1) {
        $allowedTags = '<p><br><strong><b><em><i><u><s><ol><ul><li><h1><h2><h3><blockquote><table><thead><tbody><tr><th><td>';
        $html = strip_tags($value, $allowedTags);
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/\s+(href|src)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;

        return '<div class="rich-text-content instrument-detail-rich">' . $html . '</div>';
    }

    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $lines = array_values(array_filter(array_map('trim', explode("\n", $value)), static fn(string $line): bool => $line !== ''));
    $isNumberedList = $lines !== [] && count($lines) === count(array_filter($lines, static fn(string $line): bool => preg_match('/^\d+\.\s+/', $line) === 1));

    if ($isNumberedList) {
        $items = array_map(static function (string $line): string {
            $line = preg_replace('/^\d+\.\s+/', '', $line) ?? $line;
            return '<li>' . esc($line) . '</li>';
        }, $lines);

        return '<ol class="instrument-detail-list">' . implode('', $items) . '</ol>';
    }

    $paragraphs = preg_split('/\n{2,}/', $value) ?: [];
    $html = [];

    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);

        if ($paragraph !== '') {
            $html[] = '<p>' . nl2br(esc($paragraph)) . '</p>';
        }
    }

    return '<div class="instrument-detail-prose">' . implode('', $html) . '</div>';
};
?>

<div class="instrument-detail-shell">
<div class="page-header d-print-none mb-3 instrument-detail-page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Instrumen</h2>
            <div class="text-muted mt-1">Informasi lengkap master instrumen dan naskah penyebarannya.</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="d-flex flex-wrap justify-content-end gap-2 instrument-header-actions">
                <a href="<?= base_url('admin/instruments/' . $instrumentId . '/edit') ?>" class="btn btn-warning">Edit</a>
                <a href="<?= base_url('admin/instrument-aspects?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                    Kelola Kisi-Kisi
                </a>
                <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                    Kelola Butir
                </a>
                <?php if (($currentInstrument['status'] ?? '') === 'Valid'): ?>
                    <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-light">
                        Lihat Instrumen Valid
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 instrument-detail-card">
    <div class="card-body">
        <h3 class="card-title mb-3">Identitas Instrumen</h3>

        <div class="table-responsive">
            <table class="table table-vcenter table-sm instrument-meta-table">
                <tbody>
                    <tr>
                        <th style="width: 240px;">Kode Instrumen</th>
                        <td class="fw-semibold"><?= esc((string) ($currentInstrument['kode'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Judul Instrumen</th>
                        <td><?= esc((string) ($currentInstrument['judul'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Instrumen</th>
                        <td><?= esc(title_case_label((string) ($currentInstrument['jenis'] ?? '-'))) ?></td>
                    </tr>
                    <tr>
                        <th>Sasaran</th>
                        <td><?= esc((string) (!empty($currentInstrument['sasaran']) ? $currentInstrument['sasaran'] : '-')) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-3 instrument-detail-card">
    <div class="card-body">
        <h3 class="card-title mb-3">Pengantar dan Petunjuk</h3>

        <div class="instrument-document-section">
            <h4>Pengantar</h4>
            <?= $renderDetailText($currentInstrument['pengantar'] ?? '') ?>
        </div>

        <div class="instrument-document-section">
            <h4>Petunjuk</h4>
            <?= $renderDetailText($currentInstrument['petunjuk'] ?? '') ?>
        </div>

        <div class="instrument-note">Bagian ini menampilkan pengantar dan petunjuk instrumen.</div>
    </div>
</div>

<div class="card mb-3 instrument-detail-card">
    <div class="card-body">
        <h3 class="card-title mb-1">Tabel Instrumen Siap Disebar</h3>
        <div class="text-muted mb-3">
            Layout siap sebar: <?= esc((string) ($previewLayout['title'] ?? 'Tabel Instrumen')) ?>
        </div>

        <?php if ($usesPerformanceTestLayout): ?>
            <div class="instrument-performance-layout">
                <section>
                    <h4>1. Identitas Tes</h4>
                    <table class="table table-sm instrument-plain-table">
                        <tbody>
                            <tr>
                                <th>Nama Tes</th>
                                <td><?= esc((string) ($currentInstrument['judul'] ?? 'Tes Unjuk Kerja')) ?></td>
                            </tr>
                            <tr>
                                <th>Tujuan Tes</th>
                                <td><?= esc((string) (!empty($currentInstrument['keterangan']) ? $currentInstrument['keterangan'] : 'Mengukur kemampuan peserta dalam menyelesaikan tugas unjuk kerja sesuai kriteria yang ditetapkan.')) ?></td>
                            </tr>
                            <tr>
                                <th>Bentuk Tes</th>
                                <td>Tes unjuk kerja berbasis tugas dan portofolio proses.</td>
                            </tr>
                            <tr>
                                <th>Produk yang Dinilai</th>
                                <td><?= esc((string) (!empty($currentInstrument['sasaran']) ? $currentInstrument['sasaran'] : 'Produk akhir beserta bukti proses pengerjaan.')) ?></td>
                            </tr>
                            <tr>
                                <th>Waktu Pelaksanaan</th>
                                <td>Disesuaikan dengan jadwal pembelajaran dan tahapan kegiatan.</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section>
                    <h4>2. Petunjuk Umum</h4>
                    <?= $renderDetailText($currentInstrument['petunjuk'] ?? '') ?>
                </section>

                <section>
                    <h4>3. Tugas Unjuk Kerja</h4>
                    <?= $renderDetailText($currentInstrument['pengantar'] ?? '') ?>
                </section>

                <section>
                    <h4>4. Tahapan dan Produk yang Dikumpulkan</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter instrument-ready-table">
                            <thead>
                                <tr>
                                    <th style="width: 54px;">No</th>
                                    <th style="width: 190px;">Tahap Proses Menulis</th>
                                    <th>Produk yang Dikumpulkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Pra Menulis</td>
                                    <td>Hasil pengamatan, identifikasi isu, pembatasan topik, rumusan judul, daftar referensi awal, dan kerangka awal.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Menulis Draf</td>
                                    <td>Draf awal yang memuat pendahuluan, metode, hasil atau temuan awal, pembahasan awal, simpulan sementara, dan daftar referensi awal.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>Merevisi</td>
                                    <td>Catatan pembacaan ulang draf, peer review, umpan balik, rencana revisi, dan draf hasil revisi.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td>Menyunting</td>
                                    <td>Naskah hasil suntingan dari aspek bahasa ilmiah, kalimat efektif, ejaan, kutipan, daftar pustaka, koherensi, dan format.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td>Mempublikasikan</td>
                                    <td>Produk final, abstrak, kata kunci, checklist kesiapan publikasi, bahan presentasi, unggahan, dan/atau rencana publikasi lanjutan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section>
                    <h4>5. Ketentuan Produk Final</h4>
                    <ol type="a">
                        <li>Panjang dan format produk disesuaikan dengan ketentuan atau template yang digunakan.</li>
                        <li>Produk memuat bagian utama secara lengkap sesuai karakteristik tugas.</li>
                        <li>Rujukan, sitasi, dan daftar pustaka ditulis secara konsisten jika diperlukan.</li>
                        <li>Produk merupakan hasil kerja peserta dan mencerminkan proses revisi berdasarkan umpan balik.</li>
                        <li>Produk final dikumpulkan bersama portofolio proses pengerjaan.</li>
                    </ol>
                </section>

                <section>
                    <h4>6. Aspek Penilaian</h4>
                    <?php if (empty($items)): ?>
                        <p class="text-muted mb-0">Aspek penilaian belum tersedia.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-vcenter instrument-ready-table">
                                <thead>
                                    <tr>
                                        <th style="width: 54px;">No</th>
                                        <th style="width: 260px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                        <th><?= esc((string) $previewLayout['item']) ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $itemIndex => $item): ?>
                                        <tr>
                                            <td class="text-center"><?= esc((string) ($itemIndex + 1)) ?></td>
                                            <td><?= esc((string) ($item['nama_aspek'] ?? '-')) ?></td>
                                            <td><?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        <?php elseif ($usesRubricAssessmentLayout): ?>
            <?php $rubricTotalScore = count($items) * max($rubricScaleRange); ?>
            <div class="instrument-rubric-layout">
                <h4>3. <?= esc((string) ($previewLayout['title'] ?? 'Rubrik Penilaian')) ?></h4>

                <?php if (empty($items)): ?>
                    <p class="text-muted mb-0">Butir rubrik belum tersedia.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter instrument-ready-table instrument-rubric-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 180px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                    <th rowspan="2" style="width: 260px;"><?= esc((string) $previewLayout['item']) ?></th>
                                    <th colspan="<?= count($rubricScaleRange) ?>" class="text-center"><?= esc((string) $previewLayout['score']) ?></th>
                                </tr>
                                <tr>
                                    <?php foreach ($rubricScaleRange as $score): ?>
                                        <th class="text-center">Skor <?= esc((string) $score) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <?php
                                    $indicatorText = trim((string) ($item['indikator'] ?? ''));
                                    if ($indicatorText === '') {
                                        $indicatorText = (string) ($item['pernyataan'] ?? '-');
                                    }
                                    ?>
                                    <tr>
                                        <td><?= esc((string) ($item['nama_aspek'] ?? '-')) ?></td>
                                        <td><?= nl2br(esc($indicatorText)) ?></td>
                                        <?php foreach ($rubricScaleRange as $score): ?>
                                            <?php $scoreField = 'skor_' . $score . '_deskripsi'; ?>
                                            <td><?= nl2br(esc((string) ($item[$scoreField] ?? ''))) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <h4>4. Catatan Penilai</h4>
                <div class="instrument-ready-box instrument-rubric-note-box"></div>

                <h4>5. Rekapitulasi Skor</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-vcenter instrument-ready-table instrument-rubric-recap">
                        <thead>
                            <tr>
                                <th style="width: 54px;">No</th>
                                <th>Aspek Penilaian</th>
                                <th style="width: 150px;">Skor Maksimum</th>
                                <th style="width: 180px;">Skor yang Diperoleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $itemIndex => $item): ?>
                                <tr>
                                    <td class="text-center"><?= esc((string) ($itemIndex + 1)) ?></td>
                                    <td><?= esc((string) ($item['nama_aspek'] ?? '-')) ?></td>
                                    <td class="text-center"><?= esc((string) max($rubricScaleRange)) ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td class="fw-bold">Jumlah Skor</td>
                                <td class="text-center fw-bold"><?= esc((string) $rubricTotalScore) ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="fw-bold">Nilai Akhir</td>
                                <td class="text-center fw-bold">100</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="instrument-rubric-formula">
                    <strong>Rumus Nilai Akhir:</strong><br>
                    Nilai Akhir = (Skor yang Diperoleh / <?= esc((string) max(1, $rubricTotalScore)) ?>) x 100
                </div>

                <h4>6. Kesimpulan Penilaian</h4>
                <p>Berdasarkan hasil penilaian, kemampuan menulis artikel ilmiah mahasiswa dinyatakan:</p>
                <label><span class="instrument-check-box"></span> Sangat Baik</label>
                <label><span class="instrument-check-box"></span> Baik</label>
                <label><span class="instrument-check-box"></span> Cukup</label>
                <label><span class="instrument-check-box"></span> Kurang</label>
                <label><span class="instrument-check-box"></span> Sangat Kurang</label>
            </div>
        <?php elseif (empty($items)): ?>
            <p class="text-muted mb-0">Butir instrumen belum tersedia.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-vcenter instrument-ready-table <?= $usesDocumentReviewLayout ? 'instrument-document-review-table' : '' ?>">
                    <thead>
                        <?php if ($usesDocumentReviewLayout): ?>
                            <tr>
                                <th rowspan="2" style="width: 54px;">No</th>
                                <th rowspan="2" style="width: 200px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                <th rowspan="2" style="width: 390px;"><?= esc((string) $previewLayout['item']) ?></th>
                                <th rowspan="2" style="width: 150px;">Sumber Dokumen</th>
                                <th colspan="<?= count($scaleRange) ?>" class="text-center"><?= esc((string) $previewLayout['score']) ?></th>
                                <th rowspan="2" style="width: 220px;"><?= esc((string) $previewLayout['comment']) ?></th>
                            </tr>
                            <tr>
                                <?php foreach ($scaleRange as $score): ?>
                                    <th class="text-center instrument-score-col"><?= esc((string) $score) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        <?php elseif ($usesInterviewGuideLayout): ?>
                            <tr>
                                <th style="width: 54px;">No</th>
                                <th style="width: 170px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                <th><?= esc((string) $previewLayout['item']) ?></th>
                                <th style="width: 260px;"><?= esc((string) $previewLayout['answer']) ?></th>
                            </tr>
                        <?php elseif ($usesObservationGuideLayout): ?>
                            <tr>
                                <th style="width: 54px;">No</th>
                                <th style="width: 190px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                <th><?= esc((string) $previewLayout['item']) ?></th>
                                <th style="width: 280px;"><?= esc((string) $previewLayout['result']) ?></th>
                            </tr>
                        <?php elseif ($usesQuestionnaireLayout || $usesProductValidationQuestionnaireLayout || $usesUserResponseQuestionnaireLayout): ?>
                            <tr>
                                <th rowspan="2" style="width: 54px;">No</th>
                                <th rowspan="2" style="width: 220px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                <th rowspan="2"><?= esc((string) $previewLayout['item']) ?></th>
                                <th colspan="<?= count($scaleRange) ?>" class="text-center"><?= esc((string) $previewLayout['score']) ?></th>
                            </tr>
                            <tr>
                                <?php foreach ($scaleRange as $score): ?>
                                    <th class="text-center instrument-score-col"><?= esc((string) $score) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th style="width: 54px;">No</th>
                                <th style="width: 220px;"><?= esc((string) $previewLayout['aspect']) ?></th>
                                <th><?= esc((string) $previewLayout['item']) ?></th>
                                <?php foreach ($scaleRange as $score): ?>
                                    <th class="text-center instrument-score-col"><?= esc((string) $score) ?></th>
                                <?php endforeach; ?>
                                <th style="width: 190px;"><?= esc((string) $previewLayout['comment']) ?></th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $itemIndex => $item): ?>
                            <tr>
                                <td class="text-center"><?= esc((string) ($itemIndex + 1)) ?></td>
                                <td><?= esc((string) ($item['nama_aspek'] ?? '-')) ?></td>
                                <td><?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?></td>
                                <?php if ($usesDocumentReviewLayout): ?>
                                    <td><?= esc(document_review_source_label($item['sumber_dokumen'] ?? '')) ?></td>
                                <?php endif; ?>
                                <?php if ($usesInterviewGuideLayout || $usesObservationGuideLayout): ?>
                                    <td></td>
                                <?php else: ?>
                                    <?php foreach ($scaleRange as $score): ?>
                                        <td class="text-center"></td>
                                    <?php endforeach; ?>
                                    <?php if (! $usesQuestionnaireLayout && ! $usesProductValidationQuestionnaireLayout && ! $usesUserResponseQuestionnaireLayout): ?>
                                        <td></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($usesProductValidationQuestionnaireLayout): ?>
                <div class="instrument-ready-extra">
                    <h4>1. Komentar/Saran</h4>
                    <div class="instrument-ready-box"></div>

                    <h4>2. Kesimpulan Validasi</h4>
                    <p>Berdasarkan hasil penilaian, produk dinyatakan:</p>
                    <label><span class="instrument-check-box"></span> Sangat Layak</label>
                    <label><span class="instrument-check-box"></span> Layak</label>
                    <label><span class="instrument-check-box"></span> Kurang Layak</label>
                    <label><span class="instrument-check-box"></span> Tidak Layak</label>
                </div>
            <?php endif; ?>

            <?php if ($usesUserResponseQuestionnaireLayout): ?>
                <div class="instrument-ready-extra">
                    <h4>Catatan/Saran Pengguna</h4>
                    <div class="instrument-ready-box"></div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</div>

<style>
    .instrument-detail-shell {
        width: 100%;
        max-width: 1180px;
        margin-left: auto;
        margin-right: auto;
        font-size: 14px;
    }

    .instrument-detail-page-header {
        width: 100%;
    }

    .instrument-detail-shell table,
    .instrument-detail-shell th,
    .instrument-detail-shell td,
    .instrument-detail-shell p,
    .instrument-detail-shell li,
    .instrument-detail-shell .badge,
    .instrument-detail-shell .btn {
        font-size: 14px !important;
    }

    .instrument-detail-card > .card-body {
        padding: 1.25rem 1.35rem;
    }

    .instrument-detail-card .card-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 700;
    }

    .instrument-detail-card {
        font-size: 14px;
    }

    .instrument-meta-table {
        font-size: 14px;
    }

    .instrument-meta-table th {
        width: 230px;
        color: #334155;
        font-weight: 700;
        vertical-align: top;
        background: #f8fafc;
    }

    .instrument-meta-table td {
        color: #0f172a;
        line-height: 1.55;
    }

    .instrument-meta-table th,
    .instrument-meta-table td {
        padding: .82rem .9rem !important;
    }

    .instrument-document-section {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.35rem;
        padding-bottom: 1.35rem;
    }

    .instrument-document-section h4 {
        margin: 0 0 0.85rem;
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
    }

    .instrument-scale-table {
        max-width: 460px;
        margin-bottom: 0;
        font-size: 14px;
        border: 1px solid #e2e8f0;
    }

    .instrument-scale-table th,
    .instrument-scale-table td {
        text-align: center;
    }

    .instrument-ready-table {
        min-width: 900px;
        table-layout: fixed;
    }

    .instrument-ready-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        white-space: normal !important;
        overflow-wrap: break-word;
        word-break: normal;
    }

    .instrument-ready-table td {
        color: #0f172a;
        line-height: 1.45;
        vertical-align: middle;
        white-space: normal !important;
        overflow-wrap: break-word;
        word-break: normal;
    }

    .instrument-document-review-table {
        min-width: 1220px;
    }

    .instrument-document-review-table th,
    .instrument-document-review-table td {
        padding-left: .8rem !important;
        padding-right: .8rem !important;
    }

    .instrument-document-review-table td:nth-child(1) {
        width: 54px;
    }

    .instrument-document-review-table td:nth-child(2) {
        width: 200px;
    }

    .instrument-document-review-table td:nth-child(3) {
        width: 390px;
    }

    .instrument-document-review-table td:nth-child(4) {
        width: 150px;
    }

    .instrument-document-review-table td:last-child {
        width: 220px;
    }

    .instrument-score-col {
        width: 50px;
    }

    .instrument-ready-extra {
        color: #0f172a;
        margin-top: 1.25rem;
    }

    .instrument-ready-extra h4 {
        font-size: 14px;
        font-weight: 700;
        margin: 1rem 0 .4rem;
    }

    .instrument-ready-extra p {
        margin: 0 0 .35rem;
    }

    .instrument-ready-box {
        border: 1px solid #0f172a;
        height: 150px;
        margin-bottom: 1rem;
        width: 100%;
    }

    .instrument-ready-extra label {
        display: block;
        line-height: 1.7;
        margin: 0;
    }

    .instrument-check-box {
        border: 1px solid #0f172a;
        display: inline-block;
        height: 11px;
        margin-right: .35rem;
        vertical-align: -1px;
        width: 11px;
    }

    .instrument-performance-layout section {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.2rem;
        padding-bottom: 1.2rem;
    }

    .instrument-performance-layout section:last-child {
        border-bottom: 0;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .instrument-performance-layout h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 .75rem;
    }

    .instrument-performance-layout ol {
        margin: 0;
        padding-left: 1.5rem;
    }

    .instrument-performance-layout li {
        margin-bottom: .35rem;
    }

    .instrument-plain-table {
        max-width: 980px;
        margin-bottom: 0;
    }

    .instrument-plain-table th {
        width: 190px;
        color: #0f172a;
        font-weight: 700;
        vertical-align: top;
    }

    .instrument-rubric-layout h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
        margin: 1rem 0 .55rem;
    }

    .instrument-rubric-layout h4:first-child {
        margin-top: 0;
    }

    .instrument-rubric-layout p {
        margin: 0 0 .35rem;
    }

    .instrument-rubric-table {
        min-width: 1180px;
    }

    .instrument-rubric-table th,
    .instrument-rubric-table td {
        vertical-align: top;
    }

    .instrument-rubric-note-box {
        height: 150px;
    }

    .instrument-rubric-formula {
        color: #0f172a;
        line-height: 1.6;
        margin: .75rem 0 1rem;
    }

    .instrument-rubric-layout label {
        display: block;
        line-height: 1.7;
        margin: 0;
    }

    .instrument-detail-prose {
        max-width: 1040px;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.8;
    }

    .instrument-detail-rich {
        max-width: 1040px;
        padding: 0;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.8;
        white-space: normal;
    }

    .instrument-detail-rich .ql-align-center {
        text-align: center;
    }

    .instrument-detail-rich .ql-align-right {
        text-align: right;
    }

    .instrument-detail-rich .ql-align-justify {
        text-align: justify;
        text-justify: inter-word;
    }

    .instrument-detail-rich p,
    .instrument-detail-rich ol,
    .instrument-detail-rich ul,
    .instrument-detail-rich blockquote {
        margin-bottom: 0.75rem;
    }

    .instrument-detail-rich p:last-child,
    .instrument-detail-rich ol:last-child,
    .instrument-detail-rich ul:last-child,
    .instrument-detail-rich blockquote:last-child {
        margin-bottom: 0;
    }

    .instrument-detail-rich ol,
    .instrument-detail-rich ul {
        padding-left: 1.75rem;
    }

    .instrument-detail-rich ol {
        counter-reset: instrument-list-0 instrument-list-1 instrument-list-2;
    }

    .instrument-detail-rich ol > li {
        list-style: none;
        position: relative;
        counter-increment: instrument-list-0;
        padding-left: 1.65rem;
    }

    .instrument-detail-rich ol > li::before {
        content: counter(instrument-list-0, decimal) ".";
        position: absolute;
        left: 0;
        color: #0f172a;
    }

    .instrument-detail-rich ol > li.ql-indent-1 {
        counter-increment: instrument-list-1;
        margin-left: 1.75rem;
    }

    .instrument-detail-rich ol > li.ql-indent-1::before {
        content: counter(instrument-list-1, lower-alpha) ".";
    }

    .instrument-detail-rich ol > li.ql-indent-2 {
        counter-increment: instrument-list-2;
        margin-left: 3.5rem;
    }

    .instrument-detail-rich ol > li.ql-indent-2::before {
        content: counter(instrument-list-2, lower-roman) ".";
    }

    .instrument-detail-rich ul > li.ql-indent-1 {
        margin-left: 1.75rem;
    }

    .instrument-detail-rich ul > li.ql-indent-2 {
        margin-left: 3.5rem;
    }

    .instrument-detail-rich li {
        padding-left: 0.35rem;
        margin-bottom: 0.45rem;
    }

    .instrument-detail-rich li:last-child {
        margin-bottom: 0;
    }

    .instrument-detail-prose p {
        margin: 0 0 0.9rem;
    }

    .instrument-detail-prose p:last-child {
        margin-bottom: 0;
    }

    .instrument-detail-list {
        max-width: 1040px;
        margin: 0;
        padding-left: 1.75rem;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.8;
    }

    .instrument-detail-list li {
        padding-left: 0.35rem;
        margin-bottom: 0.45rem;
    }

    .instrument-detail-list li:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .instrument-meta-table th,
        .instrument-meta-table td {
            display: block;
            width: 100% !important;
        }

        .instrument-meta-table th {
            padding-bottom: 0.25rem;
            border-bottom: 0;
        }

        .instrument-meta-table td {
            padding-top: 0.25rem;
        }
    }

    .instrument-note {
        border-top: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.55;
        margin-top: 1.35rem;
        padding-top: .95rem;
    }

    .instrument-header-actions {
        max-width: 760px;
    }
</style>

<?= $this->endSection() ?>
