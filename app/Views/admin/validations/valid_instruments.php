<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Instrumen Valid</h2>
            <div class="text-muted mt-1">
                Instrumen yang sudah melewati proses validasi dan siap digunakan untuk validasi produk atau pengisian responden.
            </div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/products') ?>" class="btn btn-light">
                Data Produk
            </a>
        </div>
    </div>
</div>

<?php if (empty($instruments)): ?>
    <div class="empty-state">
        Belum ada instrumen yang berstatus Valid.
    </div>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-vcenter table-hover table-sm">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode</th>
                <th>Judul Instrumen</th>
                <th>Jenis</th>
                <th>Sasaran</th>
                <th>Skala</th>
                <th>Status</th>
                <th class="table-actions-cell">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($instruments as $index => $instrument): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($instrument['kode']) ?></td>
                    <td><?= esc($instrument['judul']) ?></td>
                    <td><?= esc($instrument['jenis']) ?></td>
                    <td><?= esc($instrument['sasaran'] ?: '-') ?></td>
                    <td><?= esc($instrument['skala_min']) ?> - <?= esc($instrument['skala_max']) ?></td>
                    <td>
                        <span class="<?= esc(status_badge_class($instrument['status'] ?? '')) ?>"><?= esc($instrument['status']) ?></span>
                    </td>
                    <td class="table-actions-cell">
                        <div class="table-actions">
                            <a href="<?= base_url('admin/instruments/' . $instrument['id']) ?>" class="btn btn-light">
                                Detail
                            </a>

                            <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrument['id']) ?>" class="btn btn-light">
                                Butir
                            </a>

                            <a href="<?= base_url('admin/products') ?>" class="btn btn-primary">
                                Pakai untuk Produk
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
