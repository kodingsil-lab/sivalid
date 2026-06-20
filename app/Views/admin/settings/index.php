<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $activeTab = isset($activeTab) ? (string) $activeTab : 'category'; ?>

<div class="page-header d-print-none mb-3">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Pengaturan</h2>
                <div class="text-muted mt-1">Konfigurasi kategori kelayakan, data referensi, dan pengelolaan akun admin.</div>
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

<div class="card settings-layout-card">
    <div class="row g-0">
        <div class="col-lg-3 col-xl-3">
            <aside class="settings-sidebar">
                <div class="settings-nav-group">
                    <div class="settings-nav-label">Konfigurasi Penelitian</div>
                    <a href="<?= base_url('admin/settings?tab=category') ?>" class="settings-nav-link <?= $activeTab === 'category' ? 'active' : '' ?>">Kategori Kelayakan</a>
                </div>

                <div class="settings-nav-group">
                    <div class="settings-nav-label">Data Referensi</div>
                    <a href="<?= base_url('admin/settings?tab=instrument-types') ?>" class="settings-nav-link <?= $activeTab === 'instrument-types' ? 'active' : '' ?>">Jenis Instrumen</a>
                    <a href="<?= base_url('admin/settings?tab=product-types') ?>" class="settings-nav-link <?= $activeTab === 'product-types' ? 'active' : '' ?>">Jenis Produk</a>
                </div>

                <div class="settings-nav-group">
                    <div class="settings-nav-label">Sistem</div>
                    <a href="<?= base_url('admin/settings?tab=application') ?>" class="settings-nav-link <?= $activeTab === 'application' ? 'active' : '' ?>">Aplikasi</a>
                    <a href="<?= base_url('admin/settings?tab=system') ?>" class="settings-nav-link <?= $activeTab === 'system' ? 'active' : '' ?>">User Admin &amp; Backup</a>
                </div>
            </aside>
        </div>

        <div class="col-lg-9 col-xl-9">
            <div class="settings-content">
                <?php if ($activeTab === 'category'): ?>
                <section id="section-category" class="settings-section">
                    <div class="settings-section-header">
                        <h3>Kategori Kelayakan</h3>
                        <p>Ambang batas kategori yang dipakai pada analisis validasi instrumen dan produk.</p>
                    </div>

                    <div class="alert alert-info mb-4" role="alert">
                        <div class="d-flex gap-2 align-items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon mt-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M12 8h.01" /><path d="M11 12h1v4h1" /></svg>
                            <div>
                                <strong>Catatan:</strong> Nilai ambang batas ini digunakan sebagai <strong>dasar perhitungan kategori kelayakan</strong> pada analisis validasi instrumen dan produk. Pastikan setiap batas lebih besar dari kategori di bawahnya.
                            </div>
                        </div>
                    </div>

                    <form action="<?= base_url('admin/settings/category?tab=category') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered table-vcenter table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40%">Kategori</th>
                                        <th style="width:30%">Nilai Minimal (%)</th>
                                        <th class="text-muted" style="width:30%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-green text-green-fg">Sangat Layak</span></td>
                                        <td>
                                            <input type="number" name="kategori_sangat_layak_min" id="kategori_sangat_layak_min" class="form-control form-control-sm settings-number-input" value="<?= old('kategori_sangat_layak_min', esc((string) ($category['kategori_sangat_layak_min'] ?? 85))) ?>" min="0" max="100">
                                        </td>
                                        <td class="text-muted small">Skor ≥ nilai ini dikategorikan Sangat Layak</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-blue text-blue-fg">Layak</span></td>
                                        <td>
                                            <input type="number" name="kategori_layak_min" id="kategori_layak_min" class="form-control form-control-sm settings-number-input" value="<?= old('kategori_layak_min', esc((string) ($category['kategori_layak_min'] ?? 70))) ?>" min="0" max="100">
                                        </td>
                                        <td class="text-muted small">Skor ≥ nilai ini (dan &lt; Sangat Layak) dikategorikan Layak</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-orange text-orange-fg">Kurang Layak</span></td>
                                        <td>
                                            <input type="number" name="kategori_kurang_layak_min" id="kategori_kurang_layak_min" class="form-control form-control-sm settings-number-input" value="<?= old('kategori_kurang_layak_min', esc((string) ($category['kategori_kurang_layak_min'] ?? 55))) ?>" min="0" max="100">
                                        </td>
                                        <td class="text-muted small">Skor ≥ nilai ini (dan &lt; Layak) dikategorikan Kurang Layak</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-red text-red-fg">Tidak Layak</span></td>
                                        <td>
                                            <input type="number" name="kategori_tidak_layak_min" id="kategori_tidak_layak_min" class="form-control form-control-sm settings-number-input" value="<?= old('kategori_tidak_layak_min', esc((string) ($category['kategori_tidak_layak_min'] ?? 0))) ?>" min="0" max="100">
                                        </td>
                                        <td class="text-muted small">Skor di bawah ambang Kurang Layak dikategorikan Tidak Layak</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="settings-actions">
                            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                        </div>
                    </form>
                </section>
                <?php elseif ($activeTab === 'instrument-types'): ?>

                <section id="section-instrument-types" class="settings-section settings-section-last">
                    <div class="settings-section-header">
                        <h3>Jenis/Bentuk Instrumen</h3>
                        <p>Kelola daftar jenis/bentuk instrumen yang muncul di Master Instrumen, misalnya Angket, Wawancara, Observasi, FGD, atau Tes Kinerja.</p>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Jenis Baru</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/instrument-types') ?>" method="post" class="search-form">
                                <?= csrf_field() ?>
                                <input
                                    type="text"
                                    name="jenis"
                                    class="form-control"
                                    placeholder="Contoh: Angket, Wawancara, Rubrik Penilaian"
                                    value="<?= old('jenis') ?>"
                                    maxlength="100"
                                    required
                                >
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </form>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Jenis/Bentuk Instrumen</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px;">No</th>
                                            <th>Nama Jenis</th>
                                            <th style="width: 200px;">Dipakai di Master</th>
                                            <th class="table-actions-cell">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($instrumentTypes)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Belum ada jenis instrumen.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($instrumentTypes as $index => $type): ?>
                                                <?php
                                                $label = (string) ($type['setting_value'] ?? '');
                                                $usedCount = (int) ($instrumentTypeUsage[$label] ?? 0);
                                                ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= esc($label) ?></td>
                                                    <td>
                                                        <span class="badge bg-blue text-blue-fg"><?= $usedCount ?> data</span>
                                                    </td>
                                                    <td class="table-actions-cell">
                                                        <?php if ($usedCount === 0): ?>
                                                            <form action="<?= base_url('admin/instrument-types/' . (int) $type['id']) ?>" method="post" onsubmit="return confirm('Hapus jenis instrumen ini?');">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Tidak bisa dihapus</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
                <?php elseif ($activeTab === 'product-types'): ?>

                <section id="section-product-types" class="settings-section settings-section-last">
                    <div class="settings-section-header">
                        <h3>Jenis Produk</h3>
                        <p>Kelola daftar jenis yang muncul pada dropdown Jenis Produk di form Produk Penelitian.</p>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Jenis Baru</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/product-types') ?>" method="post" class="search-form">
                                <?= csrf_field() ?>
                                <input
                                    type="text"
                                    name="jenis"
                                    class="form-control"
                                    placeholder="Contoh: Modul Interaktif"
                                    value="<?= old('jenis') ?>"
                                    maxlength="100"
                                    required
                                >
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </form>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Jenis Produk</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px;">No</th>
                                            <th>Nama Jenis</th>
                                            <th style="width: 200px;">Dipakai di Produk</th>
                                            <th class="table-actions-cell">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($productTypes)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Belum ada jenis produk.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productTypes as $index => $type): ?>
                                                <?php
                                                $label = (string) ($type['setting_value'] ?? '');
                                                $usedCount = (int) ($productTypeUsage[$label] ?? 0);
                                                ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= esc($label) ?></td>
                                                    <td>
                                                        <span class="badge bg-blue text-blue-fg"><?= $usedCount ?> data</span>
                                                    </td>
                                                    <td class="table-actions-cell">
                                                        <?php if ($usedCount === 0): ?>
                                                            <form action="<?= base_url('admin/product-types/' . (int) $type['id']) ?>" method="post" onsubmit="return confirm('Hapus jenis produk ini?');">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Tidak bisa dihapus</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
                <?php elseif ($activeTab === 'application'): ?>

                <?php
                    $currentLogo = (string) ($application['app_logo'] ?? 'assets/sivalid copy.png');
                    $currentFavicon = (string) ($application['app_favicon'] ?? 'assets/sivalid copy.png');
                ?>

                <section id="section-application" class="settings-section settings-section-last">
                    <div class="settings-section-header">
                        <h3>Pengaturan Aplikasi</h3>
                        <p>Atur logo pada halaman login dan favicon yang tampil di tab browser.</p>
                    </div>

                    <form action="<?= base_url('admin/settings/application?tab=application') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="app_logo" class="form-label">Logo Login</label>
                                <div class="settings-brand-preview mb-2">
                                    <img src="<?= sivalid_asset_url($currentLogo, 'assets/sivalid copy.png') ?>" alt="Logo aplikasi saat ini">
                                </div>
                                <input
                                    type="file"
                                    name="app_logo"
                                    id="app_logo"
                                    class="form-control"
                                    accept=".png,.jpg,.jpeg,.gif,.webp,.svg,image/png,image/jpeg,image/gif,image/webp,image/svg+xml"
                                >
                                <div class="form-hint">Format PNG, JPG, GIF, WebP, atau SVG. Maksimal 2 MB.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="app_favicon" class="form-label">Favicon</label>
                                <div class="settings-brand-preview settings-brand-preview-sm mb-2">
                                    <img src="<?= sivalid_asset_url($currentFavicon, 'assets/sivalid copy.png') ?>" alt="Favicon saat ini">
                                </div>
                                <input
                                    type="file"
                                    name="app_favicon"
                                    id="app_favicon"
                                    class="form-control"
                                    accept=".ico,.png,.jpg,.jpeg,.gif,.webp,.svg,image/x-icon,image/png,image/jpeg,image/gif,image/webp,image/svg+xml"
                                >
                                <div class="form-hint">Format ICO, PNG, JPG, GIF, WebP, atau SVG. Maksimal 1 MB.</div>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button type="submit" class="btn btn-primary">Simpan Aplikasi</button>
                        </div>
                    </form>
                </section>
                <?php elseif ($activeTab === 'system'): ?>

                <section id="section-admin" class="settings-section settings-section-last">
                    <div class="settings-section-header">
                        <h3>User Admin &amp; Backup</h3>
                        <p>Kelola akun admin dan utilitas backup dari satu area yang lebih ringkas.</p>
                    </div>

                    <p class="text-muted mb-3">
                        Kelola akun yang dapat mengakses panel admin SIVALID. Tambah admin baru, perbarui nama atau kata sandi, atau nonaktifkan akun yang tidak lagi digunakan.
                    </p>

                    <?php if (is_superadmin()): ?>
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
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            Pengelolaan user dan backup hanya tersedia untuk superadmin.
                        </div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
