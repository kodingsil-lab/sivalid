<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Hasil Validasi Instrumen</div>
            <h2 class="page-title"><?= esc($validatorSession['validator_nama'] ?? '-') ?></h2>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="<?= base_url('admin/hasil-validasi-instrumen') ?>" class="btn btn-light">
                &larr; Kembali
            </a>
            <a href="<?= base_url('admin/hasil-validasi-instrumen/' . ($validatorSession['id'] ?? 0) . '/excel') ?>" class="btn btn-success">
                Download Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-6"><strong>Paket:</strong> <?= esc($bundle['judul'] ?? '-') ?></div>
            <div class="col-md-6"><strong>Progress:</strong> <?= (int) ($summary['selesai_count'] ?? 0) ?>/<?= (int) ($summary['total_instrumen'] ?? 0) ?> selesai</div>
            <div class="col-md-6"><strong>Email:</strong> <?= esc($validatorSession['validator_email'] ?: '-') ?></div>
            <div class="col-md-6"><strong>Instansi:</strong> <?= esc($validatorSession['validator_instansi'] ?: '-') ?></div>
            <div class="col-md-6"><strong>Bidang:</strong> <?= esc($validatorSession['validator_bidang_keahlian'] ?: '-') ?></div>
            <div class="col-md-6"><strong>Terakhir Diisi:</strong><?= !empty($summary['last_saved_at']) ? ' ' . esc(format_tanggal_indonesia($summary['last_saved_at'], true)) : ' -' ?></div>
        </div>
    </div>
</div>

<?php if (empty($instrumentDetails)): ?>
    <div class="card">
        <div class="card-body">Tidak ada data instrumen.</div>
    </div>
<?php else: ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php foreach ($instrumentDetails as $detail): ?>
        <?php
        $status = $detail['status'] ?? 'belum';
        $badge = $status === 'selesai' ? 'bg-green-lt text-green' : ($status === 'proses' ? 'bg-yellow-lt text-yellow' : 'bg-secondary-lt text-secondary');
        $instrumentStatus = (string) ($detail['instrument']['instrument_status'] ?? '');
        $isInstrumentValid = in_array($instrumentStatus, ['Valid', 'Siap Disebar'], true);
        ?>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0">
                    <?= (int) ($detail['position'] ?? 0) ?>. <?= esc($detail['instrument']['judul'] ?? '-') ?>
                    <small class="text-muted">(<?= esc($detail['instrument']['kode'] ?? '-') ?>)</small>
                </h3>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isInstrumentValid): ?>
                        <span class="<?= esc(status_badge_class($instrumentStatus)) ?>"><?= esc(status_display_label($instrumentStatus)) ?></span>
                    <?php endif; ?>
                    <span class="badge <?= $badge ?>"><?= esc($status) ?></span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-sm">
                    <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th style="width:170px;">Aspek</th>
                        <th>Pernyataan</th>
                        <th style="width:100px;">Tipe</th>
                        <th style="width:80px;">Skor</th>
                        <th style="width:180px;">Jawaban Teks</th>
                        <th style="width:220px;">Komentar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detail['items'] as $row): ?>
                        <tr>
                            <td><?= esc((string) ($row['nomor'] ?? '-')) ?></td>
                            <td><?= esc($row['aspek'] ?? '-') ?></td>
                            <td><?= esc($row['pernyataan'] ?? '-') ?></td>
                            <td><?= esc($row['tipe_butir'] ?? '-') ?></td>
                            <td><?= esc($row['skor'] !== null ? (string) $row['skor'] : '-') ?></td>
                            <td><?= esc($row['jawaban_teks'] !== '' ? $row['jawaban_teks'] : '-') ?></td>
                            <td><?= esc($row['komentar'] !== '' ? $row['komentar'] : '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-body border-top">
                <div><strong>Kesimpulan:</strong> <?= esc($detail['kesimpulan'] ?: '-') ?></div>
                <div class="mt-1"><strong>Komentar Umum:</strong> <?= esc($detail['komentar_umum'] ?: '-') ?></div>

                <?php if ($status === 'selesai' && ! $isInstrumentValid): ?>
                    <form
                        action="<?= base_url('admin/hasil-validasi-instrumen/' . ($validatorSession['id'] ?? 0) . '/instrumen/' . ($detail['instrument']['instrument_id'] ?? 0) . '/tetapkan-valid') ?>"
                        method="post"
                        class="mt-3"
                        onsubmit="return confirm('Tetapkan instrumen ini sebagai Valid? Pastikan hasil validasi dan revisi sudah diperiksa.')"
                    >
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary">
                            Tetapkan sebagai Valid
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
