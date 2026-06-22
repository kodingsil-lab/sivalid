<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $profile = isset($profile) && is_array($profile) ? $profile : []; ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title) ?></h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-light">Kembali</a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Data User Admin</h3>
    </div>
    <div class="card-body">
    <?php if ($user): ?>
        <form method="post" action="<?= base_url('admin/admin-users/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
    <?php else: ?>
        <form method="post" action="<?= base_url('admin/admin-users') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
    <?php endif; ?>

        <div class="form-row">
            <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
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
            <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
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
            <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
            <?php $selectedRole = old('role', $user['role'] ?? 'admin'); ?>
            <select name="role" id="role" class="form-control" required>
                <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="superadmin" <?= $selectedRole === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
            </select>
            <small class="text-muted">Superadmin dapat mengelola user dan backup. Admin hanya mengelola data miliknya.</small>
        </div>

        <div class="form-row">
            <label class="form-label" for="password">
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
            <label class="form-label" for="password_confirm">Konfirmasi Password <?= !$user ? '<span class="text-danger">*</span>' : '' ?></label>
            <input
                type="password"
                name="password_confirm"
                id="password_confirm"
                class="form-control"
                autocomplete="new-password"
                <?= !$user ? 'required' : '' ?>
            >
        </div>

        <?php if (!$user): ?>
            <hr class="my-4">

            <h3 class="card-title mb-3">Profil Penelitian</h3>

            <div class="mb-3">
                <label for="nama_penelitian" class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nama_penelitian"
                    id="nama_penelitian"
                    class="form-control"
                    placeholder="Contoh: Pengembangan Bahan Ajar IPA Berbasis PBL"
                    value="<?= esc(old('nama_penelitian', $profile['nama_penelitian'] ?? '')) ?>"
                    required
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_peneliti" class="form-label">Nama Peneliti</label>
                    <input
                        type="text"
                        name="nama_peneliti"
                        id="nama_peneliti"
                        class="form-control"
                        placeholder="Nama lengkap peneliti"
                        value="<?= esc(old('nama_peneliti', $profile['nama_peneliti'] ?? '')) ?>"
                    >
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nim" class="form-label">NIM</label>
                    <input
                        type="text"
                        name="nim"
                        id="nim"
                        class="form-control"
                        placeholder="Nomor Induk Mahasiswa"
                        value="<?= esc(old('nim', $profile['nim'] ?? '')) ?>"
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="program_studi" class="form-label">Program Studi</label>
                    <input
                        type="text"
                        name="program_studi"
                        id="program_studi"
                        class="form-control"
                        placeholder="Contoh: Pendidikan Matematika"
                        value="<?= esc(old('program_studi', $profile['program_studi'] ?? '')) ?>"
                    >
                </div>
                <div class="col-md-6 mb-3">
                    <label for="institusi" class="form-label">Perguruan Tinggi</label>
                    <input
                        type="text"
                        name="institusi"
                        id="institusi"
                        class="form-control"
                        placeholder="Nama perguruan tinggi"
                        value="<?= esc(old('institusi', $profile['institusi'] ?? '')) ?>"
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tahun_penelitian" class="form-label">Tahun Penelitian</label>
                    <input
                        type="text"
                        name="tahun_penelitian"
                        id="tahun_penelitian"
                        class="form-control"
                        placeholder="<?= date('Y') ?>"
                        value="<?= esc(old('tahun_penelitian', $profile['tahun_penelitian'] ?? date('Y'))) ?>"
                    >
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ringkasan_penelitian_pdf" class="form-label">Link Ringkasan Penelitian</label>
                    <input
                        type="url"
                        name="ringkasan_penelitian_pdf"
                        id="ringkasan_penelitian_pdf"
                        class="form-control"
                        placeholder="https://drive.google.com/file/d/.../view"
                        value="<?= esc(old('ringkasan_penelitian_pdf', $profile['ringkasan_penelitian_pdf'] ?? '')) ?>"
                    >
                    <div class="form-hint">Masukkan link Google Drive PDF. Pastikan akses file disetel ke siapa saja yang memiliki link dapat melihat.</div>
                    <?php if (!empty($profile['ringkasan_penelitian_pdf'])): ?>
                        <div class="mt-2">
                            <a href="<?= sivalid_pdf_viewer_url((string) $profile['ringkasan_penelitian_pdf']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light">
                                Lihat Ringkasan
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-light">Batal</a>
        </div>
    </form>
    </div>
</div>

<?= $this->endSection() ?>
