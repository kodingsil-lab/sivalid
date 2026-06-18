<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Link Penyebaran Instrumen</h2>
            <div class="text-muted mt-1">Kelola link untuk menyebarkan instrumen valid kepada sasaran yang ditentukan.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/respondent-links/new') ?>" class="btn btn-primary">
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

<?php if (empty($links)): ?>
    <div class="empty-state">
        Belum ada link penyebaran instrumen.
    </div>
<?php else: ?>
<?php
$currentPage = isset($pager) ? $pager->getCurrentPage($pagerGroup) : 1;
$perPage = isset($pager) ? $pager->getPerPage($pagerGroup) : 0;
$total = isset($pager) ? $pager->getTotal($pagerGroup) : count($links);
$offset = $perPage > 0 ? (($currentPage - 1) * $perPage) : 0;
$firstItem = $total > 0 && $perPage > 0 ? $offset + 1 : 0;
$lastItem = $total > 0 && $perPage > 0 ? min($currentPage * $perPage, $total) : $total;
?>
<div class="card">
    <div class="card-body p-0">
    <div class="table-responsive">
    <table class="table table-vcenter table-hover table-sm">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Judul Link</th>
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
                    <td><?= $offset + $index + 1 ?></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <strong><?= esc($link['kode']) ?></strong><br>
                        <?= esc($link['judul']) ?><br>
                        <small>Status Instrumen: <?= esc(status_display_label((string) ($link['instrument_status'] ?? ''))) ?></small>
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
    <?php if (isset($pager) && !empty($pagerGroup)): ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 px-3 py-3 border-top">
            <div class="text-muted small">
                Menampilkan <?= esc((string) $firstItem) ?> sampai <?= esc((string) $lastItem) ?> dari <?= esc((string) $total) ?> entri
            </div>
            <div><?= $pager->links($pagerGroup, 'default_full') ?></div>
        </div>
    <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
