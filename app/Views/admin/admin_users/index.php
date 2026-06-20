<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Manajemen User Admin</h2>
            <div class="text-muted mt-1">Kelola akun admin yang dapat mengakses panel SIVALID.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/admin-users/new') ?>" class="btn btn-primary btn-sm">+ Tambah User</a>
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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar User Admin</h3>
    </div>
    <div class="card-body p-0">
    <?php if (empty($users)): ?>
        <div class="empty-state">Belum ada user admin.</div>
    <?php else: ?>
        <?php
        $currentPage = isset($pager) ? $pager->getCurrentPage($pagerGroup) : 1;
        $perPage = isset($pager) ? $pager->getPerPage($pagerGroup) : 0;
        $total = isset($pager) ? $pager->getTotal($pagerGroup) : count($users);
        $offset = $perPage > 0 ? (($currentPage - 1) * $perPage) : 0;
        $firstItem = $total > 0 && $perPage > 0 ? $offset + 1 : 0;
        $lastItem = $total > 0 && $perPage > 0 ? min($currentPage * $perPage, $total) : $total;
        ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="table-actions-cell">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $user): ?>
                    <tr>
                        <td><?= $offset + $i + 1 ?></td>
                        <td><?= esc($user['name']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['role']) ?></td>
                        <td>
                            <span class="<?= esc(status_badge_class($user['status'] ?? '')) ?>">
                                <?= esc($user['status']) ?>
                            </span>
                        </td>
                        <td><?= esc($user['created_at'] ?? '-') ?></td>
                        <td class="table-actions-cell">
                            <div class="table-actions">
                                <a href="<?= base_url('admin/admin-users/' . $user['id']) ?>" class="btn btn-sm btn-light">Detail</a>
                                <a href="<?= base_url('admin/admin-users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-light">Edit</a>

                                <form method="post" action="<?= base_url('admin/admin-users/' . $user['id'] . '/toggle-status') ?>" class="action-inline" onsubmit="return confirm('Ubah status user ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm <?= $user['status'] === 'aktif' ? 'btn-warning' : 'btn-success' ?>">
                                        <?= $user['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
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
    <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
