<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Master Instrumen</h2>
            <div class="text-muted mt-1">Kelola data instrumen penelitian dan status validasinya.</div>
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
                            <td class="text-muted col-no"><?= $index + 1 ?></td>
                            <td class="col-code"><span class="fw-semibold"><?= esc((string) ($instrument['kode'] ?? '-')) ?></span></td>
                            <td class="col-title">
                                <span class="instrument-title"><?= esc((string) ($instrument['judul'] ?? '-')) ?></span>
                            </td>
                            <td class="text-muted col-type"><?= esc((string) ($instrument['jenis'] ?? '-')) ?></td>
                            <td class="text-muted col-target"><?= esc((string) (!empty($instrument['sasaran']) ? $instrument['sasaran'] : '-')) ?></td>
                            <td class="text-muted col-scale"><?= esc((string) ($instrument['skala_min'] ?? '-')) ?> - <?= esc((string) ($instrument['skala_max'] ?? '-')) ?></td>
                            <td class="col-status">
                                <?php $status = (string) ($instrument['status'] ?? ''); ?>

                                <span class="<?= esc(status_badge_class($status)) ?>">
                                    <?= esc($status !== '' ? $status : '-') ?>
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
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
