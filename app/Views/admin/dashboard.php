<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Dashboard SIVALID</h1>

<div class="grid">
    <div class="stat-card">
        <div class="number"><?= esc($totalInstrumen ?? 0) ?></div>
        <div class="label">Total Instrumen</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($instrumenValid ?? 0) ?></div>
        <div class="label">Instrumen Valid</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($totalProduk ?? 0) ?></div>
        <div class="label">Produk Penelitian</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($linkAktif ?? 0) ?></div>
        <div class="label">Link Aktif</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($totalRespon ?? 0) ?></div>
        <div class="label">Respon Masuk</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= esc($totalLaporan ?? 0) ?></div>
        <div class="label">Laporan Analisis</div>
    </div>
</div>

<div class="card">
    <h3>Ringkasan Respon Berdasarkan Mode</h3>

    <?php if (empty($responByMode)): ?>
        <div class="empty-state">Belum ada respon masuk.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Mode Pengisian</th>
                    <th style="width: 160px;">Jumlah Respon</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responByMode as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge"><?= esc($row['mode']) ?></span></td>
                        <td><?= esc($row['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Respon Terbaru</h3>

    <?php if (empty($latestResponses)): ?>
        <div class="empty-state">Belum ada respon terbaru.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Responden</th>
                    <th>Mode</th>
                    <th>Instrumen</th>
                    <th>Judul Link</th>
                    <th>Waktu Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestResponses as $index => $response): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= esc($response['nama']) ?><br>
                            <small><?= esc($response['jenis_responden']) ?></small>
                        </td>
                        <td><span class="badge"><?= esc($response['mode']) ?></span></td>
                        <td>
                            <strong><?= esc($response['kode']) ?></strong><br>
                            <?= esc($response['judul']) ?>
                        </td>
                        <td><?= esc($response['judul_link']) ?></td>
                        <td><?= esc($response['submitted_at'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Laporan Analisis Terbaru</h3>

    <?php if (empty($latestAnalyses)): ?>
        <div class="empty-state">Belum ada laporan analisis.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Jenis</th>
                    <th>Instrumen/Produk</th>
                    <th>Persentase</th>
                    <th>Kategori</th>
                    <th style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestAnalyses as $index => $analysis): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge"><?= esc($analysis['mode']) ?></span></td>
                        <td>
                            <strong><?= esc($analysis['kode']) ?></strong><br>
                            <?= esc($analysis['judul']) ?>

                            <?php if (!empty($analysis['nama_produk'])): ?>
                                <br><small>Produk: <?= esc($analysis['nama_produk']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= esc($analysis['persentase']) ?>%</strong></td>
                        <td><?= esc($analysis['kategori']) ?></td>
                        <td>
                            <?php if ($analysis['mode'] === 'validasi_instrumen'): ?>
                                <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id']) ?>" class="btn btn-light">
                                    Laporan
                                </a>
                            <?php elseif ($analysis['mode'] === 'validasi_produk'): ?>
                                <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id']) ?>" class="btn btn-light">
                                    Laporan
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="toolbar">
    <a href="<?= base_url('admin/instruments') ?>" class="btn btn-primary">Kelola Instrumen</a>
    <a href="<?= base_url('admin/respondent-links') ?>" class="btn btn-light">Link Responden</a>
    <a href="<?= base_url('admin/reports') ?>" class="btn btn-light">Laporan</a>
</div>

<?= $this->endSection() ?>
