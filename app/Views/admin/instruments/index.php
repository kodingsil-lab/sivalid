<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Master Instrumen</h2>
                <div class="text-muted">Kelola data instrumen penelitian dan status validasinya.</div>
            </div>
            <div class="col-auto ms-auto d-flex gap-2">
                <a href="<?= base_url('admin/instruments/new') ?>" class="btn btn-primary">
                    + Tambah Instrumen
                </a>
                <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-light">
                    Instrumen Valid
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc((string) session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form action="<?= base_url('admin/instruments') ?>" method="get" class="search-form">
            <input
                type="text"
                name="keyword"
                value="<?= esc((string) ($keyword ?? '')) ?>"
                placeholder="Cari kode, judul, jenis, sasaran, status..."
            >
            <button type="submit" class="btn btn-light btn-sm">Cari</button>

            <?php if (!empty($keyword)): ?>
                <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card card-no-hover instrument-table-card">
    <?php if (empty($instruments)): ?>
        <div class="card-body">
            <div class="empty-state">
                Belum ada data instrumen.
            </div>
        </div>
    <?php else: ?>
        <?php
        $currentPage = isset($pager) ? $pager->getCurrentPage($pagerGroup) : 1;
        $perPage = isset($pager) ? $pager->getPerPage($pagerGroup) : 0;
        $total = isset($pager) ? $pager->getTotal($pagerGroup) : count($instruments);
        $offset = $perPage > 0 ? (($currentPage - 1) * $perPage) : 0;
        $firstItem = $total > 0 && $perPage > 0 ? $offset + 1 : 0;
        $lastItem = $total > 0 && $perPage > 0 ? min($currentPage * $perPage, $total) : $total;
        ?>
        <div class="table-responsive instruments-table-wrap">
            <table class="table table-vcenter table-hover table-sm table-nowrap card-table instruments-table">
                <thead>
                    <tr>
                        <th class="col-no" scope="col">No</th>
                        <th class="col-code" scope="col">Kode</th>
                        <th class="col-title" scope="col">Judul Instrumen</th>
                        <th class="col-type" scope="col">Jenis</th>
                        <th class="col-target" scope="col">Sasaran</th>
                        <th class="col-scale" scope="col">Skala</th>
                        <th class="col-status" scope="col">Status</th>
                        <th class="col-actions table-actions-cell" scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($instruments as $index => $instrument): ?>
                        <tr>
                            <td class="text-muted col-no"><?= $offset + $index + 1 ?></td>
                            <td class="col-code"><span class="fw-semibold"><?= esc((string) ($instrument['kode'] ?? '-')) ?></span></td>
                            <td class="col-title">
                                <span class="instrument-title"><?= esc((string) ($instrument['judul'] ?? '-')) ?></span>
                            </td>
                            <td class="text-muted col-type"><?= esc(title_case_label((string) ($instrument['jenis'] ?? '-'))) ?></td>
                            <td class="text-muted col-target"><?= esc((string) (!empty($instrument['sasaran']) ? $instrument['sasaran'] : '-')) ?></td>
                            <td class="text-muted col-scale"><?= esc((string) ($instrument['skala_min'] ?? '-')) ?> - <?= esc((string) ($instrument['skala_max'] ?? '-')) ?></td>
                            <td class="col-status">
                                <?php $status = (string) ($instrument['status'] ?? ''); ?>

                                <span class="<?= esc(status_badge_class($status)) ?>">
                                    <?= esc(status_display_label($status)) ?>
                                </span>
                            </td>
                            <td class="col-actions table-actions-cell">
                                <div class="table-actions">
                                    <a href="<?= base_url('admin/instruments/' . $instrument['id']) ?>" class="btn btn-sm btn-light">
                                        Detail
                                    </a>

                                    <a href="<?= base_url('admin/instruments/' . $instrument['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <?php
                                    $usageCounts = $instrument['usage_counts'] ?? ['aspects' => 0, 'indicators' => 0, 'items' => 0];
                                    $canDelete = (bool) ($instrument['can_delete'] ?? false);
                                    $deleteTitle = 'Tidak bisa dihapus karena masih memiliki '
                                        . (int) ($usageCounts['aspects'] ?? 0) . ' aspek, '
                                        . (int) ($usageCounts['indicators'] ?? 0) . ' indikator, dan '
                                        . (int) ($usageCounts['items'] ?? 0) . ' butir.';
                                    ?>

                                    <?php if ($canDelete): ?>
                                        <form
                                            action="<?= base_url('admin/instruments/' . $instrument['id']) ?>"
                                            method="post"
                                            class="action-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus instrumen ini?')"
                                        >
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-danger" disabled title="<?= esc($deleteTitle) ?>">
                                            Hapus
                                        </button>
                                    <?php endif; ?>
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
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
