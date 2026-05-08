<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Jenis Produk</h2>
            <div class="text-muted mt-1">Kelola daftar jenis yang muncul pada dropdown Jenis Produk di form Produk Penelitian.</div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3">
        <?= esc((string) session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-3">
        <strong>Periksa kembali input berikut:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                <li><?= esc((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Tambah Jenis Baru</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/product-types') ?>" method="post" class="search-form">
            <?= csrf_field() ?>
            <input
                type="text"
                name="jenis"
                class="form-control"
                placeholder="Contoh: Modul Interaktif"
                value="<?= old('jenis') ?>"
                maxlength="100"
                required
            >
            <button type="submit" class="btn btn-primary">Tambah</button>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Daftar Jenis Produk</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Nama Jenis</th>
                        <th style="width: 200px;">Dipakai di Produk</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($types)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada jenis produk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($types as $index => $type): ?>
                            <?php
                            $label = (string) ($type['setting_value'] ?? '');
                            $usedCount = (int) ($usage[$label] ?? 0);
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($label) ?></td>
                                <td>
                                    <span class="badge badge-status-process"><?= $usedCount ?> data</span>
                                </td>
                                <td class="table-actions-cell">
                                    <?php if ($usedCount === 0): ?>
                                        <form action="<?= base_url('admin/product-types/' . (int) $type['id']) ?>" method="post" onsubmit="return confirm('Hapus jenis produk ini?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak bisa dihapus</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
