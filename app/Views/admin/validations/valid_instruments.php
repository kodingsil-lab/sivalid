<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Instrumen Valid</h1>

<div class="card">
    <h3>Daftar Instrumen yang Sudah Valid</h3>
    <p>
        Instrumen pada halaman ini sudah melewati proses validasi instrumen dan dapat digunakan
        untuk validasi produk atau pengisian responden.
    </p>
</div>

<?php if (empty($instruments)): ?>
    <div class="empty-state">
        Belum ada instrumen yang berstatus Valid.
    </div>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-vcenter table-hover">
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
                        <span class="badge"><?= esc($instrument['status']) ?></span>
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
