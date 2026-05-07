<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title"><?= esc($title) ?></h1>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-error">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <?php if ($user): ?>
        <form method="post" action="<?= base_url('admin/admin-users/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
    <?php else: ?>
        <form method="post" action="<?= base_url('admin/admin-users') ?>">
            <?= csrf_field() ?>
    <?php endif; ?>

        <div class="form-row">
            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control"
                value="<?= old('name', $user['name'] ?? '') ?>"
                required
            >
        </div>

        <?php if (!$user): ?>
        <div class="form-row">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                value="<?= old('email') ?>"
                required
            >
        </div>
        <?php else: ?>
        <div class="form-row">
            <label>Email</label>
            <input type="text" class="form-control" value="<?= esc($user['email']) ?>" disabled>
        </div>
        <?php endif; ?>

        <div class="form-row">
            <label for="password">
                Password <?= $user ? '<span class="text-muted">(kosongkan jika tidak diubah)</span>' : '<span class="text-danger">*</span>' ?>
            </label>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                autocomplete="new-password"
                <?= !$user ? 'required' : '' ?>
            >
        </div>

        <div class="form-row">
            <label for="password_confirm">Konfirmasi Password <?= !$user ? '<span class="text-danger">*</span>' : '' ?></label>
            <input
                type="password"
                name="password_confirm"
                id="password_confirm"
                class="form-control"
                autocomplete="new-password"
                <?= !$user ? 'required' : '' ?>
            >
        </div>

        <div style="display:flex; gap:1rem; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-light">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
