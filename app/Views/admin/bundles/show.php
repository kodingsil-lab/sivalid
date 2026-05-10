<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title) ?></h2>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/sessions') ?>" class="btn btn-success">
                Monitor Validator
            </a>
            <a href="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/edit') ?>" class="btn btn-primary">
                Edit Paket
            </a>
            <form action="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/duplicate') ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-light" onclick="return confirm('Duplikat paket ini untuk validator lain?')">
                    Duplikat Paket
                </button>
            </form>
            <a href="<?= base_url('admin/instrument-bundles') ?>" class="btn btn-light">
                &larr; Kembali
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Paket</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7">
                        <span class="<?= esc(status_badge_class($bundle['status'] ?? '')) ?>">
                            <?= esc($bundle['status']) ?>
                        </span>
                    </dd>

                    <dt class="col-sm-5">Validator</dt>
                    <dd class="col-sm-7"><?= esc($bundle['sasaran'] ?: '-') ?></dd>

                    <dt class="col-sm-5">Tanggal Mulai</dt>
                    <dd class="col-sm-7">
                        <?= !empty($bundle['tanggal_mulai']) ? esc(format_tanggal_indonesia($bundle['tanggal_mulai'])) : '-' ?>
                    </dd>

                    <dt class="col-sm-5">Tanggal Selesai</dt>
                    <dd class="col-sm-7">
                        <?= !empty($bundle['tanggal_selesai']) ? esc(format_tanggal_indonesia($bundle['tanggal_selesai'])) : '-' ?>
                    </dd>

                    <dt class="col-sm-5">Token Expired</dt>
                    <dd class="col-sm-7">
                        <?= !empty($bundle['token_expires_at']) ? esc(format_tanggal_indonesia($bundle['token_expires_at'], true)) : '-' ?>
                    </dd>

                    <dt class="col-sm-5">Status Token</dt>
                    <dd class="col-sm-7">
                        <?php if (!empty($bundle['token_revoked_at'])): ?>
                            <span class="badge bg-red-lt text-red">Revoked</span>
                        <?php else: ?>
                            <span class="badge bg-green-lt text-green">Aktif</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Link Publik</h3>
            </div>
            <div class="card-body">
                <?php $publicUrl = base_url('paket/' . $bundle['token']); ?>
                <input
                    type="text"
                    value="<?= esc($publicUrl) ?>"
                    class="form-control mb-2"
                    readonly
                    onclick="this.select();"
                >
                <a href="<?= esc($publicUrl) ?>" target="_blank" class="btn btn-sm btn-light w-100">
                    Buka Link Publik
                </a>

                <?php if (!empty($bundle['token_revoked_at'])): ?>
                    <form action="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/activate-token') ?>" method="post" class="mt-2">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-success w-100">Aktifkan Kembali Token</button>
                    </form>
                <?php else: ?>
                    <form action="<?= base_url('admin/instrument-bundles/' . $bundle['id'] . '/revoke-token') ?>" method="post" class="mt-2">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Revoke token publik paket ini?')">
                            Revoke Token
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <?php if (!empty($bundle['deskripsi'])): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Deskripsi</h3>
                </div>
                <div class="card-body bundle-description-preview">
                    <?= render_rich_text_content($bundle['deskripsi']) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Instrumen dalam Paket
                    <span class="badge bg-blue-lt ms-1"><?= count($instruments) ?></span>
                </h3>
            </div>
            <?php if (empty($instruments)): ?>
                <div class="card-body">
                    <div class="empty-state">Belum ada instrumen dalam paket ini.</div>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter table-sm">
                    <thead>
                        <tr>
                            <th style="width:56px;">No</th>
                            <th>Kode</th>
                            <th>Judul Instrumen</th>
                            <th>Jenis</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instruments as $i => $instr): ?>
                            <tr>
                                <td><?= esc(sprintf('%03d', $i + 1)) ?></td>
                                <td><code><?= esc($instr['kode']) ?></code></td>
                                <td><?= esc($instr['judul']) ?></td>
                                <td><?= esc(title_case_label((string) ($instr['jenis'] ?? '-'))) ?></td>
                                <td>
                                    <span class="<?= esc(status_badge_class($instr['instrument_status'] ?? '')) ?>">
                                        <?= esc(status_display_label((string) ($instr['instrument_status'] ?? ''))) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .bundle-description-preview .rich-text-content {
        padding: 0;
        line-height: 1.55;
    }

    .bundle-description-preview .rich-text-content p,
    .bundle-description-preview .rich-text-content ol,
    .bundle-description-preview .rich-text-content ul {
        margin-bottom: .65rem;
    }

    .bundle-description-preview .rich-text-content > :last-child {
        margin-bottom: 0;
    }
</style>

<?= $this->endSection() ?>
