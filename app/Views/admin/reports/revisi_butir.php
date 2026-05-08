<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Laporan Revisi Butir Instrumen</h2>
            <div class="text-muted mt-1">
                Riwayat perubahan redaksi butir berdasarkan hasil validasi, komentar validator, atau keputusan peneliti.
            </div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-light">Kembali</a>
        </div>
    </div>
</div>

<div class="card">
    <h3>Riwayat Revisi Butir</h3>
    <p>
        Laporan ini memuat perubahan redaksi butir instrumen berdasarkan hasil validasi,
        komentar validator, atau keputusan peneliti.
    </p>
</div>

<?php if (empty($revisions)): ?>
    <div class="empty-state">
        Belum ada riwayat revisi butir.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Instrumen</th>
                <th>Aspek</th>
                <th>Butir</th>
                <th>Pernyataan Lama</th>
                <th>Pernyataan Baru</th>
                <th>Alasan Revisi</th>
                <th>Sumber Revisi</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($revisions as $index => $revision): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <strong><?= esc($revision['kode']) ?></strong><br>
                        <?= esc($revision['judul']) ?>
                    </td>
                    <td><?= esc($revision['nama_aspek']) ?></td>
                    <td>Butir <?= esc($revision['nomor']) ?></td>
                    <td><?= nl2br(esc($revision['pernyataan_lama'])) ?></td>
                    <td><?= nl2br(esc($revision['pernyataan_baru'])) ?></td>
                    <td><?= nl2br(esc($revision['alasan_revisi'] ?: '-')) ?></td>
                    <td><?= esc($revision['sumber_revisi'] ?: '-') ?></td>
                    <td><?= esc($revision['tanggal_revisi'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
