<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$pageTitle = isset($title) ? (string) $title : 'Form Produk';
$formAction = isset($action) ? (string) $action : base_url('admin/products');
$formMethod = isset($method) ? (string) $method : 'post';
$checkedInstruments = isset($selectedInstruments) && is_array($selectedInstruments) ? $selectedInstruments : [];
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($pageTitle) ?></h2>
            <div class="text-muted mt-1">Lengkapi metadata produk sebelum digunakan dalam proses validasi.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/products') ?>" class="btn btn-light">Kembali</a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= esc($formAction) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php if (strtolower($formMethod) === 'put'): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Identitas Produk</h3>
        </div>
        <div class="card-body">

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="kode">Kode Produk</label>
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
                    <label class="form-label" for="jenis_produk">Jenis Produk</label>
                    <?php
                    $jenisOptions = isset($jenisOptions) && is_array($jenisOptions)
                        ? $jenisOptions
                        : [];
                    $selectedJenis = old('jenis_produk', $product['jenis_produk'] ?? '');

                    if ($selectedJenis !== '' && !in_array($selectedJenis, $jenisOptions, true)) {
                        array_unshift($jenisOptions, $selectedJenis);
                    }
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
                <label class="form-label" for="nama_produk">Nama Produk</label>
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
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Deskripsi Produk</h3>
        </div>
        <div class="card-body">

            <div class="form-row">
                <label class="form-label" for="deskripsi">Deskripsi Produk</label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    class="form-control"
                    rows="5"
                    placeholder="Tuliskan deskripsi singkat produk penelitian."
                ><?= old('deskripsi', $product['deskripsi'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">File dan Link Produk</h3>
        </div>
        <div class="card-body">

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="file_produk">Upload File Produk</label>
                    <input
                        type="file"
                        name="file_produk"
                        id="file_produk"
                        class="form-control"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                    >

                    <?php if (!empty($product['file_produk'])): ?>
                        <small class="text-muted">File saat ini: <?= esc((string) $product['file_produk']) ?></small>
                    <?php else: ?>
                        <small class="text-muted">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP. Maksimal 10 MB.</small>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <label class="form-label" for="link_produk">Link Produk</label>
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
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Instrumen Validasi yang Digunakan</h3>
        </div>
        <div class="card-body">

            <?php if (empty($instruments)): ?>
                <div class="empty-state">
                    Belum ada instrumen validasi. Silakan buat instrumen terlebih dahulu pada menu Master Instrumen.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Pilih</th>
                                <th style="width: 120px;">Kode</th>
                                <th>Judul Instrumen</th>
                                <th style="width: 170px;">Jenis</th>
                                <th style="width: 170px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instruments as $instrument): ?>
                                <?php
                                $checked = in_array((int) ($instrument['id'] ?? 0), $checkedInstruments, true);
                                ?>
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="instrument_ids[]"
                                            value="<?= (int) ($instrument['id'] ?? 0) ?>"
                                            <?= $checked ? 'checked' : '' ?>
                                        >
                                    </td>
                                    <td class="fw-semibold"><?= esc((string) ($instrument['kode'] ?? '-')) ?></td>
                                    <td><?= esc((string) ($instrument['judul'] ?? '-')) ?></td>
                                    <td><?= esc((string) ($instrument['jenis'] ?? '-')) ?></td>
                                    <td><span class="<?= esc(status_badge_class($instrument['status'] ?? '')) ?>"><?= esc((string) ($instrument['status'] ?? '-')) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Status</h3>
        </div>
        <div class="card-body">

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="status">Status Produk</label>
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
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-1">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/products') ?>" class="btn btn-light">Kembali</a>
    </div>
</form>

<?= $this->endSection() ?>
