<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title"><?= esc($title) ?></h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-error">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <form action="<?= esc($action) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <?php if ($method === 'put'): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-row">
                <label for="kode">Kode Produk</label>
                <input
                    type="text"
                    name="kode"
                    id="kode"
                    class="form-control"
                    value="<?= old('kode', $product['kode'] ?? '') ?>"
                    placeholder="Contoh: PRD-001"
                    required
                >
            </div>

            <div class="form-row">
                <label for="jenis_produk">Jenis Produk</label>
                <?php
                $jenisOptions = [
                    'Buku Model',
                    'Buku Ajar',
                    'Materi Ajar',
                    'Panduan Pembelajaran',
                    'E-Learning',
                    'Rubrik',
                    'Template Artikel',
                    'Produk Lainnya',
                ];

                $selectedJenis = old('jenis_produk', $product['jenis_produk'] ?? '');
                ?>

                <select name="jenis_produk" id="jenis_produk" class="form-control" required>
                    <option value="">-- Pilih Jenis Produk --</option>
                    <?php foreach ($jenisOptions as $jenis): ?>
                        <option value="<?= esc($jenis) ?>" <?= $selectedJenis === $jenis ? 'selected' : '' ?>>
                            <?= esc($jenis) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label for="nama_produk">Nama Produk</label>
            <input
                type="text"
                name="nama_produk"
                id="nama_produk"
                class="form-control"
                value="<?= old('nama_produk', $product['nama_produk'] ?? '') ?>"
                placeholder="Contoh: Buku Model Pembelajaran Menulis Artikel Ilmiah"
                required
            >
        </div>

        <div class="form-row">
            <label for="deskripsi">Deskripsi Produk</label>
            <textarea
                name="deskripsi"
                id="deskripsi"
                class="form-control"
                placeholder="Tuliskan deskripsi singkat produk penelitian."
            ><?= old('deskripsi', $product['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label for="file_produk">Upload File Produk</label>
                <input
                    type="file"
                    name="file_produk"
                    id="file_produk"
                    class="form-control"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                >

                <?php if (!empty($product['file_produk'])): ?>
                    <small>File saat ini: <?= esc($product['file_produk']) ?></small>
                <?php else: ?>
                    <small>Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP. Maksimal 10 MB.</small>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label for="link_produk">Link Produk</label>
                <input
                    type="url"
                    name="link_produk"
                    id="link_produk"
                    class="form-control"
                    value="<?= old('link_produk', $product['link_produk'] ?? '') ?>"
                    placeholder="Contoh: https://elearning.disertasi.web.id/..."
                >
            </div>
        </div>

        <div class="form-row">
            <label for="status">Status Produk</label>
            <?php
            $statusOptions = [
                'Draft',
                'Aktif',
                'Dalam Validasi Produk',
                'Perlu Revisi',
                'Layak',
                'Tidak Aktif',
                'Arsip',
            ];

            $selectedStatus = old('status', $product['status'] ?? 'Draft');

            if ($selectedStatus !== '' && !in_array($selectedStatus, $statusOptions, true)) {
                array_unshift($statusOptions, $selectedStatus);
            }
            ?>

            <select name="status" id="status" class="form-control" required>
                <?php foreach ($statusOptions as $status): ?>
                    <option value="<?= esc($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
                        <?= esc($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Instrumen Validasi yang Dihubungkan</label>

            <?php if (empty($instruments)): ?>
                <div class="empty-state">
                    Belum ada instrumen validasi. Silakan buat instrumen terlebih dahulu pada menu Master Instrumen.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">Pilih</th>
                            <th>Kode</th>
                            <th>Judul Instrumen</th>
                            <th>Jenis</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instruments as $instrument): ?>
                            <?php
                            $checked = in_array((int) $instrument['id'], $selectedInstruments ?? [], true);
                            ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="instrument_ids[]"
                                        value="<?= $instrument['id'] ?>"
                                        <?= $checked ? 'checked' : '' ?>
                                    >
                                </td>
                                <td><?= esc($instrument['kode']) ?></td>
                                <td><?= esc($instrument['judul']) ?></td>
                                <td><?= esc($instrument['jenis']) ?></td>
                                <td><span class="badge"><?= esc($instrument['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/products') ?>" class="btn btn-light">Kembali</a>
    </form>
</div>

<?= $this->endSection() ?>
