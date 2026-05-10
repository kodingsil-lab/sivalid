<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Monitor Validator</div>
            <h2 class="page-title">Detail Sesi: <?= esc($validatorSession['validator_nama'] ?? '-') ?></h2>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="<?= base_url('admin/instrument-bundles/' . ($bundle['id'] ?? 0) . '/sessions') ?>" class="btn btn-light">
                &larr; Kembali ke Daftar Sesi
            </a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-6"><strong>Paket:</strong> <?= esc($bundle['judul'] ?? '-') ?></div>
            <div class="col-md-6"><strong>Email:</strong> <?= esc($validatorSession['validator_email'] ?: '-') ?></div>
            <div class="col-md-6"><strong>Instansi:</strong> <?= esc($validatorSession['validator_instansi'] ?: '-') ?></div>
            <div class="col-md-6"><strong>Bidang:</strong> <?= esc($validatorSession['validator_bidang_keahlian'] ?: '-') ?></div>
            <div class="col-md-6"><strong>Mulai:</strong><?= !empty($validatorSession['started_at']) ? ' ' . esc(format_tanggal_indonesia($validatorSession['started_at'], true)) : ' -' ?></div>
        </div>
    </div>
</div>

<?php if (empty($instrumentDetails)): ?>
    <div class="card">
        <div class="card-body">Tidak ada data instrumen.</div>
    </div>
<?php else: ?>
    <?php foreach ($instrumentDetails as $detail): ?>
        <?php
        $status = $detail['status'] ?? 'belum';
        $badge = $status === 'selesai' ? 'bg-green-lt text-green' : ($status === 'proses' ? 'bg-yellow-lt text-yellow' : 'bg-secondary-lt text-secondary');
        ?>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0">
                    <?= (int) ($detail['position'] ?? 0) ?>. <?= esc($detail['instrument']['judul'] ?? '-') ?>
                    <small class="text-muted">(<?= esc($detail['instrument']['kode'] ?? '-') ?>)</small>
                </h3>
                <span class="badge <?= $badge ?>"><?= esc($status) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-sm">
                    <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th style="width:180px;">Aspek</th>
                        <th>Pernyataan</th>
                        <th style="width:120px;">Jawaban</th>
                        <th style="width:220px;">Komentar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detail['items'] as $row): ?>
                        <tr>
                            <td><?= esc((string) ($row['nomor'] ?? '-')) ?></td>
                            <td><?= esc($row['aspek'] ?? '-') ?></td>
                            <td><?= esc($row['pernyataan'] ?? '-') ?></td>
                            <td><?= esc($row['jawaban'] ?? '-') ?></td>
                            <td><?= esc($row['komentar'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-body border-top">
                <div><strong>Kesimpulan:</strong> <?= esc($detail['kesimpulan'] ?: '-') ?></div>
                <div class="mt-1"><strong>Komentar Umum:</strong> <?= esc($detail['komentar_umum'] ?: '-') ?></div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
