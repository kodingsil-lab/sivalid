<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Detail Hasil Pengisian</h1>

<div class="card">
    <h3>Identitas Pengisian</h3>

    <table>
        <tr>
            <th style="width: 240px;">Mode</th>
            <td><span class="badge"><?= esc($response['mode']) ?></span></td>
        </tr>
        <tr>
            <th>Judul Link</th>
            <td><?= esc($response['judul_link']) ?></td>
        </tr>
        <tr>
            <th>Instrumen</th>
            <td>
                <strong><?= esc($response['kode']) ?></strong><br>
                <?= esc($response['judul']) ?><br>
                <small><?= esc($response['jenis']) ?></small>
            </td>
        </tr>
        <tr>
            <th>Produk</th>
            <td>
                <?php if (!empty($response['nama_produk'])): ?>
                    <?= esc($response['product_kode']) ?> - <?= esc($response['nama_produk']) ?><br>
                    <small><?= esc($response['jenis_produk']) ?></small>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= esc($response['status']) ?></td>
        </tr>
        <tr>
            <th>Waktu Submit</th>
            <td><?= esc($response['submitted_at'] ?: '-') ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Identitas Responden/Validator</h3>

    <table>
        <tr>
            <th style="width: 240px;">Nama</th>
            <td><?= esc($response['nama']) ?></td>
        </tr>
        <tr>
            <th>Jenis Responden</th>
            <td><?= esc($response['jenis_responden']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= esc($response['email'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>NIM</th>
            <td><?= esc($response['nim'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Program Studi</th>
            <td><?= esc($response['program_studi'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Kelas</th>
            <td><?= esc($response['kelas'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Semester/Pertemuan</th>
            <td><?= esc($response['semester'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Instansi</th>
            <td><?= esc($response['instansi'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>Bidang/Jabatan</th>
            <td><?= esc($response['bidang_keahlian'] ?: '-') ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Jawaban</h3>

    <?php if (empty($answers)): ?>
        <div class="empty-state">Belum ada jawaban.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Aspek</th>
                    <th>Butir</th>
                    <th style="width: 110px;">Tipe</th>
                    <th style="width: 100px;">Skor</th>
                    <th>Jawaban Teks</th>
                    <th>Komentar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($answers as $answer): ?>
                    <tr>
                        <td><?= esc($answer['nomor']) ?></td>
                        <td><?= esc($answer['nama_aspek'] ?: '-') ?></td>
                        <td><?= nl2br(esc($answer['pernyataan'])) ?></td>
                        <td><?= esc($answer['tipe_butir']) ?></td>
                        <td><?= esc($answer['skor'] ?? '-') ?></td>
                        <td><?= nl2br(esc($answer['jawaban_teks'] ?: '-')) ?></td>
                        <td><?= nl2br(esc($answer['komentar'] ?: '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Komentar Umum dan Kesimpulan</h3>

    <table>
        <tr>
            <th style="width: 240px;">Komentar Umum</th>
            <td><?= nl2br(esc($response['komentar_umum'] ?: '-')) ?></td>
        </tr>
        <tr>
            <th>Kesimpulan</th>
            <td><?= esc($response['kesimpulan'] ?: '-') ?></td>
        </tr>
    </table>
</div>

<a href="<?= base_url('admin/submissions?mode=' . $response['mode']) ?>" class="btn btn-light">Kembali</a>

<?= $this->endSection() ?>
