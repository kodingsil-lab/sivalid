<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$modeLabel = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === 'validasi_instrumen') {
        return 'Validasi Instrumen';
    }

    if ($mode === 'validasi_produk') {
        return 'Validasi Produk';
    }

    return ucwords(str_replace('_', ' ', $mode));
};

$modeBadgeClass = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === 'validasi_instrumen') {
        return 'badge-status-process';
    }

    if ($mode === 'validasi_produk') {
        return 'badge-status-warning';
    }

    return 'badge-status-draft';
};

$kategoriBadgeClass = static function (?string $kategori): string {
    $value = strtolower(trim((string) $kategori));

    if ($value === 'sangat valid' || $value === 'valid') {
        return 'badge-status-success';
    }

    if ($value === 'cukup valid') {
        return 'badge-status-warning';
    }

    if ($value === 'kurang valid' || $value === 'tidak valid') {
        return 'badge-status-danger';
    }

    return 'badge-status-draft';
};
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title mb-1">Dashboard SIVALID</h1>
        <div class="text-muted">Ringkasan validasi instrumen penelitian.</div>
    </div>
</div>

<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="number"><?= (int) ($totalInstrumen ?? 0) ?></div>
                        <div class="label">Total Instrumen</div>
                    </div>
                    <span class="badge badge-status-process">Instrumen</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="number"><?= (int) ($instrumenValid ?? 0) ?></div>
                        <div class="label">Instrumen Valid</div>
                    </div>
                    <span class="badge badge-status-success">Valid</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="number"><?= (int) ($totalProduk ?? 0) ?></div>
                        <div class="label">Produk Penelitian</div>
                    </div>
                    <span class="badge badge-status-draft">Produk</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="number"><?= (int) ($linkAktif ?? 0) ?></div>
                        <div class="label">Link Aktif</div>
                    </div>
                    <span class="badge badge-status-warning">Distribusi</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="number"><?= (int) ($totalRespon ?? 0) ?></div>
                        <div class="label">Respon Masuk</div>
                    </div>
                    <span class="badge badge-status-process">Respon</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="number"><?= (int) ($totalLaporan ?? 0) ?></div>
                        <div class="label">Laporan Analisis</div>
                    </div>
                    <span class="badge badge-status-success">Laporan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Ringkasan Respon Berdasarkan Mode</h3>

        <?php if (empty($responByMode)): ?>
            <div class="empty-state">Belum ada respon masuk.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Mode Pengisian</th>
                            <th style="width: 200px;">Jumlah Respon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($responByMode as $index => $row): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <span class="badge <?= esc($modeBadgeClass($row['mode'] ?? '')) ?>">
                                        <?= esc($modeLabel($row['mode'] ?? '')) ?>
                                    </span>
                                </td>
                                <td><strong><?= (int) ($row['total'] ?? 0) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Respon Terbaru</h3>

        <?php if (empty($latestResponses)): ?>
            <div class="empty-state">Belum ada respon terbaru.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Responden</th>
                            <th style="width: 180px;">Mode</th>
                            <th>Instrumen</th>
                            <th>Judul Link</th>
                            <th style="width: 180px;">Waktu Submit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestResponses as $index => $response): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="fw-semibold"><?= esc((string) ($response['nama'] ?? '-')) ?></div>
                                    <div class="text-muted small"><?= esc((string) ($response['jenis_responden'] ?? '-')) ?></div>
                                </td>
                                <td>
                                    <span class="badge <?= esc($modeBadgeClass($response['mode'] ?? '')) ?>">
                                        <?= esc($modeLabel($response['mode'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= esc((string) ($response['kode'] ?? '-')) ?></div>
                                    <div class="text-muted small"><?= esc((string) ($response['judul'] ?? '-')) ?></div>
                                </td>
                                <td><?= esc((string) ($response['judul_link'] ?? '-')) ?></td>
                                <td class="text-muted"><?= esc((string) (!empty($response['submitted_at']) ? $response['submitted_at'] : '-')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-3">Laporan Analisis Terbaru</h3>

        <?php if (empty($latestAnalyses)): ?>
            <div class="empty-state">Belum ada laporan analisis.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th style="width: 180px;">Jenis</th>
                            <th>Instrumen/Produk</th>
                            <th style="width: 130px;">Persentase</th>
                            <th style="width: 180px;">Kategori</th>
                            <th class="table-actions-cell">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestAnalyses as $index => $analysis): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <span class="badge <?= esc($modeBadgeClass($analysis['mode'] ?? '')) ?>">
                                        <?= esc($modeLabel($analysis['mode'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= esc((string) ($analysis['kode'] ?? '-')) ?></div>
                                    <div class="text-muted small"><?= esc((string) ($analysis['judul'] ?? '-')) ?></div>
                                    <?php if (!empty($analysis['nama_produk'])): ?>
                                        <div class="small mt-1">Produk: <?= esc((string) $analysis['nama_produk']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= (float) ($analysis['persentase'] ?? 0) ?>%</span>
                                </td>
                                <td>
                                    <span class="badge <?= esc($kategoriBadgeClass($analysis['kategori'] ?? '')) ?>">
                                        <?= esc((string) ($analysis['kategori'] ?? '-')) ?>
                                    </span>
                                </td>
                                <td class="table-actions-cell">
                                    <div class="table-actions">
                                        <?php if (($analysis['mode'] ?? '') === 'validasi_instrumen'): ?>
                                            <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id']) ?>" class="btn btn-sm btn-light">
                                                Laporan
                                            </a>
                                        <?php elseif (($analysis['mode'] ?? '') === 'validasi_produk'): ?>
                                            <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id']) ?>" class="btn btn-sm btn-light">
                                                Laporan
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="toolbar">
    <a href="<?= base_url('admin/instruments') ?>" class="btn btn-primary">Kelola Instrumen</a>
    <a href="<?= base_url('admin/respondent-links') ?>" class="btn btn-light">Link Responden</a>
    <a href="<?= base_url('admin/reports') ?>" class="btn btn-light">Laporan</a>
</div>

<?= $this->endSection() ?>
