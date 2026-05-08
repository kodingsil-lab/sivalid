<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$currentResponse = isset($response) && is_array($response) ? $response : [];
$safeAnswers = isset($answers) && is_array($answers) ? $answers : [];

$modeValue = (string) ($currentResponse['mode'] ?? '');
$modeLabel = ucwords(str_replace('_', ' ', $modeValue));
$modeBadgeClass = 'badge badge-status-draft';

if ($modeValue === 'validasi_instrumen') {
    $modeBadgeClass = 'badge badge-status-process';
} elseif ($modeValue === 'validasi_produk') {
    $modeBadgeClass = 'badge badge-status-warning';
} elseif (in_array($modeValue, ['respon_mahasiswa', 'observasi', 'fgd', 'tes_kinerja'], true)) {
    $modeBadgeClass = 'badge badge-status-success';
}

$scoreAnswers = array_values(array_filter($safeAnswers, static function ($answer) {
    return isset($answer['skor']) && $answer['skor'] !== null && $answer['skor'] !== '';
}));

$textAnswers = array_values(array_filter($safeAnswers, static function ($answer) {
    return !empty($answer['jawaban_teks']);
}));

$commentAnswers = array_values(array_filter($safeAnswers, static function ($answer) {
    return !empty($answer['komentar']);
}));
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Hasil Pengisian</h2>
            <div class="text-muted mt-1">Rincian pengisian instrumen oleh responden/validator.</div>
        </div>
        <div class="col-auto ms-auto">
            <span class="<?= esc($modeBadgeClass) ?>">
                <?= esc($modeLabel !== '' ? $modeLabel : '-') ?>
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
                    <th style="width: 240px;">Mode</th>
                    <td>
                        <span class="<?= esc($modeBadgeClass) ?>">
                            <?= esc($modeLabel !== '' ? $modeLabel : '-') ?>
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
                        <div class="small text-muted"><?= esc((string) ($currentResponse['jenis'] ?? '-')) ?></div>
                    </td>
                </tr>
                <tr>
                    <th>Produk</th>
                    <td>
                        <?php if (!empty($currentResponse['nama_produk'])): ?>
                            <div class="fw-semibold"><?= esc((string) ($currentResponse['product_kode'] ?? '-')) ?> - <?= esc((string) $currentResponse['nama_produk']) ?></div>
                            <div class="small text-muted"><?= esc((string) ($currentResponse['jenis_produk'] ?? '-')) ?></div>
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
                    <th style="width: 240px;">Nama</th>
                    <td class="fw-semibold"><?= esc((string) ($currentResponse['nama'] ?? '-')) ?></td>
                </tr>
                <tr>
                    <th>Jenis Responden</th>
                    <td><?= esc((string) ($currentResponse['jenis_responden'] ?? '-')) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= esc((string) (!empty($currentResponse['email']) ? $currentResponse['email'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>NIM</th>
                    <td><?= esc((string) (!empty($currentResponse['nim']) ? $currentResponse['nim'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>Program Studi</th>
                    <td><?= esc((string) (!empty($currentResponse['program_studi']) ? $currentResponse['program_studi'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><?= esc((string) (!empty($currentResponse['kelas']) ? $currentResponse['kelas'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>Semester/Pertemuan</th>
                    <td><?= esc((string) (!empty($currentResponse['semester']) ? $currentResponse['semester'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>Instansi</th>
                    <td><?= esc((string) (!empty($currentResponse['instansi']) ? $currentResponse['instansi'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>Bidang/Jabatan</th>
                    <td><?= esc((string) (!empty($currentResponse['bidang_keahlian']) ? $currentResponse['bidang_keahlian'] : '-')) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
    <h3 class="card-title mb-3">Jawaban Skor</h3>

    <?php if (empty($scoreAnswers)): ?>
        <div class="empty-state">Belum ada jawaban skor.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 180px;">Aspek</th>
                        <th>Butir</th>
                        <th style="width: 120px;">Tipe</th>
                        <th style="width: 100px;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scoreAnswers as $answer): ?>
                        <tr>
                            <td class="text-muted"><?= esc((string) ($answer['nomor'] ?? '-')) ?></td>
                            <td><?= esc((string) (!empty($answer['nama_aspek']) ? $answer['nama_aspek'] : '-')) ?></td>
                            <td><?= nl2br(esc((string) ($answer['pernyataan'] ?? '-'))) ?></td>
                            <td><?= esc((string) ($answer['tipe_butir'] ?? '-')) ?></td>
                            <td><span class="fw-semibold"><?= esc((string) ($answer['skor'] ?? '-')) ?></span></td>
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
    <h3 class="card-title mb-3">Jawaban Teks</h3>

    <?php if (empty($textAnswers)): ?>
        <div class="empty-state">Belum ada jawaban teks.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 180px;">Aspek</th>
                        <th>Butir</th>
                        <th>Jawaban Teks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($textAnswers as $answer): ?>
                        <tr>
                            <td class="text-muted"><?= esc((string) ($answer['nomor'] ?? '-')) ?></td>
                            <td><?= esc((string) (!empty($answer['nama_aspek']) ? $answer['nama_aspek'] : '-')) ?></td>
                            <td><?= nl2br(esc((string) ($answer['pernyataan'] ?? '-'))) ?></td>
                            <td><?= nl2br(esc((string) (!empty($answer['jawaban_teks']) ? $answer['jawaban_teks'] : '-'))) ?></td>
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
    <h3 class="card-title mb-3">Komentar</h3>

    <?php if (empty($commentAnswers)): ?>
        <div class="text-muted mb-3">Tidak ada komentar per butir.</div>
    <?php else: ?>
        <div class="table-responsive mb-3">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 180px;">Aspek</th>
                        <th>Butir</th>
                        <th>Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commentAnswers as $answer): ?>
                        <tr>
                            <td class="text-muted"><?= esc((string) ($answer['nomor'] ?? '-')) ?></td>
                            <td><?= esc((string) (!empty($answer['nama_aspek']) ? $answer['nama_aspek'] : '-')) ?></td>
                            <td><?= nl2br(esc((string) ($answer['pernyataan'] ?? '-'))) ?></td>
                            <td><?= nl2br(esc((string) (!empty($answer['komentar']) ? $answer['komentar'] : '-'))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div>
        <div class="fw-semibold mb-1">Komentar Umum</div>
        <div class="text-muted"><?= nl2br(esc((string) (!empty($currentResponse['komentar_umum']) ? $currentResponse['komentar_umum'] : '-'))) ?></div>
    </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-2">Kesimpulan</h3>
        <div class="fw-semibold"><?= esc((string) (!empty($currentResponse['kesimpulan']) ? $currentResponse['kesimpulan'] : '-')) ?></div>
    </div>
</div>

<a href="<?= base_url('admin/submissions?mode=' . ($currentResponse['mode'] ?? '')) ?>" class="btn btn-light">Kembali</a>

<?= $this->endSection() ?>
