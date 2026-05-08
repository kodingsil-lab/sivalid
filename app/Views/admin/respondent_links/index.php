<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Link Instrumen Responden</h2>
            <div class="text-muted mt-1">Kelola link pengisian untuk mahasiswa, observasi, FGD, dan tes kinerja.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/respondent-links/new' . (!empty($mode) ? '?mode=' . $mode : '')) ?>" class="btn btn-primary">
                + Buat Link
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
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
    </form>
    </div>
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
<div class="card">
    <div class="card-body p-0">
    <div class="table-responsive">
    <table class="table table-vcenter table-hover table-sm">
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
                <th class="table-actions-cell">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $index => $link): ?>
                <?php $publicUrl = base_url('isi/' . $link['token']); ?>

                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <span class="badge badge-status-draft">
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
                        <span class="<?= esc(status_badge_class($link['status'] ?? '')) ?>"><?= esc($link['status']) ?></span>
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
                    <td class="table-actions-cell">
                        <div class="table-actions">
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
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
