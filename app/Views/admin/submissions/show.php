<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$currentResponse = isset($response) && is_array($response) ? $response : [];
$safeAnswers = isset($answers) && is_array($answers) ? $answers : [];

$modeValue = (string) ($currentResponse['mode'] ?? '');
$modeLabel = ucwords(str_replace('_', ' ', $modeValue));
$modeBadgeClass = 'badge bg-secondary text-secondary-fg';

if ($modeValue === 'validasi_instrumen') {
    $modeBadgeClass = 'badge bg-blue text-blue-fg';
} elseif ($modeValue === 'validasi_produk') {
    $modeBadgeClass = 'badge bg-orange text-orange-fg';
} elseif (in_array($modeValue, ['respon_mahasiswa', 'observasi', 'fgd', 'tes_kinerja'], true)) {
    $modeBadgeClass = 'badge bg-green text-green-fg';
}

$respondentTypeLabel = static function (array $row, array $fields = []): string {
    $templateKey = trim((string) ($row['identity_template'] ?? ''));
    $templates = \App\Libraries\RespondentIdentitySchema::templates();

    if ($templateKey !== '' && isset($templates[$templateKey])) {
        return (string) $templates[$templateKey]['label'];
    }

    foreach ($fields as $field) {
        $label = strtolower((string) ($field['label'] ?? ''));

        if (str_contains($label, 'dosen')) {
            return 'Dosen';
        }

        if (str_contains($label, 'mahasiswa') || str_contains($label, 'nim')) {
            return 'Mahasiswa';
        }

        if (str_contains($label, 'guru') || str_contains($label, 'praktisi')) {
            return 'Guru / Praktisi';
        }

        if (str_contains($label, 'validator') || str_contains($label, 'ahli')) {
            return 'Validator / Ahli';
        }
    }

    return title_case_label((string) ($row['jenis_responden'] ?? '-'));
};

$identityData = [];
if (!empty($currentResponse['identity_data'])) {
    $decodedIdentity = json_decode((string) $currentResponse['identity_data'], true);
    $identityData = is_array($decodedIdentity) ? $decodedIdentity : [];
}

$identityFields = [];
if (!empty($currentResponse['identity_fields'])) {
    $decodedFields = json_decode((string) $currentResponse['identity_fields'], true);
    $identityFields = is_array($decodedFields) ? $decodedFields : [];
}

if (empty($identityFields)) {
    $identityFields = [
        ['key' => 'nama', 'label' => 'Nama'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'nim', 'label' => 'NIM'],
        ['key' => 'program_studi', 'label' => 'Program Studi'],
        ['key' => 'kelas', 'label' => 'Kelas'],
        ['key' => 'semester', 'label' => 'Semester/Pertemuan'],
        ['key' => 'instansi', 'label' => 'Instansi'],
        ['key' => 'bidang_keahlian', 'label' => 'Bidang/Jabatan'],
    ];
}

foreach (['nama', 'email', 'nim', 'program_studi', 'kelas', 'semester', 'instansi', 'bidang_keahlian'] as $identityKey) {
    if (!isset($identityData[$identityKey])) {
        $identityData[$identityKey] = $currentResponse[$identityKey] ?? '';
    }
}

$justificationData = [];
if (!empty($currentResponse['justification_data'])) {
    $decodedJustification = json_decode((string) $currentResponse['justification_data'], true);
    $justificationData = is_array($decodedJustification) ? $decodedJustification : [];
}

$justificationConfig = [];
if (!empty($currentResponse['justification_config'])) {
    $decodedJustificationConfig = json_decode((string) $currentResponse['justification_config'], true);
    $justificationConfig = is_array($decodedJustificationConfig) ? $decodedJustificationConfig : [];
}

$commentLabel = (string) ($justificationData['comment_label'] ?? $justificationConfig['comment_label'] ?? 'Komentar Umum');
$conclusionLabel = (string) ($justificationData['conclusion_label'] ?? $justificationConfig['conclusion_label'] ?? 'Kesimpulan');
$previewLayout = instrument_preview_layout($currentResponse['jenis'] ?? '');
$layoutType = (string) ($previewLayout['type'] ?? 'standard');
$usesDocumentReview = $layoutType === 'document_review';
$usesInterview = $layoutType === 'interview_guide';
$usesObservation = $layoutType === 'observation_guide';
$usesRubric = $layoutType === 'rubric_assessment';
$usesQuestionnaire = in_array($layoutType, ['questionnaire', 'product_validation_questionnaire', 'user_response_questionnaire'], true);
$usesPerformanceTest = $layoutType === 'performance_test';
$respondenLabel = $respondentTypeLabel($currentResponse, $identityFields);
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Hasil Pengisian</h2>
            <div class="text-muted mt-1">Rincian pengisian instrumen oleh responden/validator.</div>
        </div>
        <div class="col-auto ms-auto">
            <span class="<?= esc($modeBadgeClass) ?>">
                <?= esc($respondenLabel) ?>
            </span>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
    <h3 class="card-title mb-3">Identitas Link</h3>

    <div class="table-responsive">
        <table class="table table-vcenter table-sm">
            <tbody>
                <tr>
                    <th style="width: 240px;">Kategori Pengisi</th>
                    <td>
                        <span class="<?= esc($modeBadgeClass) ?>">
                            <?= esc($respondenLabel) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Judul Link</th>
                    <td><?= esc((string) ($currentResponse['judul_link'] ?? '-')) ?></td>
                </tr>
                <tr>
                    <th>Instrumen</th>
                    <td>
                        <div class="fw-semibold"><?= esc((string) ($currentResponse['kode'] ?? '-')) ?></div>
                        <div><?= esc((string) ($currentResponse['judul'] ?? '-')) ?></div>
                        <div class="small text-muted"><?= esc(title_case_label((string) ($currentResponse['jenis'] ?? '-'))) ?></div>
                    </td>
                </tr>
                <tr>
                    <th>Produk</th>
                    <td>
                        <?php if (!empty($currentResponse['nama_produk'])): ?>
                            <div class="fw-semibold"><?= esc((string) ($currentResponse['product_kode'] ?? '-')) ?> - <?= esc((string) $currentResponse['nama_produk']) ?></div>
                            <div class="small text-muted"><?= esc(title_case_label((string) ($currentResponse['jenis_produk'] ?? '-'))) ?></div>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Status Link</th>
                    <td><?= esc((string) (!empty($currentResponse['status']) ? $currentResponse['status'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>Waktu Submit</th>
                    <td><?= esc((string) (!empty($currentResponse['submitted_at']) ? $currentResponse['submitted_at'] : '-')) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
    <h3 class="card-title mb-3">Identitas Responden/Validator</h3>

    <div class="table-responsive">
        <table class="table table-vcenter table-sm">
            <tbody>
                <tr>
                    <th>Jenis Responden</th>
                    <td><?= esc($respondenLabel) ?></td>
                </tr>
                <?php foreach ($identityFields as $field): ?>
                    <?php
                    $key = (string) ($field['key'] ?? '');
                    $label = (string) ($field['label'] ?? $key);
                    $value = $key !== '' ? trim((string) ($identityData[$key] ?? '')) : '';
                    ?>
                    <?php if ($key === '' || $value === '') {
                        continue;
                    } ?>
                    <tr>
                        <th style="width: 240px;"><?= esc($label) ?></th>
                        <td class="<?= $key === 'nama' ? 'fw-semibold' : '' ?>"><?= nl2br(esc($value)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Hasil Pengisian</h3>

        <?php if (empty($safeAnswers)): ?>
            <div class="empty-state">Belum ada jawaban.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th style="width: 180px;"><?= esc((string) ($previewLayout['aspect'] ?? 'Aspek')) ?></th>
                            <th><?= esc((string) ($previewLayout['item'] ?? 'Butir')) ?></th>
                            <?php if ($usesDocumentReview): ?>
                                <th style="width: 150px;">Sumber Dokumen</th>
                                <th style="width: 100px;">Skor</th>
                                <th style="width: 220px;">Komentar</th>
                            <?php elseif ($usesRubric): ?>
                                <?php foreach (range(1, 5) as $score): ?>
                                    <th style="width: 190px;">Skor <?= $score ?></th>
                                <?php endforeach; ?>
                                <th style="width: 120px;">Skor Diperoleh</th>
                                <th style="width: 220px;">Catatan</th>
                            <?php elseif ($usesInterview): ?>
                                <th style="width: 300px;"><?= esc((string) ($previewLayout['answer'] ?? 'Jawaban')) ?></th>
                            <?php elseif ($usesObservation): ?>
                                <th style="width: 300px;"><?= esc((string) ($previewLayout['result'] ?? 'Hasil Pengamatan')) ?></th>
                            <?php else: ?>
                                <th style="width: 120px;"><?= $usesQuestionnaire || $usesPerformanceTest ? 'Skor' : 'Jawaban' ?></th>
                                <th style="width: 220px;">Komentar/Catatan</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($safeAnswers as $answer): ?>
                            <tr>
                                <td class="text-muted"><?= esc((string) ($answer['nomor'] ?? '-')) ?></td>
                                <td><?= esc((string) (!empty($answer['nama_aspek']) ? $answer['nama_aspek'] : '-')) ?></td>
                                <td><?= nl2br(esc((string) ($answer['pernyataan'] ?? '-'))) ?></td>
                                <?php if ($usesDocumentReview): ?>
                                    <td><?= esc(document_review_source_label($answer['sumber_dokumen'] ?? '')) ?></td>
                                    <td><span class="fw-semibold"><?= esc((string) ($answer['skor'] ?? '-')) ?></span></td>
                                    <td><?= nl2br(esc((string) (!empty($answer['komentar']) ? $answer['komentar'] : '-'))) ?></td>
                                <?php elseif ($usesRubric): ?>
                                    <?php foreach (range(1, 5) as $score): ?>
                                        <td><?= nl2br(esc((string) ($answer['skor_' . $score . '_deskripsi'] ?? '-'))) ?></td>
                                    <?php endforeach; ?>
                                    <td><span class="fw-semibold"><?= esc((string) ($answer['skor'] ?? '-')) ?></span></td>
                                    <td><?= nl2br(esc((string) (!empty($answer['komentar']) ? $answer['komentar'] : '-'))) ?></td>
                                <?php elseif ($usesInterview || $usesObservation): ?>
                                    <td><?= nl2br(esc((string) (!empty($answer['jawaban_teks']) ? $answer['jawaban_teks'] : '-'))) ?></td>
                                <?php else: ?>
                                    <td><span class="fw-semibold"><?= esc((string) (($answer['skor'] ?? '') !== '' ? $answer['skor'] : ($answer['jawaban_teks'] ?? '-'))) ?></span></td>
                                    <td><?= nl2br(esc((string) (!empty($answer['komentar']) ? $answer['komentar'] : '-'))) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-2"><?= esc($commentLabel) ?></h3>
        <div class="text-muted"><?= nl2br(esc((string) (!empty($currentResponse['komentar_umum']) ? $currentResponse['komentar_umum'] : '-'))) ?></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-2"><?= esc($conclusionLabel) ?></h3>
        <div class="fw-semibold"><?= esc((string) (!empty($currentResponse['kesimpulan']) ? $currentResponse['kesimpulan'] : '-')) ?></div>
    </div>
</div>

<a href="<?= base_url('admin/submissions') ?>" class="btn btn-light">Kembali</a>

<?= $this->endSection() ?>
