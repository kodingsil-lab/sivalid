<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Detail Produk Penelitian</h1>

<div class="card">
    <h3><?= esc($product['nama_produk']) ?></h3>

    <table>
        <tr>
            <th style="width: 220px;">Kode Produk</th>
            <td><?= esc($product['kode']) ?></td>
        </tr>
        <tr>
            <th>Jenis Produk</th>
            <td><?= esc($product['jenis_produk']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="badge"><?= esc($product['status']) ?></span></td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td><?= nl2br(esc($product['deskripsi'] ?: '-')) ?></td>
        </tr>
        <tr>
            <th>File Produk</th>
            <td>
                <?php if (!empty($product['file_produk'])): ?>
                    <?= esc($product['file_produk']) ?>
                    <br>
                    <a href="<?= base_url('admin/products/download/' . $product['id']) ?>" class="btn btn-light">
                        Download File
                    </a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Link Produk</th>
            <td>
                <?php if (!empty($product['link_produk'])): ?>
                    <a href="<?= esc($product['link_produk']) ?>" target="_blank">
                        <?= esc($product['link_produk']) ?>
                    </a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Instrumen Validasi yang Dihubungkan</h3>

    <?php if (empty($productInstruments)): ?>
        <div class="empty-state">
            Produk ini belum dihubungkan dengan instrumen validasi.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Kode</th>
                    <th>Judul Instrumen</th>
                    <th>Jenis</th>
                    <th>Status Instrumen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productInstruments as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($row['kode']) ?></td>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc($row['jenis']) ?></td>
                        <td><span class="badge"><?= esc($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Tahap Berikutnya</h3>
    <p>
        Setelah produk penelitian dibuat dan dihubungkan dengan instrumen,
        tahap berikutnya adalah membuat link validasi produk agar validator
        dapat menilai produk menggunakan instrumen yang sudah disiapkan.
    </p>
</div>

<a href="<?= base_url('admin/products') ?>" class="btn btn-light">Kembali</a>
<a href="<?= base_url('admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-warning">Edit</a>
<a href="<?= base_url('admin/validasi-produk/new?product_id=' . $product['id']) ?>" class="btn btn-primary">
    Buat Link Validasi Produk
</a>

<?= $this->endSection() ?>
