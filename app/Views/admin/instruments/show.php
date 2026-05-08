<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$currentInstrument = isset($instrument) && is_array($instrument) ? $instrument : [];
$instrumentId = (int) ($currentInstrument['id'] ?? 0);
$status = (string) ($currentInstrument['status'] ?? '');
$statusClass = status_badge_class($status);
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Instrumen</h2>
            <div class="text-muted mt-1">Informasi lengkap instrumen untuk proses validasi penelitian.</div>
        </div>
        <div class="col-auto ms-auto">
            <span class="<?= esc($statusClass) ?>">
                <?= esc($status !== '' ? $status : '-') ?>
            </span>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Identitas Instrumen</h3>

        <div class="table-responsive">
            <table class="table table-vcenter table-sm">
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
            </table>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Pengantar, Petunjuk, dan Skala</h3>

        <div class="table-responsive mb-3">
            <table class="table table-vcenter table-sm">
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
