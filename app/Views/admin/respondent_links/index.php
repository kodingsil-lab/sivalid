<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Link Instrumen Responden</h1>

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
    <form action="<?= base_url('admin/respondent-links') ?>" method="get" class="search-form">
        <select name="mode" class="form-control" style="min-width: 280px;">
            <option value="">-- Semua Mode --</option>
            <?php foreach ($allowedModes as $modeOption): ?>
                <option value="<?= esc($modeOption) ?>" <?= ($mode ?? '') === $modeOption ? 'selected' : '' ?>>
                    <?= esc(str_replace('_', ' ', strtoupper($modeOption))) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>

        <a href="<?= base_url('admin/respondent-links/new' . (!empty($mode) ? '?mode=' . $mode : '')) ?>" class="btn btn-primary">
            + Buat Link
        </a>
    </form>
</div>

<div class="toolbar">
    <a href="<?= base_url('admin/respondent-links?mode=respon_mahasiswa') ?>" class="btn btn-light">Angket Mahasiswa</a>
    <a href="<?= base_url('admin/respondent-links?mode=observasi') ?>" class="btn btn-light">Observasi</a>
    <a href="<?= base_url('admin/respondent-links?mode=fgd') ?>" class="btn btn-light">FGD</a>
    <a href="<?= base_url('admin/respondent-links?mode=tes_kinerja') ?>" class="btn btn-light">Tes Kinerja</a>
</div>

<?php if (empty($links)): ?>
    <div class="empty-state">
        Belum ada link instrumen responden.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Judul Link</th>
                <th>Mode</th>
                <th>Instrumen</th>
                <th>Sasaran</th>
                <th>Status</th>
                <th>Respon</th>
                <th>Link Publik</th>
                <th style="width: 220px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $index => $link): ?>
                <?php $publicUrl = base_url('isi/' . $link['token']); ?>

                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <span class="badge">
                            <?= esc($link['mode']) ?>
                        </span>
                    </td>
                    <td>
                        <strong><?= esc($link['kode']) ?></strong><br>
                        <?= esc($link['judul']) ?><br>
                        <small>Status Instrumen: <?= esc($link['instrument_status']) ?></small>
                    </td>
                    <td><?= esc($link['sasaran'] ?: '-') ?></td>
                    <td>
                        <span class="badge"><?= esc($link['status']) ?></span>
                    </td>
                    <td>
                        <?= esc($link['jumlah_respon'] ?? 0) ?>
                        <?php if (!empty($link['maksimal_respon'])): ?>
                            / <?= esc($link['maksimal_respon']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input
                            type="text"
                            value="<?= esc($publicUrl) ?>"
                            class="form-control"
                            readonly
                            onclick="this.select();"
                        >
                        <small>Klik kotak link lalu salin.</small>
                    </td>
                    <td>
                        <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-light">
                            Buka
                        </a>

                        <a href="<?= base_url('admin/respondent-links/' . $link['id'] . '/edit') ?>" class="btn btn-warning">
                            Edit
                        </a>

                        <form
                            action="<?= base_url('admin/respondent-links/' . $link['id']) ?>"
                            method="post"
                            class="action-inline"
                            onsubmit="return confirm('Yakin ingin menghapus link ini?')"
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