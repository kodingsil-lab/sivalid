<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Pengaturan</h2>
                <div class="text-muted mt-1">Konfigurasi profil penelitian, kategori kelayakan, dan pengelolaan akun admin.</div>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible mb-3" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
            </div>
            <div><?= esc((string) session()->getFlashdata('success')) ?></div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible mb-3" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4m0 4v.01" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z" /></svg>
            </div>
            <div>
                <h4 class="alert-title">Periksa kembali input berikut:</h4>
                <ul class="mb-0 mt-1">
                    <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc((string) $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
<?php endif; ?>

<!-- 1. Profil Penelitian -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="4" /><path d="M6.523 6.523a7 7 0 1 0 10.95 0" /></svg>
            Profil Penelitian
        </h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/settings/profile') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nama_penelitian" class="form-label">Nama / Judul Penelitian <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nama_penelitian"
                    id="nama_penelitian"
                    class="form-control"
                    placeholder="Contoh: Pengembangan Bahan Ajar IPA Berbasis PBL"
                    value="<?= old('nama_penelitian', esc($profile['nama_penelitian'] ?? '')) ?>"
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
                        value="<?= old('nama_peneliti', esc($profile['nama_peneliti'] ?? '')) ?>"
                    >
                </div>
                <div class="col-md-6 mb-3">
                    <label for="institusi" class="form-label">Institusi</label>
                    <input
                        type="text"
                        name="institusi"
                        id="institusi"
                        class="form-control"
                        placeholder="Nama universitas / lembaga"
                        value="<?= old('institusi', esc($profile['institusi'] ?? '')) ?>"
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
                        value="<?= old('program_studi', esc($profile['program_studi'] ?? '')) ?>"
                    >
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tahun_penelitian" class="form-label">Tahun Penelitian</label>
                    <input
                        type="text"
                        name="tahun_penelitian"
                        id="tahun_penelitian"
                        class="form-control"
                        placeholder="<?= date('Y') ?>"
                        value="<?= old('tahun_penelitian', esc($profile['tahun_penelitian'] ?? date('Y'))) ?>"
                    >
                </div>
            </div>

            <div class="mt-1">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Kategori Kelayakan -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
            Kategori Kelayakan
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4" role="alert">
            <div class="d-flex gap-2 align-items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon mt-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M12 8h.01" /><path d="M11 12h1v4h1" /></svg>
                <div>
                    <strong>Catatan:</strong> Nilai ambang batas ini digunakan sebagai <strong>dasar perhitungan kategori kelayakan</strong> pada analisis validasi instrumen dan produk. Pastikan setiap batas lebih besar dari kategori di bawahnya.
                </div>
            </div>
        </div>

        <form action="<?= base_url('admin/settings/category') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-responsive mb-3">
                <table class="table table-bordered table-vcenter">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40%">Kategori</th>
                            <th style="width:30%">Nilai Minimal (%)</th>
                            <th class="text-muted" style="width:30%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="badge badge-status-success">Sangat Layak</span>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="kategori_sangat_layak_min"
                                    id="kategori_sangat_layak_min"
                                    class="form-control form-control-sm"
                                    style="width:100px"
                                    value="<?= old('kategori_sangat_layak_min', esc((string) ($category['kategori_sangat_layak_min'] ?? 85))) ?>"
                                    min="0"
                                    max="100"
                                >
                            </td>
                            <td class="text-muted small">Skor ≥ nilai ini dikategorikan Sangat Layak</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge badge-status-process">Layak</span>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="kategori_layak_min"
                                    id="kategori_layak_min"
                                    class="form-control form-control-sm"
                                    style="width:100px"
                                    value="<?= old('kategori_layak_min', esc((string) ($category['kategori_layak_min'] ?? 70))) ?>"
                                    min="0"
                                    max="100"
                                >
                            </td>
                            <td class="text-muted small">Skor ≥ nilai ini (dan &lt; Sangat Layak) dikategorikan Layak</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge badge-status-warning">Kurang Layak</span>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="kategori_kurang_layak_min"
                                    id="kategori_kurang_layak_min"
                                    class="form-control form-control-sm"
                                    style="width:100px"
                                    value="<?= old('kategori_kurang_layak_min', esc((string) ($category['kategori_kurang_layak_min'] ?? 55))) ?>"
                                    min="0"
                                    max="100"
                                >
                            </td>
                            <td class="text-muted small">Skor ≥ nilai ini (dan &lt; Layak) dikategorikan Kurang Layak</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge badge-status-danger">Tidak Layak</span>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="kategori_tidak_layak_min"
                                    id="kategori_tidak_layak_min"
                                    class="form-control form-control-sm"
                                    style="width:100px"
                                    value="<?= old('kategori_tidak_layak_min', esc((string) ($category['kategori_tidak_layak_min'] ?? 0))) ?>"
                                    min="0"
                                    max="100"
                                >
                            </td>
                            <td class="text-muted small">Skor di bawah ambang Kurang Layak dikategorikan Tidak Layak</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. User Admin -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-secondary" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
            User Admin
        </h3>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Kelola akun yang dapat mengakses panel admin SIVALID. Tambah admin baru, perbarui nama atau kata sandi, atau nonaktifkan akun yang tidak lagi digunakan.
        </p>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 11h6m-3 -3v6" /></svg>
                Manajemen User Admin
            </a>
            <a href="<?= base_url('admin/backup') ?>" class="btn btn-light">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                Backup &amp; Restore
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
