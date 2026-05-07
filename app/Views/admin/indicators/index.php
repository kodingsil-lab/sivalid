<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Indikator Kisi-Kisi Instrumen</h1>

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
    <form action="<?= base_url('admin/instrument-indicators') ?>" method="get" class="search-form">
        <select name="instrument_id" class="form-control" style="min-width: 420px;">
            <option value="">-- Semua Instrumen --</option>
            <?php foreach ($instruments as $instrument): ?>
                <option value="<?= $instrument['id'] ?>" <?= (int) ($instrumentId ?? 0) === (int) $instrument['id'] ? 'selected' : '' ?>>
                    <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>

        <?php if (!empty($instrumentId)): ?>
            <a href="<?= base_url('admin/instrument-indicators/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                + Tambah Indikator
            </a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($indicators)): ?>
    <div class="empty-state">
        Belum ada indikator.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">Urutan</th>
                <th>Instrumen</th>
                <th>Aspek</th>
                <th>Indikator</th>
                <th style="width: 180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($indicators as $indicator): ?>
                <tr>
                    <td><?= esc($indicator['urutan']) ?></td>
                    <td>
                        <strong><?= esc($indicator['kode']) ?></strong><br>
                        <?= esc($indicator['judul']) ?>
                    </td>
                    <td><?= esc($indicator['nama_aspek']) ?></td>
                    <td><?= nl2br(esc($indicator['indikator'])) ?></td>
                    <td>
                        <a href="<?= base_url('admin/instrument-indicators/' . $indicator['id'] . '/edit') ?>" class="btn btn-warning">
                            Edit
                        </a>

                        <form
                            action="<?= base_url('admin/instrument-indicators/' . $indicator['id']) ?>"
                            method="post"
                            class="action-inline"
                            onsubmit="return confirm('Yakin ingin menghapus indikator ini?')"
                        >
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>