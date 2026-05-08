<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$currentInstrument = isset($instrument) && is_array($instrument) ? $instrument : [];
$instrumentId = (int) ($currentInstrument['id'] ?? 0);
$status = (string) ($currentInstrument['status'] ?? '');
$statusClass = 'badge badge-status-draft';

if ($status === 'Valid') {
    $statusClass = 'badge badge-status-success';
} elseif (in_array($status, ['Perlu Revisi', 'Dalam Validasi Instrumen'], true)) {
    $statusClass = 'badge badge-status-warning';
} elseif (in_array($status, ['Ditutup', 'Tidak Aktif'], true)) {
    $statusClass = 'badge badge-status-danger';
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h1 class="page-title mb-1">Detail Instrumen</h1>
        <div class="text-muted">Informasi lengkap instrumen untuk proses validasi penelitian.</div>
    </div>
    <span class="<?= esc($statusClass) ?>">
        <?= esc($status !== '' ? $status : '-') ?>
    </span>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Identitas Instrumen</h3>

        <div class="table-responsive">
            <table class="table table-vcenter">
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
                        <td><?= esc((string) ($currentInstrument['jenis'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Sasaran</th>
                        <td><?= esc((string) (!empty($currentInstrument['sasaran']) ? $currentInstrument['sasaran'] : '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="<?= esc($statusClass) ?>">
                                <?= esc($status !== '' ? $status : '-') ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
                                <?= esc((string) ($currentInstrument['skala_min'] ?? '-')) ?> - <?= esc((string) ($currentInstrument['skala_max'] ?? '-')) ?>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Pengantar, Petunjuk, dan Skala</h3>

        <div class="table-responsive mb-3">
            <table class="table table-vcenter">
                <tbody>
                    <tr>
                        <th style="width: 240px;">Skala Penilaian</th>
                        <td>
                            <?= esc((string) ($instrument['skala_min'] ?? '-')) ?> - <?= esc((string) ($instrument['skala_max'] ?? '-')) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td><?= nl2br(esc((string) (!empty($currentInstrument['deskripsi']) ? $currentInstrument['deskripsi'] : '-'))) ?></td>
                    </tr>
                    <tr>
                        <th>Pengantar</th>
                        <td><?= nl2br(esc((string) (!empty($currentInstrument['pengantar']) ? $currentInstrument['pengantar'] : '-'))) ?></td>
                    </tr>
                    <tr>
                        <th>Petunjuk Pengisian</th>
                        <td><?= nl2br(esc((string) (!empty($currentInstrument['petunjuk']) ? $currentInstrument['petunjuk'] : '-'))) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-muted">
            Setelah data instrumen ditetapkan, tahap berikutnya adalah menyusun kisi-kisi, aspek, indikator, dan butir pernyataan.
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
    <a href="<?= base_url('admin/instruments/' . $instrumentId . '/edit') ?>" class="btn btn-warning">Edit</a>
    <a href="<?= base_url('admin/instrument-aspects?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
        Kelola Kisi-Kisi
    </a>
    <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
        Kelola Butir
    </a>
    <a href="<?= base_url('admin/instrument-links/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
        Buat Link Validasi Instrumen
    </a>
    <?php if (($currentInstrument['status'] ?? '') === 'Valid'): ?>
        <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-light">
            Lihat Instrumen Valid
        </a>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
