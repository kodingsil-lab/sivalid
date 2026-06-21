<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$safeFilters = isset($filters) && is_array($filters) ? $filters : [];
$safeAllowedModes = isset($allowedModes) && is_array($allowedModes) ? $allowedModes : [];
$safeInstruments = isset($instruments) && is_array($instruments) ? $instruments : [];
$safeInstrumentTypes = isset($instrumentTypes) && is_array($instrumentTypes) ? $instrumentTypes : [];
$safeLinks = isset($links) && is_array($links) ? $links : [];
$safeProducts = isset($products) && is_array($products) ? $products : [];
$safeSummaries = isset($summaries) && is_array($summaries) ? $summaries : [];
$safeOffset = isset($offset) ? (int) $offset : 0;

$modeLabel = static function (?string $mode): string {
    $mode = (string) $mode;

    $labels = [
        'validasi_instrumen' => 'Validasi Instrumen',
        'validasi_produk'    => 'Validasi Produk',
        'respon_mahasiswa'   => 'Pengisian Responden',
        'observasi'          => 'Observasi',
        'fgd'                => 'FGD',
        'tes_kinerja'        => 'Penilaian Kinerja',
    ];

    if ($mode === '') {
        return '-';
    }

    if (isset($labels[$mode])) {
        return $labels[$mode];
    }

    return ucwords(str_replace('_', ' ', $mode));
};

$categoryLabel = static function (array $row) use ($modeLabel): string {
    $jenis = trim((string) ($row['jenis'] ?? ''));
    if ($jenis !== '') {
        return title_case_label($jenis);
    }

    $judulLink = trim((string) ($row['judul_link'] ?? ''));
    if ($judulLink !== '') {
        return $judulLink;
    }

    return $modeLabel((string) ($row['mode'] ?? ''));
};

$respondentTypeLabel = static function (array $row): string {
    $templateKey = trim((string) ($row['identity_template'] ?? ''));
    $templates = \App\Libraries\RespondentIdentitySchema::templates();

    if ($templateKey !== '' && isset($templates[$templateKey])) {
        return (string) $templates[$templateKey]['label'];
    }

    $fields = [];
    if (!empty($row['identity_fields'])) {
        $decodedFields = json_decode((string) $row['identity_fields'], true);
        $fields = is_array($decodedFields) ? $decodedFields : [];
    }

    foreach ($fields as $field) {
        $label = strtolower((string) ($field['label'] ?? ''));

        if (str_contains($label, 'dosen')) {
            return 'Dosen';
        }

        if (str_contains($label, 'mahasiswa') || str_contains($label, 'nim')) {
            return 'Mahasiswa';
        }

        if (str_contains($label, 'guru') || str_contains($label, 'praktisi')) {
            return 'Guru / Praktisi';
        }

        if (str_contains($label, 'validator') || str_contains($label, 'ahli')) {
            return 'Validator / Ahli';
        }
    }

    return title_case_label((string) ($row['jenis_responden'] ?? '-'));
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

    $exportReportUrl = base_url('admin/submissions/export/report');

    if (!empty($activeFilters)) {
        $queryString = '?' . http_build_query($activeFilters);
        $exportReportUrl .= $queryString;
    }
    ?>

    <form action="<?= base_url('admin/submissions') ?>" method="get" class="filter-form">
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label" for="jenis">Jenis Instrumen</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="">-- Semua Jenis Instrumen --</option>
                    <?php foreach ($safeInstrumentTypes as $jenisOption): ?>
                        <option value="<?= esc((string) $jenisOption) ?>" <?= ($safeFilters['jenis'] ?? '') === (string) $jenisOption ? 'selected' : '' ?>>
                            <?= esc(title_case_label((string) $jenisOption)) ?>
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
                            <?= esc((string) ($link['kode'] ?? '-')) ?> - <?= esc((string) ($link['judul_link'] ?? '-')) ?> (<?= esc($categoryLabel($link)) ?>)
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

            <div class="d-flex flex-wrap gap-2">
                <a href="<?= esc($exportReportUrl) ?>" class="btn btn-primary btn-sm">Export Laporan</a>
            </div>
        </div>
    </form>
    </div>
</div>

<?php if (count($safeLinks) > 1): ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="fw-semibold mb-2">Filter cepat berdasarkan link/instrumen</div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('admin/submissions') ?>" class="btn btn-sm <?= empty($safeFilters['instrument_link_id']) && empty($safeFilters['mode']) && empty($safeFilters['jenis']) ? 'btn-primary' : 'btn-light' ?>">
                    Semua
                </a>
                <?php foreach ($safeLinks as $link): ?>
                    <?php
                    $linkId = (string) ($link['id'] ?? '');
                    $isActiveLink = ($safeFilters['instrument_link_id'] ?? '') === $linkId;
                    $buttonLabel = trim((string) ($link['judul_link'] ?? ''));
                    if ($buttonLabel === '') {
                        $buttonLabel = trim((string) ($link['jenis'] ?? ''));
                    }
                    if ($buttonLabel === '') {
                        $buttonLabel = trim((string) ($link['judul'] ?? 'Link Pengisian'));
                    }
                    ?>
                    <a href="<?= base_url('admin/submissions?instrument_link_id=' . rawurlencode($linkId)) ?>" class="btn btn-sm <?= $isActiveLink ? 'btn-primary' : 'btn-light' ?>">
                        <?= esc($buttonLabel) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
    <?php if (empty($safeSummaries)): ?>
        <div class="empty-state">
            Belum ada hasil pengisian.
        </div>
    <?php else: ?>
        <?php
        $total = count($safeSummaries);
        ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Instrumen</th>
                        <th style="width: 220px;">Jenis Instrumen</th>
                        <th>Link Pengisian</th>
                        <th style="width: 150px;">Kategori Pengisi</th>
                        <th style="width: 130px;">Jumlah Pengisi</th>
                        <th style="width: 170px;">Submit Terakhir</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($safeSummaries as $index => $summary): ?>
                        <?php
                        $summaryFilters = array_filter($safeFilters + [
                            'instrument_link_id' => (string) ($summary['instrument_link_id'] ?? ''),
                        ], static fn ($value) => $value !== null && $value !== '');
                        $summaryFilters['instrument_link_id'] = (string) ($summary['instrument_link_id'] ?? '');
                        $summaryQuery = http_build_query($summaryFilters);
                        $summaryUrlSuffix = $summaryQuery !== '' ? '?' . $summaryQuery : '';
                        ?>
                        <tr>
                            <td class="text-muted"><?= $safeOffset + $index + 1 ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc((string) ($summary['kode'] ?? '-')) ?></div>
                                <div><?= esc((string) ($summary['judul'] ?? '-')) ?></div>
                                <?php if (!empty($summary['nama_produk'])): ?>
                                    <div class="small text-muted">Produk: <?= esc((string) $summary['nama_produk']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-green text-green-fg">
                                    <?= esc(title_case_label((string) ($summary['jenis'] ?? '-'))) ?>
                                </span>
                            </td>
                            <td>
                                <?= esc((string) (!empty($summary['judul_link']) ? $summary['judul_link'] : '-')) ?>
                            </td>
                            <td><?= esc($respondentTypeLabel($summary)) ?></td>
                            <td><span class="fw-semibold"><?= esc((string) ($summary['total_responses'] ?? 0)) ?></span> entri</td>
                            <td class="text-muted"><?= esc((string) (!empty($summary['last_submitted_at']) ? $summary['last_submitted_at'] : '-')) ?></td>
                            <td class="table-actions-cell">
                                <div class="table-actions">
                                    <a href="<?= base_url('admin/submissions/export/report' . $summaryUrlSuffix) ?>" class="btn btn-sm btn-primary">
                                        Export Laporan
                                    </a>
                                    <form
                                        action="<?= base_url('admin/submissions/delete-summary') ?>"
                                        method="post"
                                        class="action-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus semua hasil pengisian pada rekap ini? Data jawaban responden akan ikut terhapus.');"
                                    >
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="instrument_link_id" value="<?= esc((string) ($summary['instrument_link_id'] ?? '')) ?>">
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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                <div class="text-muted small">
                    Menampilkan <?= esc((string) $total) ?> rekap instrumen/link pengisian.
                </div>
            </div>
    <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
