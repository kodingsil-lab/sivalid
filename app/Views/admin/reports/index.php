<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Laporan</h1>

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
    <h3>Daftar Laporan Hasil Analisis</h3>
    <p>
        Bagian ini menampilkan laporan validasi instrumen dan validasi produk yang sudah dianalisis.
    </p>
</div>

<?php if (empty($analyses)): ?>
    <div class="empty-state">
        Belum ada data analisis validasi.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Jenis Laporan</th>
                <th>Instrumen / Produk</th>
                <th>Responden</th>
                <th>Persentase</th>
                <th>Kategori</th>
                <th style="width: 240px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($analyses as $index => $analysis): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?php if ($analysis['mode'] === 'validasi_instrumen'): ?>
                            Laporan Validasi Instrumen
                        <?php elseif ($analysis['mode'] === 'validasi_produk'): ?>
                            Laporan Validasi Produk
                        <?php else: ?>
                            <?= esc($analysis['mode']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= esc($analysis['kode']) ?></strong><br>
                        <?= esc($analysis['judul']) ?>

                        <?php if (!empty($analysis['nama_produk'])): ?>
                            <hr>
                            <strong>Produk:</strong><br>
                            <?= esc($analysis['product_kode']) ?> - <?= esc($analysis['nama_produk']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($analysis['jumlah_responden']) ?></td>
                    <td><strong><?= esc($analysis['persentase']) ?>%</strong></td>
                    <td><?= esc($analysis['kategori']) ?></td>
                    <td>
                        <?php if ($analysis['mode'] === 'validasi_instrumen'): ?>
                            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id']) ?>" class="btn btn-primary">
                                Buka
                            </a>

                            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/print') ?>" target="_blank" class="btn btn-light">
                                Cetak
                            </a>

                            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/pdf') ?>" class="btn btn-light">
                                PDF
                            </a>
                        <?php elseif ($analysis['mode'] === 'validasi_produk'): ?>
                            <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id']) ?>" class="btn btn-primary">
                                Buka
                            </a>

                            <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id'] . '/print') ?>" target="_blank" class="btn btn-light">
                                Cetak
                            </a>

                            <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id'] . '/pdf') ?>" class="btn btn-light">
                                PDF
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="card" style="margin-top: 24px;">
    <h3>Laporan Pengisian Responden</h3>
    <p>
        Bagian ini menampilkan laporan angket mahasiswa, observasi, FGD, dan instrumen pengisian lain.
    </p>
</div>

<?php if (empty($links)): ?>
    <div class="empty-state">
        Belum ada link pengisian responden.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Mode</th>
                <th>Judul Link</th>
                <th>Instrumen</th>
                <th>Jumlah Respon</th>
                <th style="width: 180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $index => $link): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><span class="badge"><?= esc($link['mode']) ?></span></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <strong><?= esc($link['kode']) ?></strong><br>
                        <?= esc($link['judul']) ?>
                    </td>
                    <td><?= esc($link['jumlah_respon'] ?? 0) ?></td>
                    <td>
                        <?php if ($link['mode'] === 'respon_mahasiswa'): ?>
                            <a href="<?= base_url('admin/reports/respon-mahasiswa/' . $link['id']) ?>" class="btn btn-primary">
                                Laporan
                            </a>
                        <?php elseif ($link['mode'] === 'observasi'): ?>
                            <a href="<?= base_url('admin/reports/observasi/' . $link['id']) ?>" class="btn btn-primary">
                                Laporan
                            </a>
                        <?php elseif ($link['mode'] === 'fgd'): ?>
                            <a href="<?= base_url('admin/reports/fgd/' . $link['id']) ?>" class="btn btn-primary">
                                Laporan
                            </a>
                        <?php else: ?>
                            <span class="badge">Belum tersedia</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="card" style="margin-top: 24px;">
    <h3>Laporan Revisi Butir</h3>
    <p>
        Laporan ini menampilkan riwayat revisi butir instrumen berdasarkan hasil validasi dan komentar validator.
    </p>

    <a href="<?= base_url('admin/reports/revisi-butir') ?>" class="btn btn-primary">
        Buka Laporan Revisi Butir
    </a>
</div>

<?= $this->endSection() ?>
