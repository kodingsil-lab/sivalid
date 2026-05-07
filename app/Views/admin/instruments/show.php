<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Detail Instrumen</h1>

<div class="card">
    <h3><?= esc($instrument['judul']) ?></h3>

    <table>
        <tr>
            <th style="width: 220px;">Kode Instrumen</th>
            <td><?= esc($instrument['kode']) ?></td>
        </tr>
        <tr>
            <th>Jenis Instrumen</th>
            <td><?= esc($instrument['jenis']) ?></td>
        </tr>
        <tr>
            <th>Sasaran</th>
            <td><?= esc($instrument['sasaran'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Skala</th>
            <td><?= esc($instrument['skala_min']) ?> - <?= esc($instrument['skala_max']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="badge"><?= esc($instrument['status']) ?></span></td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td><?= nl2br(esc($instrument['deskripsi'] ?: '-')) ?></td>
        </tr>
        <tr>
            <th>Pengantar</th>
            <td><?= nl2br(esc($instrument['pengantar'] ?: '-')) ?></td>
        </tr>
        <tr>
            <th>Petunjuk Pengisian</th>
            <td><?= nl2br(esc($instrument['petunjuk'] ?: '-')) ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Tahap Berikutnya</h3>
    <p>
        Setelah data instrumen dibuat, tahap berikutnya adalah menyusun
        kisi-kisi instrumen, aspek, indikator, dan butir pernyataan.
    </p>
</div>

<a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
<a href="<?= base_url('admin/instruments/' . $instrument['id'] . '/edit') ?>" class="btn btn-warning">Edit</a>
<a href="<?= base_url('admin/instrument-aspects?instrument_id=' . $instrument['id']) ?>" class="btn btn-primary">
    Kelola Kisi-Kisi
</a>
<a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrument['id']) ?>" class="btn btn-primary">
    Kelola Butir
</a>
<a href="<?= base_url('admin/instrument-links/new?instrument_id=' . $instrument['id']) ?>" class="btn btn-primary">
    Buat Link Validasi Instrumen
</a>
<?php if (($instrument['status'] ?? '') === 'Valid'): ?>
    <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-light">
        Lihat Instrumen Valid
    </a>
<?php endif; ?>

<?= $this->endSection() ?>
