<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Produk Penelitian</h1>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="toolbar">
    <a href="<?= base_url('admin/products/new') ?>" class="btn btn-primary">
        + Tambah Produk
    </a>

    <form action="<?= base_url('admin/products') ?>" method="get" class="search-form">
        <input
            type="text"
            name="keyword"
            value="<?= esc($keyword ?? '') ?>"
            placeholder="Cari kode, nama produk, jenis, status..."
        >
        <button type="submit" class="btn btn-light">Cari</button>

        <?php if (!empty($keyword)): ?>
            <a href="<?= base_url('admin/products') ?>" class="btn btn-light">Reset</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">
        Belum ada data produk penelitian.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Jenis</th>
                <th>File</th>
                <th>Link</th>
                <th>Status</th>
                <th style="width: 220px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $index => $product): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($product['kode']) ?></td>
                    <td><?= esc($product['nama_produk']) ?></td>
                    <td><?= esc($product['jenis_produk']) ?></td>
                    <td>
                        <?php if (!empty($product['file_produk'])): ?>
                            <span class="badge">Ada file</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($product['link_produk'])): ?>
                            <a href="<?= esc($product['link_produk']) ?>" target="_blank">Buka</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge"><?= esc($product['status']) ?></span>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/products/' . $product['id']) ?>" class="btn btn-light">
                            Detail
                        </a>

                        <a href="<?= base_url('admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-warning">
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
                            <button type="submit" class="btn btn-danger">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>