<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$modeLabel = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === 'validasi_instrumen') {
        return 'Validasi Instrumen';
    }

    return ucwords(str_replace('_', ' ', $mode));
};

$modeBadgeClass = static function (?string $mode): string {
    $mode = (string) $mode;

    if ($mode === 'validasi_instrumen') {
        return 'bg-blue text-blue-fg';
    }

    return 'bg-secondary text-secondary-fg';
};

?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Dashboard SIVALID</h2>
            <div class="text-muted mt-1">Ringkasan master instrumen, link responden, dan hasil pengisian.</div>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="<?= base_url('admin/instruments') ?>" class="btn btn-primary">Kelola Instrumen</a>
            <a href="<?= base_url('admin/hasil-validasi-instrumen') ?>" class="btn btn-light">Instrumen Siap Disebar</a>
            <a href="<?= base_url('admin/respondent-links') ?>" class="btn btn-light">Link Responden</a>
        </div>
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
                    <span class="badge bg-blue text-blue-fg">Instrumen</span>
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
                        <div class="label">Instrumen Siap Disebar</div>
                    </div>
                    <span class="badge bg-green text-green-fg">Siap</span>
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
                    <span class="badge bg-orange text-orange-fg">Distribusi</span>
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
                    <span class="badge bg-blue text-blue-fg">Respon</span>
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
                <table class="table table-vcenter table-hover table-sm">
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
                <table class="table table-vcenter table-hover table-sm">
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
                                    <div class="text-muted small"><?= esc(title_case_label((string) ($response['jenis_responden'] ?? '-'))) ?></div>
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

<?= $this->endSection() ?>
