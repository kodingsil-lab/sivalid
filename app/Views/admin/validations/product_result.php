<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title ?? 'Analisis Validasi Produk') ?></h2>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="toolbar">
    <a href="<?= base_url('admin/validasi-produk/new') ?>" class="btn btn-primary">
        + Buat Link Validasi Produk
    </a>

    <a href="<?= base_url('admin/products') ?>" class="btn btn-light">
        Data Produk
    </a>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Daftar Link Validasi Produk</h3>
    </div>
    <div class="card-body">
        <p class="text-muted mb-0">Pilih link validasi produk yang sudah memiliki respon validator, lalu proses analisis.</p>
    </div>
</div>

<?php if (empty($links)): ?>
    <div class="empty-state">
        Belum ada link validasi produk.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-vcenter">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Judul Link</th>
                <th>Produk</th>
                <th>Instrumen</th>
                <th>Status Link</th>
                <th style="width: 90px;">Respon</th>
                <th>Analisis Terakhir</th>
                <th class="table-actions-cell">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $index => $link): ?>
                <?php $publicUrl = base_url('isi/' . $link['token']); ?>

                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <?php if (!empty($link['nama_produk'])): ?>
                            <strong><?= esc($link['product_kode']) ?></strong><br>
                            <?= esc($link['nama_produk']) ?><br>
                            <small><?= esc($link['jenis_produk']) ?></small>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= esc($link['kode']) ?></strong><br>
                        <?= esc($link['judul']) ?>
                    </td>
                    <td>
                        <span class="<?= esc(status_badge_class($link['status'] ?? '')) ?>"><?= esc($link['status']) ?></span>
                    </td>
                    <td>
                        <?= esc($link['jumlah_respon'] ?? 0) ?>
                        <?php if (!empty($link['maksimal_respon'])): ?>
                            / <?= esc($link['maksimal_respon']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($link['analysis'])): ?>
                            <strong><?= esc($link['analysis']['persentase']) ?>%</strong><br>
                            <?= esc($link['analysis']['kategori']) ?>
                        <?php else: ?>
                            <span class="badge badge-status-draft">Belum dianalisis</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-actions-cell">
                        <div class="table-actions">
                            <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-light">
                                Buka Link
                            </a>

                            <form
                                action="<?= base_url('admin/validasi-produk/proses/' . $link['id']) ?>"
                                method="post"
                                class="action-inline"
                            >
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary">
                                    Analisis
                                </button>
                            </form>

                            <?php if (!empty($link['analysis'])): ?>
                                <a href="<?= base_url('admin/validasi-produk/analisis/' . $link['analysis']['id']) ?>" class="btn btn-light">
                                    Lihat
                                </a>
                            <?php endif; ?>

                            <a href="<?= base_url('admin/validasi-produk/' . $link['id'] . '/edit') ?>" class="btn btn-warning">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
        </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
