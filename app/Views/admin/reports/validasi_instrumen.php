<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Laporan Validasi Instrumen</h2>
            <div class="text-muted mt-1">
                <?= esc($link['kode']) ?> - <?= esc($link['judul']) ?>
            </div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/print') ?>" target="_blank" class="btn btn-light">
                Cetak HTML
            </a>
            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/pdf-preview') ?>" target="_blank" class="btn btn-light">
                Preview PDF
            </a>
            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/pdf') ?>" class="btn btn-primary">
                Unduh PDF
            </a>
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-light">Kembali</a>
        </div>
    </div>
</div>

<div class="card">
    <h3>1. Identitas Instrumen</h3>

    <table>
        <tr>
            <th style="width: 240px;">Kode Instrumen</th>
            <td><?= esc($link['kode']) ?></td>
        </tr>
        <tr>
            <th>Judul Instrumen</th>
            <td><?= esc($link['judul']) ?></td>
        </tr>
        <tr>
            <th>Jenis Instrumen</th>
            <td><?= esc(title_case_label((string) ($link['jenis'] ?? '-'))) ?></td>
        </tr>
        <tr>
            <th>Sasaran</th>
            <td><?= esc($link['instrument_sasaran'] ?: $link['sasaran'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Status Instrumen</th>
            <td><span class="<?= esc(status_badge_class($link['instrument_status'] ?? '')) ?>"><?= esc(status_display_label((string) ($link['instrument_status'] ?? ''))) ?></span></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>2. Tujuan Validasi Instrumen</h3>
    <p>
        Validasi instrumen dilakukan untuk memperoleh penilaian ahli terhadap kelayakan instrumen
        sebelum digunakan dalam proses validasi produk atau pengumpulan data penelitian.
    </p>
</div>

<div class="card">
    <h3>3. Identitas Validator</h3>

    <?php if (empty($responses)): ?>
        <div class="empty-state">Belum ada data validator.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Validator</th>
                    <th>Email</th>
                    <th>Bidang Keahlian</th>
                    <th>Instansi</th>
                    <th>Kesimpulan</th>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>4. Rekap Skor Keseluruhan</h3>

    <table>
        <tr>
            <th style="width: 240px;">Jumlah Validator</th>
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
            <th>Kategori Kelayakan</th>
            <td><strong><?= esc($analysis['kategori']) ?></strong></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>5. Rekap Skor Per Aspek</h3>

    <?php if (empty($aspectAnalysis)): ?>
        <div class="empty-state">Belum ada analisis aspek.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Aspek</th>
                    <th>Total Skor</th>
                    <th>Skor Maksimal</th>
                    <th>Rata-Rata</th>
                    <th>Persentase</th>
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
    <?php endif; ?>
</div>

<div class="card">
    <h3>6. Rekap Skor Per Butir</h3>

    <?php if (empty($itemAnalysis)): ?>
        <div class="empty-state">Belum ada analisis butir.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No Butir</th>
                    <th>Aspek</th>
                    <th>Pernyataan</th>
                    <th>Total Skor</th>
                    <th>Rata-Rata</th>
                    <th>Kategori</th>
                    <th>Rekomendasi</th>
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
    <?php endif; ?>
</div>

<div class="card">
    <h3>7. Komentar Validator</h3>

    <?php if (empty($comments)): ?>
        <div class="empty-state">Tidak ada komentar per butir.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No Butir</th>
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
    <?php endif; ?>
</div>

<div class="card">
    <h3>8. Daftar Revisi Butir</h3>

    <?php if (empty($revisions)): ?>
        <div class="empty-state">Belum ada riwayat revisi butir.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Butir</th>
                    <th>Pernyataan Lama</th>
                    <th>Pernyataan Baru</th>
                    <th>Alasan Revisi</th>
                    <th>Tanggal Revisi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($revisions as $index => $revision): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>Butir <?= esc($revision['nomor']) ?></td>
                        <td><?= nl2br(esc($revision['pernyataan_lama'])) ?></td>
                        <td><?= nl2br(esc($revision['pernyataan_baru'])) ?></td>
                        <td><?= nl2br(esc($revision['alasan_revisi'] ?: '-')) ?></td>
                        <td><?= esc($revision['tanggal_revisi'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>9. Kesimpulan Akhir</h3>
    <p>
        Berdasarkan hasil validasi instrumen oleh
        <strong><?= esc($analysis['jumlah_responden']) ?></strong> validator,
        instrumen memperoleh persentase kelayakan sebesar
        <strong><?= esc($analysis['persentase']) ?>%</strong>
        dan berada pada kategori
        <strong>"<?= esc($analysis['kategori']) ?>"</strong>.
    </p>
</div>

<?= $this->endSection() ?>
