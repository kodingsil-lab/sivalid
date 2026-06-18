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

<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info">
        <?= esc((string) session()->getFlashdata('info')) ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form action="<?= base_url('admin/instruments') ?>" method="get" class="search-form">
            <input
                type="text"
                name="keyword"
                value="<?= esc((string) ($keyword ?? '')) ?>"
                placeholder="Cari kode, judul, jenis, sasaran, keterangan, status..."
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
                        <th class="col-note" scope="col">Keterangan</th>
                        <th class="col-scale" scope="col">Skala</th>
                        <th class="col-status" scope="col">Status</th>
                        <th class="col-actions table-actions-cell" scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody
                    id="instrument-sortable-body"
                    data-reorder-url="<?= base_url('admin/instruments/reorder') ?>"
                    data-offset="<?= esc((string) $offset) ?>"
                    data-csrf-token="<?= csrf_token() ?>"
                    data-csrf-hash="<?= csrf_hash() ?>"
                >
                    <?php foreach ($instruments as $index => $instrument): ?>
                        <tr data-id="<?= esc((string) $instrument['id']) ?>">
                            <td class="text-muted col-no">
                                <span class="drag-handle" title="Geser untuk mengubah urutan" aria-label="Geser untuk mengubah urutan">
                                    <span></span><span></span><span></span>
                                </span>
                                <span class="row-number"><?= $offset + $index + 1 ?></span>
                            </td>
                            <td class="col-code"><span class="fw-semibold"><?= esc((string) ($instrument['kode'] ?? '-')) ?></span></td>
                            <td class="col-title">
                                <span class="instrument-title"><?= esc((string) ($instrument['judul'] ?? '-')) ?></span>
                            </td>
                            <td class="text-muted col-type"><?= esc(title_case_label((string) ($instrument['jenis'] ?? '-'))) ?></td>
                            <td class="text-muted col-target"><?= esc((string) (!empty($instrument['sasaran']) ? $instrument['sasaran'] : '-')) ?></td>
                            <td class="text-muted col-note"><?= esc((string) (!empty($instrument['keterangan']) ? $instrument['keterangan'] : '-')) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<style>
    .drag-handle {
        align-items: center;
        cursor: grab;
        display: inline-grid;
        gap: 2px;
        grid-template-columns: repeat(3, 3px);
        margin-right: 10px;
        padding: 6px 3px;
        vertical-align: middle;
    }

    .drag-handle span {
        background: #7b8aa0;
        border-radius: 50%;
        display: block;
        height: 3px;
        width: 3px;
    }

    .sortable-ghost {
        background: #eef6ff;
        opacity: 0.75;
    }

    .sortable-chosen .drag-handle {
        cursor: grabbing;
    }

    .instrument-sort-saving {
        opacity: 0.7;
        pointer-events: none;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tbody = document.getElementById('instrument-sortable-body');

        if (!tbody || typeof Sortable === 'undefined') {
            return;
        }

        var offset = parseInt(tbody.getAttribute('data-offset') || '0', 10);
        var reorderUrl = tbody.getAttribute('data-reorder-url');
        var csrfToken = tbody.getAttribute('data-csrf-token');
        var csrfHash = tbody.getAttribute('data-csrf-hash');

        function updateRowNumbers() {
            tbody.querySelectorAll('tr').forEach(function (row, index) {
                var number = row.querySelector('.row-number');

                if (number) {
                    number.textContent = String(offset + index + 1);
                }
            });
        }

        function saveOrder() {
            var formData = new FormData();

            tbody.querySelectorAll('tr[data-id]').forEach(function (row) {
                formData.append('order[]', row.getAttribute('data-id'));
            });

            formData.append('offset', String(offset));
            formData.append(csrfToken, csrfHash);
            tbody.classList.add('instrument-sort-saving');

            fetch(reorderUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok || !payload.success) {
                            throw new Error(payload.message || 'Urutan instrumen gagal disimpan.');
                        }

                        if (payload.csrfHash) {
                            csrfHash = payload.csrfHash;
                            tbody.setAttribute('data-csrf-hash', csrfHash);
                        }
                    });
                })
                .catch(function (error) {
                    alert(error.message);
                    window.location.reload();
                })
                .finally(function () {
                    tbody.classList.remove('instrument-sort-saving');
                });
        }

        Sortable.create(tbody, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                updateRowNumbers();
                saveOrder();
            }
        });
    });
</script>

<?= $this->endSection() ?>
