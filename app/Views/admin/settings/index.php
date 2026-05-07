<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Pengaturan</h1>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

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
    <h3>Profil Penelitian</h3>

    <form action="<?= base_url('admin/settings/profile') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-row">
            <label for="nama_penelitian">Nama/Judul Penelitian</label>
            <input
                type="text"
                name="nama_penelitian"
                id="nama_penelitian"
                class="form-control"
                value="<?= old('nama_penelitian', $profile['nama_penelitian'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label for="nama_peneliti">Nama Peneliti</label>
                <input
                    type="text"
                    name="nama_peneliti"
                    id="nama_peneliti"
                    class="form-control"
                    value="<?= old('nama_peneliti', $profile['nama_peneliti'] ?? '') ?>"
                >
            </div>

            <div class="form-row">
                <label for="institusi">Institusi</label>
                <input
                    type="text"
                    name="institusi"
                    id="institusi"
                    class="form-control"
                    value="<?= old('institusi', $profile['institusi'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label for="program_studi">Program Studi</label>
                <input
                    type="text"
                    name="program_studi"
                    id="program_studi"
                    class="form-control"
                    value="<?= old('program_studi', $profile['program_studi'] ?? '') ?>"
                >
            </div>

            <div class="form-row">
                <label for="tahun_penelitian">Tahun Penelitian</label>
                <input
                    type="text"
                    name="tahun_penelitian"
                    id="tahun_penelitian"
                    class="form-control"
                    value="<?= old('tahun_penelitian', $profile['tahun_penelitian'] ?? date('Y')) ?>"
                >
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Profil</button>
    </form>
</div>

<div class="card">
    <h3>Kategori Kelayakan</h3>
    <p>
        Pengaturan ini disiapkan sebagai dasar kategori. Pada tahap berikutnya,
        fungsi kategori di controller dapat diarahkan untuk membaca nilai dari sini.
    </p>

    <form action="<?= base_url('admin/settings/category') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-grid">
            <div class="form-row">
                <label for="kategori_sangat_layak_min">Sangat Layak Minimal (%)</label>
                <input
                    type="number"
                    name="kategori_sangat_layak_min"
                    id="kategori_sangat_layak_min"
                    class="form-control"
                    value="<?= old('kategori_sangat_layak_min', $category['kategori_sangat_layak_min'] ?? 85) ?>"
                    min="0"
                    max="100"
                >
            </div>

            <div class="form-row">
                <label for="kategori_layak_min">Layak Minimal (%)</label>
                <input
                    type="number"
                    name="kategori_layak_min"
                    id="kategori_layak_min"
                    class="form-control"
                    value="<?= old('kategori_layak_min', $category['kategori_layak_min'] ?? 70) ?>"
                    min="0"
                    max="100"
                >
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label for="kategori_kurang_layak_min">Kurang Layak Minimal (%)</label>
                <input
                    type="number"
                    name="kategori_kurang_layak_min"
                    id="kategori_kurang_layak_min"
                    class="form-control"
                    value="<?= old('kategori_kurang_layak_min', $category['kategori_kurang_layak_min'] ?? 55) ?>"
                    min="0"
                    max="100"
                >
            </div>

            <div class="form-row">
                <label for="kategori_tidak_layak_min">Tidak Layak Minimal (%)</label>
                <input
                    type="number"
                    name="kategori_tidak_layak_min"
                    id="kategori_tidak_layak_min"
                    class="form-control"
                    value="<?= old('kategori_tidak_layak_min', $category['kategori_tidak_layak_min'] ?? 0) ?>"
                    min="0"
                    max="100"
                >
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
    </form>
</div>

<div class="card">
    <h3>User Admin</h3>
    <p>
        Pengaturan user admin sementara masih mengikuti modul login/admin yang sudah ada.
        Jika nanti diperlukan, bagian ini dapat dikembangkan menjadi manajemen user lengkap.
    </p>

    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-light">
        Kembali ke Dashboard
    </a>
</div>

<?= $this->endSection() ?>
