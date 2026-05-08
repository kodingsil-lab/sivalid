<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Produk Penelitian</h2>
            <div class="text-muted mt-1">Daftar produk yang terkait dengan proses validasi penelitian.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/products/new') ?>" class="btn btn-primary">
                + Tambah Produk
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc((string) session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form action="<?= base_url('admin/products') ?>" method="get" class="search-form">
            <input
                type="text"
                name="keyword"
                value="<?= esc((string) ($keyword ?? '')) ?>"
                placeholder="Cari kode, nama produk, jenis, status..."
            >
            <button type="submit" class="btn btn-light btn-sm">Cari</button>

            <?php if (!empty($keyword)): ?>
                <a href="<?= base_url('admin/products') ?>" class="btn btn-light btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                Belum ada data produk penelitian.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th style="width: 120px;">Kode</th>
                            <th>Nama Produk</th>
                            <th style="width: 160px;">Jenis</th>
                            <th style="width: 130px;">File</th>
                            <th style="width: 120px;">Link</th>
                            <th style="width: 180px;">Status</th>
                            <th class="table-actions-cell">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $index => $product): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td><span class="fw-semibold"><?= esc((string) ($product['kode'] ?? '-')) ?></span></td>
                                <td><?= esc((string) ($product['nama_produk'] ?? '-')) ?></td>
                                <td><?= esc((string) ($product['jenis_produk'] ?? '-')) ?></td>
                                <td>
                                    <?php if (!empty($product['file_produk'])): ?>
                                        <span class="<?= esc(status_badge_class('Ada file')) ?>">Ada file</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($product['link_produk'])): ?>
                                        <a href="<?= esc((string) $product['link_produk']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light">
                                            Buka
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?= esc(status_badge_class($product['status'] ?? '')) ?>">
                                        <?= esc((string) (!empty($product['status']) ? $product['status'] : '-')) ?>
                                    </span>
                                </td>
                                <td class="table-actions-cell">
                                    <div class="table-actions">
                                        <a href="<?= base_url('admin/products/' . $product['id']) ?>" class="btn btn-sm btn-light">
                                            Detail
                                        </a>

                                        <a href="<?= base_url('admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form
                                            action="<?= base_url('admin/products/' . $product['id']) ?>"
                                            method="post"
                                            class="action-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                                        >
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
