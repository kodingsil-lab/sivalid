<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Laporan Respon Mahasiswa</h1>

<div class="card">
    <h3>Identitas Instrumen</h3>

    <table>
        <tr>
            <th style="width: 240px;">Judul Link</th>
            <td><?= esc($link['judul_link']) ?></td>
        </tr>
        <tr>
            <th>Instrumen</th>
            <td><?= esc($link['kode']) ?> - <?= esc($link['judul']) ?></td>
        </tr>
        <tr>
            <th>Sasaran</th>
            <td><?= esc($link['sasaran'] ?: '-') ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Ringkasan Respon</h3>

    <table>
        <tr>
            <th style="width: 240px;">Jumlah Responden</th>
            <td><?= esc($summary['jumlah_responden']) ?></td>
        </tr>
        <tr>
            <th>Total Skor</th>
            <td><?= esc($summary['total_skor']) ?></td>
        </tr>
        <tr>
            <th>Jumlah Jawaban</th>
            <td><?= esc($summary['jumlah_jawaban']) ?></td>
        </tr>
        <tr>
            <th>Rata-Rata</th>
            <td><?= esc($summary['rata_rata']) ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Identitas Responden</h3>

    <?php if (empty($responses)): ?>
        <div class="empty-state">Belum ada responden.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
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
                        <td><?= nl2br(esc($response['komentar_umum'] ?: '-')) ?></td>
                        <td><?= esc($response['submitted_at'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Rekap Per Butir</h3>

    <?php if (empty($items)): ?>
        <div class="empty-state">Belum ada rekap butir.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No Butir</th>
                    <th>Aspek</th>
                    <th>Pernyataan</th>
                    <th>Jumlah Jawaban</th>
                    <th>Total Skor</th>
                    <th>Rata-Rata</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= esc($item['nomor']) ?></td>
                        <td><?= esc($item['nama_aspek']) ?></td>
                        <td><?= nl2br(esc($item['pernyataan'])) ?></td>
                        <td><?= esc($item['jumlah_jawaban']) ?></td>
                        <td><?= esc($item['total_skor']) ?></td>
                        <td><?= number_format((float) $item['rata_rata'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Komentar Responden</h3>

    <?php if (empty($comments)): ?>
        <div class="empty-state">Tidak ada komentar per butir.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No Butir</th>
                    <th>Butir</th>
                    <th>Responden</th>
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

<a href="<?= base_url('admin/reports') ?>" class="btn btn-light">Kembali</a>

<?= $this->endSection() ?>