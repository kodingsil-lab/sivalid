<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Paket Validasi</div>
            <h2 class="page-title">Monitor Validator: <?= esc($bundle['judul'] ?? '-') ?></h2>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="<?= base_url('admin/instrument-bundles/' . ($bundle['id'] ?? 0)) ?>" class="btn btn-light">
                &larr; Kembali ke Detail Paket
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Sesi Validator</h3>
    </div>

    <?php if (empty($sessions)): ?>
        <div class="card-body">
            <div class="empty">Belum ada validator yang memulai sesi.</div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-vcenter table-sm">
                <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Validator</th>
                    <th>Email</th>
                    <th>Instansi</th>
                    <th style="width:130px;">Progress</th>
                    <th style="width:150px;">Mulai</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sessions as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($s['validator_nama'] ?? '-') ?></td>
                        <td><?= esc($s['validator_email'] ?: '-') ?></td>
                        <td><?= esc($s['validator_instansi'] ?: '-') ?></td>
                        <td><?= (int) ($s['selesai_count'] ?? 0) ?>/<?= (int) ($s['total'] ?? 0) ?></td>
                        <td><?= !empty($s['started_at']) ? esc(format_tanggal_indonesia($s['started_at'], true)) : '-' ?></td>
                        <td>
                            <a href="<?= base_url('admin/instrument-bundles/' . ($bundle['id'] ?? 0) . '/sessions/' . $s['id']) ?>" class="btn btn-sm btn-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
