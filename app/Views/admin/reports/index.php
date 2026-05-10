<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$safeAnalyses = isset($analyses) && is_array($analyses) ? $analyses : [];
$safeLinks = isset($links) && is_array($links) ? $links : [];

$modeLabel = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === 'validasi_instrumen') {
        return 'Validasi Instrumen';
    }

    if ($mode === 'validasi_produk') {
        return 'Validasi Produk';
    }

    if ($mode === 'respon_mahasiswa') {
        return 'Respon Mahasiswa';
    }

    if ($mode === 'tes_kinerja') {
        return 'Tes Kinerja';
    }

    return ucwords(str_replace('_', ' ', $mode));
};

$modeBadgeClass = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === 'validasi_instrumen') {
        return 'badge bg-blue text-blue-fg';
    }

    if ($mode === 'validasi_produk') {
        return 'badge bg-orange text-orange-fg';
    }

    if (in_array($mode, ['respon_mahasiswa', 'observasi', 'fgd', 'tes_kinerja'], true)) {
        return 'badge bg-green text-green-fg';
    }

    return 'badge bg-secondary text-secondary-fg';
};

?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Laporan</h2>
            <div class="text-muted mt-1">Pusat laporan validasi instrumen, validasi produk, dan laporan respon pengisian.</div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc((string) session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-2">Daftar Laporan Hasil Analisis</h3>
        <div class="text-muted">
            Bagian ini menampilkan laporan validasi instrumen dan validasi produk yang sudah dianalisis.
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
    <?php if (empty($safeAnalyses)): ?>
        <div class="empty-state">
            Belum ada data analisis validasi.
        </div>
    <?php else: ?>
            <?php
            $currentPageAnalyses = isset($pagerAnalyses) ? $pagerAnalyses->getCurrentPage($pagerGroupAnalyses) : 1;
            $perPageAnalyses = isset($pagerAnalyses) ? $pagerAnalyses->getPerPage($pagerGroupAnalyses) : 0;
            $totalAnalyses = isset($pagerAnalyses) ? $pagerAnalyses->getTotal($pagerGroupAnalyses) : count($safeAnalyses);
            $offsetAnalyses = $perPageAnalyses > 0 ? (($currentPageAnalyses - 1) * $perPageAnalyses) : 0;
            $firstAnalyses = $totalAnalyses > 0 && $perPageAnalyses > 0 ? $offsetAnalyses + 1 : 0;
            $lastAnalyses = $totalAnalyses > 0 && $perPageAnalyses > 0 ? min($currentPageAnalyses * $perPageAnalyses, $totalAnalyses) : $totalAnalyses;
            ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 180px;">Jenis Laporan</th>
                        <th>Instrumen / Produk</th>
                        <th style="width: 120px;">Responden</th>
                        <th style="width: 120px;">Persentase</th>
                        <th style="width: 160px;">Kategori</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($safeAnalyses as $index => $analysis): ?>
                        <?php $analysisMode = (string) ($analysis['mode'] ?? ''); ?>
                        <tr>
                            <td class="text-muted"><?= $offsetAnalyses + $index + 1 ?></td>
                            <td>
                                <span class="<?= esc($modeBadgeClass($analysisMode)) ?>">
                                    <?= esc($modeLabel($analysisMode)) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= esc((string) ($analysis['kode'] ?? '-')) ?></div>
                                <div class="small text-muted"><?= esc((string) ($analysis['judul'] ?? '-')) ?></div>

                                <?php if (!empty($analysis['nama_produk'])): ?>
                                    <div class="small mt-1">
                                        Produk: <?= esc((string) ($analysis['product_kode'] ?? '-')) ?> - <?= esc((string) $analysis['nama_produk']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) ($analysis['jumlah_responden'] ?? 0) ?></td>
                            <td><span class="fw-semibold"><?= esc((string) ($analysis['persentase'] ?? 0)) ?>%</span></td>
                            <td>
                                <span class="<?= esc(status_badge_class((string) ($analysis['kategori'] ?? ''))) ?>">
                                    <?= esc((string) ($analysis['kategori'] ?? '-')) ?>
                                </span>
                            </td>
                            <td class="table-actions-cell">
                                <div class="table-actions">
                                    <?php if ($analysisMode === 'validasi_instrumen'): ?>
                                        <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id']) ?>" class="btn btn-sm btn-light">
                                            Buka
                                        </a>
                                        <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/print') ?>" target="_blank" class="btn btn-sm btn-light">
                                            Cetak HTML
                                        </a>
                                        <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/pdf-preview') ?>" target="_blank" class="btn btn-sm btn-light">
                                            Preview PDF
                                        </a>
                                        <a href="<?= base_url('admin/reports/validasi-instrumen/' . $analysis['id'] . '/pdf') ?>" class="btn btn-sm btn-light">
                                            Unduh PDF
                                        </a>
                                    <?php elseif ($analysisMode === 'validasi_produk'): ?>
                                        <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id']) ?>" class="btn btn-sm btn-light">
                                            Buka
                                        </a>
                                        <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id'] . '/print') ?>" target="_blank" class="btn btn-sm btn-light">
                                            Cetak HTML
                                        </a>
                                        <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id'] . '/pdf-preview') ?>" target="_blank" class="btn btn-sm btn-light">
                                            Preview PDF
                                        </a>
                                        <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id'] . '/pdf') ?>" class="btn btn-sm btn-light">
                                            Unduh PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Aksi belum tersedia</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($pagerAnalyses) && !empty($pagerGroupAnalyses)): ?>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 px-3 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan <?= esc((string) $firstAnalyses) ?> sampai <?= esc((string) $lastAnalyses) ?> dari <?= esc((string) $totalAnalyses) ?> entri
                </div>
                <div><?= $pagerAnalyses->links($pagerGroupAnalyses, 'default_full') ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title mb-2">Laporan Pengisian Responden</h3>
        <div class="text-muted">
            Bagian ini menampilkan laporan angket mahasiswa, observasi, FGD, dan instrumen pengisian lain.
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
    <?php if (empty($safeLinks)): ?>
        <div class="empty-state">
            Belum ada link pengisian responden.
        </div>
    <?php else: ?>
        <?php
        $currentPageLinks = isset($pagerLinks) ? $pagerLinks->getCurrentPage($pagerGroupLinks) : 1;
        $perPageLinks = isset($pagerLinks) ? $pagerLinks->getPerPage($pagerGroupLinks) : 0;
        $totalLinks = isset($pagerLinks) ? $pagerLinks->getTotal($pagerGroupLinks) : count($safeLinks);
        $offsetLinks = $perPageLinks > 0 ? (($currentPageLinks - 1) * $perPageLinks) : 0;
        $firstLinks = $totalLinks > 0 && $perPageLinks > 0 ? $offsetLinks + 1 : 0;
        $lastLinks = $totalLinks > 0 && $perPageLinks > 0 ? min($currentPageLinks * $perPageLinks, $totalLinks) : $totalLinks;
        ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 180px;">Mode</th>
                        <th>Judul Link</th>
                        <th>Instrumen</th>
                        <th style="width: 130px;">Jumlah Respon</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($safeLinks as $index => $link): ?>
                        <?php $linkMode = (string) ($link['mode'] ?? ''); ?>
                        <tr>
                            <td class="text-muted"><?= $offsetLinks + $index + 1 ?></td>
                            <td>
                                <span class="<?= esc($modeBadgeClass($linkMode)) ?>">
                                    <?= esc($modeLabel($linkMode)) ?>
                                </span>
                            </td>
                            <td><?= esc((string) ($link['judul_link'] ?? '-')) ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc((string) ($link['kode'] ?? '-')) ?></div>
                                <div class="small text-muted"><?= esc((string) ($link['judul'] ?? '-')) ?></div>
                            </td>
                            <td><?= (int) ($link['jumlah_respon'] ?? 0) ?></td>
                            <td class="table-actions-cell">
                                <div class="table-actions">
                                    <?php if ($linkMode === 'respon_mahasiswa'): ?>
                                        <a href="<?= base_url('admin/reports/respon-mahasiswa/' . $link['id']) ?>" class="btn btn-sm btn-primary">
                                            Laporan
                                        </a>
                                    <?php elseif ($linkMode === 'observasi'): ?>
                                        <a href="<?= base_url('admin/reports/observasi/' . $link['id']) ?>" class="btn btn-sm btn-primary">
                                            Laporan
                                        </a>
                                    <?php elseif ($linkMode === 'fgd'): ?>
                                        <a href="<?= base_url('admin/reports/fgd/' . $link['id']) ?>" class="btn btn-sm btn-primary">
                                            Laporan
                                        </a>
                                    <?php elseif ($linkMode === 'tes_kinerja'): ?>
                                        <a href="<?= base_url('admin/reports/tes-kinerja/' . $link['id']) ?>" class="btn btn-sm btn-primary">
                                            Laporan
                                        </a>
                                    <?php else: ?>
                                        <span class="<?= esc(status_badge_class('Belum tersedia')) ?>">Belum tersedia</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($pagerLinks) && !empty($pagerGroupLinks)): ?>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 px-3 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan <?= esc((string) $firstLinks) ?> sampai <?= esc((string) $lastLinks) ?> dari <?= esc((string) $totalLinks) ?> entri
                </div>
                <div><?= $pagerLinks->links($pagerGroupLinks, 'default_full') ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3 class="card-title mb-2">Laporan Revisi Butir</h3>
        <div class="text-muted mb-3">
            Laporan ini menampilkan riwayat revisi butir instrumen berdasarkan hasil validasi dan komentar validator.
        </div>

        <a href="<?= base_url('admin/reports/revisi-butir') ?>" class="btn btn-primary">
            Buka Laporan Revisi Butir
        </a>
    </div>
</div>

<?= $this->endSection() ?>
