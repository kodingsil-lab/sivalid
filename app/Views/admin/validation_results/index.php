<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Validasi Instrumen</div>
            <h2 class="page-title">Hasil Validasi Instrumen</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Penilaian Validator</h3>
    </div>

    <?php if (empty($sessions)): ?>
        <div class="card-body">
            <div class="empty">Belum ada validator yang mengisi paket validasi.</div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-sm">
                <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Paket</th>
                    <th>Validator</th>
                    <th>Instansi</th>
                    <th style="width:120px;">Instrumen</th>
                    <th style="width:120px;">Status</th>
                    <th style="width:160px;">Terakhir Diisi</th>
                    <th style="width:190px;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sessions as $i => $session): ?>
                    <?php
                    $total = (int) ($session['total_instrumen'] ?? 0);
                    $selesai = (int) ($session['selesai_count'] ?? 0);
                    $isDone = $total > 0 && $selesai >= $total;
                    $badge = $isDone ? 'bg-green-lt text-green' : 'bg-yellow-lt text-yellow';
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold"><?= esc($session['bundle_judul'] ?? '-') ?></div>
                            <div class="text-muted small">Token: <?= esc($session['bundle_token'] ?? '-') ?></div>
                        </td>
                        <td>
                            <div class="fw-semibold"><?= esc($session['validator_nama'] ?? '-') ?></div>
                            <div class="text-muted small"><?= esc($session['validator_email'] ?: '-') ?></div>
                        </td>
                        <td><?= esc($session['validator_instansi'] ?: '-') ?></td>
                        <td><?= $selesai ?>/<?= $total ?> selesai</td>
                        <td><span class="badge <?= $badge ?>"><?= $isDone ? 'Selesai' : 'Proses' ?></span></td>
                        <td><?= !empty($session['last_saved_at']) ? esc(format_tanggal_indonesia($session['last_saved_at'], true)) : '-' ?></td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="<?= base_url('admin/hasil-validasi-instrumen/' . $session['id']) ?>" class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                                <a href="<?= base_url('admin/hasil-validasi-instrumen/' . $session['id'] . '/excel') ?>" class="btn btn-sm btn-success">
                                    Excel
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager): ?>
            <div class="card-footer">
                <?= $pager->links($pagerGroup ?? 'default', 'default_full') ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
