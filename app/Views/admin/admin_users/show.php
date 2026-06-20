<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail User Admin</h2>
            <div class="text-muted mt-1">Profil akun admin dan profil penelitian dalam satu halaman.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-light">Kembali</a>
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

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                <li><?= esc((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row row-cards">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Profil User</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Nama Lengkap</div>
                    <div class="fw-semibold"><?= esc($user['name'] ?? '-') ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold"><?= esc($user['email'] ?? '-') ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Role</div>
                    <div class="fw-semibold"><?= esc(ucfirst((string) ($user['role'] ?? '-'))) ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Status</div>
                    <span class="<?= esc(status_badge_class($user['status'] ?? '')) ?>">
                        <?= esc($user['status'] ?? '-') ?>
                    </span>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Dibuat</div>
                    <div><?= esc($user['created_at'] ?? '-') ?></div>
                </div>
                <div>
                    <div class="text-muted small">Terakhir Diperbarui</div>
                    <div><?= esc($user['updated_at'] ?? '-') ?></div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="<?= base_url('admin/admin-users/' . $user['id'] . '/edit') ?>" class="btn btn-primary btn-sm">Edit User</a>
                <form method="post" action="<?= base_url('admin/admin-users/' . $user['id'] . '/toggle-status') ?>" class="action-inline" onsubmit="return confirm('Ubah status user ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm <?= ($user['status'] ?? '') === 'aktif' ? 'btn-warning' : 'btn-success' ?>">
                        <?= ($user['status'] ?? '') === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profil Penelitian</h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/admin-users/' . $user['id'] . '/profile') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

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
                            <label for="ringkasan_penelitian_pdf" class="form-label">Ringkasan Penelitian</label>
                            <input
                                type="file"
                                name="ringkasan_penelitian_pdf"
                                id="ringkasan_penelitian_pdf"
                                class="form-control"
                                accept="application/pdf,.pdf"
                            >
                            <div class="form-hint">Unggah file PDF ringkasan penelitian. Maksimal 10 MB.</div>
                            <?php if (!empty($profile['ringkasan_penelitian_pdf'])): ?>
                                <div class="mt-2">
                                    <a href="<?= base_url($profile['ringkasan_penelitian_pdf']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light">
                                        Lihat PDF Tersimpan
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Profil Penelitian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
