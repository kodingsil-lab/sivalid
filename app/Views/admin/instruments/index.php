<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Master Instrumen</h1>

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
    <div>
        <a href="<?= base_url('admin/instruments/new') ?>" class="btn btn-primary">
            + Tambah Instrumen
        </a>

        <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-light">
            Instrumen Valid
        </a>
    </div>

    <form action="<?= base_url('admin/instruments') ?>" method="get" class="search-form">
        <input
            type="text"
            name="keyword"
            value="<?= esc($keyword ?? '') ?>"
            placeholder="Cari kode, judul, jenis, sasaran, status..."
        >
        <button type="submit" class="btn btn-light">Cari</button>

        <?php if (!empty($keyword)): ?>
            <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Reset</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($instruments)): ?>
    <div class="empty-state">
        Belum ada data instrumen.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode</th>
                <th>Judul Instrumen</th>
                <th>Jenis</th>
                <th>Sasaran</th>
                <th>Skala</th>
                <th>Status</th>
                <th style="width: 220px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($instruments as $index => $instrument): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($instrument['kode']) ?></td>
                    <td><?= esc($instrument['judul']) ?></td>
                    <td><?= esc($instrument['jenis']) ?></td>
                    <td><?= esc($instrument['sasaran'] ?: '-') ?></td>
                    <td><?= esc($instrument['skala_min']) ?> - <?= esc($instrument['skala_max']) ?></td>
                    <td>
                        <?php
                        $statusClass = 'badge';

                        if ($instrument['status'] === 'Valid') {
                            $statusClass .= ' badge-valid';
                        } elseif (in_array($instrument['status'], ['Perlu Revisi', 'Dalam Validasi Instrumen'], true)) {
                            $statusClass .= ' badge-warning';
                        } elseif (in_array($instrument['status'], ['Ditutup', 'Tidak Aktif'], true)) {
                            $statusClass .= ' badge-danger';
                        }
                        ?>

                        <span class="<?= esc($statusClass) ?>">
                            <?= esc($instrument['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/instruments/' . $instrument['id']) ?>" class="btn btn-light">
                            Detail
                        </a>

                        <a href="<?= base_url('admin/instruments/' . $instrument['id'] . '/edit') ?>" class="btn btn-warning">
                            Edit
                        </a>

                        <form
                            action="<?= base_url('admin/instruments/' . $instrument['id']) ?>"
                            method="post"
                            class="action-inline"
                            onsubmit="return confirm('Yakin ingin menghapus instrumen ini?')"
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
