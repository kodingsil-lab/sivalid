<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Manajemen User Admin</h1>

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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h3 style="margin:0;">Daftar User Admin</h3>
        <a href="<?= base_url('admin/admin-users/new') ?>" class="btn btn-primary">+ Tambah User</a>
    </div>

    <?php if (empty($users)): ?>
        <p class="text-muted">Belum ada user admin.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $user): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($user['name']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['role']) ?></td>
                        <td>
                            <span class="badge badge-<?= $user['status'] === 'aktif' ? 'success' : 'danger' ?>">
                                <?= esc($user['status']) ?>
                            </span>
                        </td>
                        <td><?= esc($user['created_at'] ?? '-') ?></td>
                        <td>
                            <a href="<?= base_url('admin/admin-users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-light">Edit</a>

                            <form method="post" action="<?= base_url('admin/admin-users/' . $user['id'] . '/toggle-status') ?>" style="display:inline;" onsubmit="return confirm('Ubah status user ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm <?= $user['status'] === 'aktif' ? 'btn-warning' : 'btn-success' ?>">
                                    <?= $user['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
