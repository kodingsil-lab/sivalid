<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title) ?></h2>
            <div class="text-muted mt-1">Kelola paket validasi instrumen yang berisi satu atau banyak instrumen dalam satu link validator.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/instrument-bundles/new') ?>" class="btn btn-primary">
                + Buat Paket Validasi Instrumen
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (empty($bundles)): ?>
    <div class="empty-state">
        Belum ada paket validasi instrumen. Buat paket untuk mengirim satu atau beberapa instrumen dalam satu link validator.
    </div>
<?php else: ?>
<?php
$currentPage = isset($pager) ? $pager->getCurrentPage($pagerGroup) : 1;
$perPage = isset($pager) ? $pager->getPerPage($pagerGroup) : 0;
$total = isset($pager) ? $pager->getTotal($pagerGroup) : count($bundles);
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
                <th>Judul Paket</th>
                <th>Instrumen</th>
                <th>Validator</th>
                <th>Status</th>
                <th>Berlaku</th>
                <th>Link Publik</th>
                <th class="table-actions-cell">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bundles as $index => $bundle): ?>
                <?php $publicUrl = base_url('paket/' . $bundle['token']); ?>
                <tr>
                    <td><?= $offset + $index + 1 ?></td>
                    <td>
                        <strong><?= esc($bundle['judul']) ?></strong>
                    </td>
                    <td>
                        <span class="badge bg-blue-lt"><?= (int) $bundle['jumlah_instrumen'] ?> instrumen</span>
                    </td>
                    <td><?= esc($bundle['sasaran'] ?: '-') ?></td>
                    <td>
                        <span class="<?= esc(status_badge_class($bundle['status'] ?? '')) ?>">
                            <?= esc($bundle['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($bundle['tanggal_mulai'])): ?>
                            <?= esc(format_tanggal_indonesia($bundle['tanggal_mulai'])) ?>
                        <?php endif; ?>
                        <?php if (!empty($bundle['tanggal_mulai']) && !empty($bundle['tanggal_selesai'])): ?>
                            &ndash;
                        <?php endif; ?>
                        <?php if (!empty($bundle['tanggal_selesai'])): ?>
                            <?= esc(format_tanggal_indonesia($bundle['tanggal_selesai'])) ?>
                        <?php endif; ?>
                        <?php if (empty($bundle['tanggal_mulai']) && empty($bundle['tanggal_selesai'])): ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td style="min-width: 220px;">
                        <input
                            type="text"
                            value="<?= esc($publicUrl) ?>"
                            class="form-control form-control-sm"
                            readonly
                            onclick="this.select();"
                        >
                        <small class="text-muted">Klik lalu salin.</small>
                    </td>
                    <td class="table-actions-cell">
                        <div class="table-actions">
                            <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-sm btn-light">
                                Buka
                            </a>
                            <a href="<?= base_url('admin/instrument-bundles/' . $bundle['id']) ?>" class="btn btn-sm btn-light">
                                Detail
                            </a>
                            <a href="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/edit') ?>" class="btn btn-sm btn-light">
                                Edit
                            </a>
                            <form action="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/duplicate') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-light"
                                    onclick="return confirm('Duplikat paket ini untuk validator lain?')">
                                    Duplikat
                                </button>
                            </form>
                            <form action="<?= base_url('admin/instrument-bundles/' . $bundle['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus paket ini? Instrumen yang sudah ditambahkan akan ikut terhapus.')">
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
