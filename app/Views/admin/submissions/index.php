<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$safeFilters = isset($filters) && is_array($filters) ? $filters : [];
$safeAllowedModes = isset($allowedModes) && is_array($allowedModes) ? $allowedModes : [];
$safeInstruments = isset($instruments) && is_array($instruments) ? $instruments : [];
$safeLinks = isset($links) && is_array($links) ? $links : [];
$safeProducts = isset($products) && is_array($products) ? $products : [];
$safeResponses = isset($responses) && is_array($responses) ? $responses : [];
$safeOffset = isset($offset) ? (int) $offset : 0;

$modeLabel = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === '') {
        return '-';
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
            <h2 class="page-title">Hasil Pengisian</h2>
            <div class="text-muted mt-1">Rekap hasil pengisian instrumen dari semua mode validasi dan respon.</div>
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
    <?php
    $activeFilters = array_filter($safeFilters, static function ($value) {
        return $value !== null && $value !== '';
    });

    $exportUrl = base_url('admin/submissions/export');

    if (!empty($activeFilters)) {
        $exportUrl .= '?' . http_build_query($activeFilters);
    }
    ?>

    <form action="<?= base_url('admin/submissions') ?>" method="get" class="filter-form">
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label" for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control">
                    <option value="">-- Semua Mode --</option>
                    <?php foreach ($safeAllowedModes as $modeOption): ?>
                        <option value="<?= esc((string) $modeOption) ?>" <?= ($safeFilters['mode'] ?? '') === $modeOption ? 'selected' : '' ?>>
                            <?= esc($modeLabel($modeOption)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="instrument_id">Instrumen</label>
                <select name="instrument_id" id="instrument_id" class="form-control">
                    <option value="">-- Semua Instrumen --</option>
                    <?php foreach ($safeInstruments as $instrument): ?>
                        <option value="<?= esc((string) ($instrument['id'] ?? '')) ?>" <?= ($safeFilters['instrument_id'] ?? '') === (string) ($instrument['id'] ?? '') ? 'selected' : '' ?>>
                            <?= esc((string) ($instrument['kode'] ?? '-')) ?> - <?= esc((string) ($instrument['judul'] ?? '-')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="instrument_link_id">Link Pengisian</label>
                <select name="instrument_link_id" id="instrument_link_id" class="form-control">
                    <option value="">-- Semua Link --</option>
                    <?php foreach ($safeLinks as $link): ?>
                        <option value="<?= esc((string) ($link['id'] ?? '')) ?>" <?= ($safeFilters['instrument_link_id'] ?? '') === (string) ($link['id'] ?? '') ? 'selected' : '' ?>>
                            <?= esc((string) ($link['kode'] ?? '-')) ?> - <?= esc((string) ($link['judul_link'] ?? '-')) ?> (<?= esc($modeLabel((string) ($link['mode'] ?? ''))) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="product_id">Produk</label>
                <select name="product_id" id="product_id" class="form-control">
                    <option value="">-- Semua Produk --</option>
                    <?php foreach ($safeProducts as $product): ?>
                        <option value="<?= esc((string) ($product['id'] ?? '')) ?>" <?= ($safeFilters['product_id'] ?? '') === (string) ($product['id'] ?? '') ? 'selected' : '' ?>>
                            <?= esc((string) ($product['kode'] ?? '-')) ?> - <?= esc((string) ($product['nama_produk'] ?? '-')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="date_from">Tanggal Dari</label>
                <input
                    type="date"
                    name="date_from"
                    id="date_from"
                    class="form-control"
                    value="<?= esc((string) ($safeFilters['date_from'] ?? '')) ?>"
                >
            </div>

            <div class="form-row">
                <label class="form-label" for="date_to">Tanggal Sampai</label>
                <input
                    type="date"
                    name="date_to"
                    id="date_to"
                    class="form-control"
                    value="<?= esc((string) ($safeFilters['date_to'] ?? '')) ?>"
                >
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-2">
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                <a href="<?= base_url('admin/submissions') ?>" class="btn btn-light btn-sm">Reset</a>
            </div>

            <a href="<?= esc($exportUrl) ?>" class="btn btn-light btn-sm">Export CSV</a>
        </div>
    </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('admin/submissions?mode=validasi_instrumen') ?>" class="btn btn-light btn-sm">Validasi Instrumen</a>
            <a href="<?= base_url('admin/submissions?mode=validasi_produk') ?>" class="btn btn-light btn-sm">Validasi Produk</a>
            <a href="<?= base_url('admin/submissions?mode=respon_mahasiswa') ?>" class="btn btn-light btn-sm">Respon Mahasiswa</a>
            <a href="<?= base_url('admin/submissions?mode=observasi') ?>" class="btn btn-light btn-sm">Observasi</a>
            <a href="<?= base_url('admin/submissions?mode=fgd') ?>" class="btn btn-light btn-sm">FGD</a>
            <a href="<?= base_url('admin/submissions?mode=tes_kinerja') ?>" class="btn btn-light btn-sm">Tes Kinerja</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
    <?php if (empty($safeResponses)): ?>
        <div class="empty-state">
            Belum ada hasil pengisian.
        </div>
    <?php else: ?>
        <?php
        $currentPage = isset($pager) ? $pager->getCurrentPage('submissions') : 1;
        $perPage = isset($pager) ? $pager->getPerPage('submissions') : 0;
        $total = isset($pager) ? $pager->getTotal('submissions') : count($safeResponses);
        $firstItem = $total > 0 && $perPage > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $lastItem = $total > 0 && $perPage > 0 ? min($currentPage * $perPage, $total) : $total;
        ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Responden</th>
                        <th style="width: 180px;">Mode</th>
                        <th>Instrumen</th>
                        <th style="width: 170px;">Produk</th>
                        <th style="width: 220px;">Kesimpulan</th>
                        <th style="width: 170px;">Waktu Submit</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($safeResponses as $index => $response): ?>
                        <tr>
                            <td class="text-muted"><?= $safeOffset + $index + 1 ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc((string) ($response['nama'] ?? '-')) ?></div>
                                <div class="small text-muted"><?= esc(title_case_label((string) ($response['jenis_responden'] ?? '-'))) ?></div>

                                <?php if (!empty($response['nim'])): ?>
                                    <div class="small text-muted">NIM: <?= esc((string) $response['nim']) ?></div>
                                <?php endif; ?>

                                <?php if (!empty($response['program_studi'])): ?>
                                    <div class="small text-muted">Prodi: <?= esc((string) $response['program_studi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?= esc($modeBadgeClass((string) ($response['mode'] ?? ''))) ?>">
                                    <?= esc($modeLabel((string) ($response['mode'] ?? ''))) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= esc((string) ($response['kode'] ?? '-')) ?></div>
                                <div class="small text-muted"><?= esc((string) ($response['judul'] ?? '-')) ?></div>
                                <div class="small text-muted"><?= esc((string) ($response['judul_link'] ?? '-')) ?></div>
                            </td>
                            <td><?= esc((string) (!empty($response['nama_produk']) ? $response['nama_produk'] : '-')) ?></td>
                            <td><?= esc((string) (!empty($response['kesimpulan']) ? $response['kesimpulan'] : '-')) ?></td>
                            <td class="text-muted"><?= esc((string) (!empty($response['submitted_at']) ? $response['submitted_at'] : '-')) ?></td>
                            <td class="table-actions-cell">
                                <div class="table-actions">
                                    <a href="<?= base_url('admin/submissions/' . $response['id']) ?>" class="btn btn-sm btn-light">
                                        Detail
                                    </a>

                                    <form
                                        action="<?= base_url('admin/submissions/' . $response['id']) ?>"
                                        method="post"
                                        class="action-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus hasil pengisian ini?')"
                                    >
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                <div class="text-muted small">
                    Menampilkan <?= esc((string) $firstItem) ?> sampai <?= esc((string) $lastItem) ?> dari <?= esc((string) $total) ?> entri
                </div>
                <div><?= $pager->links('submissions', 'default_full') ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
