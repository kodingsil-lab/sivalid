<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Backup &amp; Restore</h1>

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

<div class="card">
    <h3>Export Database</h3>
    <p>
        Unduh seluruh isi database sebagai file <code>.sql</code>.
        Simpan file ini di tempat yang aman secara berkala.
    </p>
    <a href="<?= base_url('admin/backup/export-database') ?>" class="btn btn-primary">
        Download Database (.sql)
    </a>
</div>

<div class="card">
    <h3>Export File Produk</h3>
    <p>
        Unduh semua file produk yang diunggah sebagai arsip <code>.zip</code>.
        Membutuhkan ekstensi <code>ZipArchive</code> yang aktif di server.
    </p>
    <a href="<?= base_url('admin/backup/export-files') ?>" class="btn btn-primary">
        Download File Produk (.zip)
    </a>
</div>

<div class="card">
    <h3>Panduan Backup Manual</h3>
    <ol>
        <li>
            <strong>Database:</strong> Gunakan tombol <em>Download Database</em> di atas,
            atau buka phpMyAdmin &rarr; pilih database <code>sivalid</code> &rarr; klik <strong>Export</strong>.
        </li>
        <li>
            <strong>File Produk:</strong> Gunakan tombol <em>Download File Produk</em> di atas,
            atau salin folder <code>writable/uploads/products/</code> secara manual via FTP atau cPanel File Manager.
        </li>
        <li>
            <strong>Frekuensi:</strong> Lakukan backup sebelum setiap sesi pengisian data penting atau
            sebelum melakukan pembaruan sistem.
        </li>
        <li>
            <strong>Restore:</strong> Import file <code>.sql</code> kembali melalui phpMyAdmin &rarr;
            pilih database &rarr; klik <strong>Import</strong>. Kembalikan file produk ke folder
            <code>writable/uploads/products/</code>.
        </li>
    </ol>
</div>

<?= $this->endSection() ?>
