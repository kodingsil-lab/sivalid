<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Hasil Analisis Validasi Produk</h2>
            <div class="text-muted mt-1">
                <?= esc($link['product_kode'] ?? '-') ?> - <?= esc($link['nama_produk'] ?? '-') ?>
            </div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id']) ?>" class="btn btn-primary">
                Buka Laporan
            </a>
            <a href="<?= base_url('admin/products/' . $analysis['product_id']) ?>" class="btn btn-light">Detail Produk</a>
            <a href="<?= base_url('admin/validasi-produk') ?>" class="btn btn-light">Kembali</a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3><?= esc($link['nama_produk'] ?? '-') ?></h3>

    <table class="table table-vcenter table-sm">
        <tr>
            <th style="width: 240px;">Kode Produk</th>
            <td><?= esc($link['product_kode'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Jenis Produk</th>
            <td><?= esc($link['jenis_produk'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Status Produk</th>
            <td><span class="<?= esc(status_badge_class($link['product_status'] ?? '')) ?>"><?= esc($link['product_status'] ?? '-') ?></span></td>
        </tr>
        <tr>
            <th>Instrumen Penilaian</th>
            <td>
                <strong><?= esc($link['kode']) ?></strong><br>
                <?= esc($link['judul']) ?>
            </td>
        </tr>
        <tr>
            <th>Judul Link</th>
            <td><?= esc($link['judul_link']) ?></td>
        </tr>
        <tr>
            <th>Jumlah Validator</th>
            <td><?= esc($analysis['jumlah_responden']) ?></td>
        </tr>
        <tr>
            <th>Jumlah Butir</th>
            <td><?= esc($analysis['jumlah_butir']) ?></td>
        </tr>
        <tr>
            <th>Total Skor</th>
            <td><?= esc($analysis['total_skor']) ?></td>
        </tr>
        <tr>
            <th>Skor Maksimal</th>
            <td><?= esc($analysis['skor_maksimal']) ?></td>
        </tr>
        <tr>
            <th>Rata-Rata</th>
            <td><?= esc($analysis['rata_rata']) ?></td>
        </tr>
        <tr>
            <th>Persentase Kelayakan</th>
            <td><strong><?= esc($analysis['persentase']) ?>%</strong></td>
        </tr>
        <tr>
            <th>Kategori Produk</th>
            <td><strong><?= esc($analysis['kategori']) ?></strong></td>
        </tr>
    </table>
</div>

<div class="grid">
    <div class="stat-card">
        <div class="number"><?= esc($analysis['jumlah_responden']) ?></div>
        <div class="label">Validator Produk</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($analysis['jumlah_butir']) ?></div>
        <div class="label">Butir Penilaian</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($analysis['persentase']) ?>%</div>
        <div class="label">Persentase Kelayakan</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($analysis['kategori']) ?></div>
        <div class="label">Kategori Produk</div>
    </div>
</div>

<div class="card">
    <h3>Rekap Validator Produk</h3>

    <?php if (empty($responses)): ?>
        <div class="empty-state">Belum ada data validator produk.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Bidang Keahlian</th>
                    <th>Instansi</th>
                    <th>Kesimpulan</th>
                    <th>Komentar Umum</th>
                    <th>Waktu Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $index => $response): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($response['nama']) ?></td>
                        <td><?= esc($response['email'] ?: '-') ?></td>
                        <td><?= esc($response['bidang_keahlian'] ?: '-') ?></td>
                        <td><?= esc($response['instansi'] ?: '-') ?></td>
                        <td><?= esc($response['kesimpulan'] ?: '-') ?></td>
                        <td><?= nl2br(esc($response['komentar_umum'] ?: '-')) ?></td>
                        <td><?= esc($response['submitted_at'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Analisis Per Aspek</h3>

    <?php if (empty($aspectAnalysis)): ?>
        <div class="empty-state">Analisis per aspek belum tersedia.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Aspek</th>
                    <th style="width: 120px;">Total Skor</th>
                    <th style="width: 120px;">Skor Maks.</th>
                    <th style="width: 120px;">Rata-Rata</th>
                    <th style="width: 120px;">Persentase</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($aspectAnalysis as $index => $aspect): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($aspect['nama_aspek']) ?></td>
                        <td><?= esc($aspect['total_skor']) ?></td>
                        <td><?= esc($aspect['skor_maksimal']) ?></td>
                        <td><?= esc($aspect['rata_rata']) ?></td>
                        <td><?= esc($aspect['persentase']) ?>%</td>
                        <td><?= esc($aspect['kategori']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Analisis Per Butir</h3>

    <?php if (empty($itemAnalysis)): ?>
        <div class="empty-state">Analisis per butir belum tersedia.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Aspek</th>
                    <th>Butir Pernyataan</th>
                    <th style="width: 110px;">Total Skor</th>
                    <th style="width: 110px;">Rata-Rata</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 150px;">Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemAnalysis as $item): ?>
                    <tr>
                        <td><?= esc($item['nomor']) ?></td>
                        <td><?= esc($item['nama_aspek']) ?></td>
                        <td><?= nl2br(esc($item['pernyataan'])) ?></td>
                        <td><?= esc($item['total_skor']) ?></td>
                        <td><?= esc($item['rata_rata']) ?></td>
                        <td><?= esc($item['kategori']) ?></td>
                        <td><strong><?= esc($item['rekomendasi']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Komentar dan Saran Validator per Butir</h3>

    <?php if (empty($comments)): ?>
        <div class="empty-state">Belum ada komentar per butir.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Butir</th>
                    <th>Validator</th>
                    <th>Komentar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= esc($comment['nomor']) ?></td>
                        <td><?= nl2br(esc($comment['pernyataan'])) ?></td>
                        <td><?= esc($comment['nama']) ?></td>
                        <td><?= nl2br(esc($comment['komentar'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Kesimpulan Otomatis</h3>
    <p>
        Berdasarkan hasil validasi produk oleh
        <strong><?= esc($analysis['jumlah_responden']) ?></strong> validator,
        produk <strong><?= esc($link['nama_produk'] ?? '-') ?></strong>
        memperoleh total skor
        <strong><?= esc($analysis['total_skor']) ?></strong>
        dari skor maksimal
        <strong><?= esc($analysis['skor_maksimal']) ?></strong>,
        dengan persentase kelayakan sebesar
        <strong><?= esc($analysis['persentase']) ?>%</strong>.
        Dengan demikian, produk berada pada kategori
        <strong>"<?= esc($analysis['kategori']) ?>"</strong>.
    </p>
</div>

<?= $this->endSection() ?>
