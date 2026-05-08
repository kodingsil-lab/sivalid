<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title ?? 'Link Validasi Instrumen') ?></h2>
            <div class="text-muted mt-1">Kelola link validasi dan proses analisis berdasarkan respon validator.</div>
        </div>
        <div class="col-auto ms-auto">
            <?php if (service('uri')->getSegment(2) === 'validasi-produk'): ?>
                <a href="<?= base_url('admin/validasi-produk/new') ?>" class="btn btn-primary">
                    + Buat Link Validasi Produk
                </a>
            <?php else: ?>
                <a href="<?= base_url('admin/instrument-links/new') ?>" class="btn btn-primary">
                    + Buat Link Validasi Instrumen
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

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

<?php if (empty($links)): ?>
    <div class="empty-state">
        Belum ada <?= strtolower(esc($title ?? 'link validasi instrumen')) ?>.
    </div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
    <div class="table-responsive">
    <table class="table table-vcenter table-hover table-sm">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Judul Link</th>
                <th>Instrumen</th>
                <th>Sasaran</th>
                <th>Mode</th>
                <th>Status</th>
                <th>Respon</th>
                <th>Link Publik</th>
                <th class="table-actions-cell">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $index => $link): ?>
                <?php $publicUrl = base_url('isi/' . $link['token']); ?>

                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <strong><?= esc($link['kode']) ?></strong><br>
                        <?= esc($link['judul']) ?>

                        <?php if (!empty($link['nama_produk'])): ?>
                            <hr>
                            <strong>Produk:</strong><br>
                            <?= esc($link['product_kode']) ?> - <?= esc($link['nama_produk']) ?><br>
                            <small><?= esc($link['jenis_produk']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($link['sasaran'] ?: '-') ?></td>
                    <td><?= esc($link['mode']) ?></td>
                    <td><span class="<?= esc(status_badge_class($link['status'] ?? '')) ?>"><?= esc($link['status']) ?></span></td>
                    <td>
                        <?= esc($link['jumlah_respon'] ?? 0) ?>
                        <?php if (!empty($link['maksimal_respon'])): ?>
                            / <?= esc($link['maksimal_respon']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input
                            type="text"
                            value="<?= esc($publicUrl) ?>"
                            class="form-control"
                            readonly
                            onclick="this.select();"
                        >
                        <small>Klik kotak link lalu salin.</small>
                    </td>
                    <td class="table-actions-cell">
                        <div class="table-actions">
                            <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-light">
                                Buka
                            </a>

                            <?php if ($link['mode'] === 'validasi_instrumen'): ?>
                                <form
                                    action="<?= base_url('admin/validasi-instrumen/proses/' . $link['id']) ?>"
                                    method="post"
                                    class="action-inline"
                                >
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary">
                                        Analisis
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($link['mode'] === 'validasi_produk'): ?>
                                <a href="<?= base_url('admin/validasi-produk/' . $link['id'] . '/edit') ?>" class="btn btn-warning">
                                    Edit
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('admin/instrument-links/' . $link['id'] . '/edit') ?>" class="btn btn-warning">
                                    Edit
                                </a>
                            <?php endif; ?>

                            <form
                                action="<?= $link['mode'] === 'validasi_produk' ? base_url('admin/validasi-produk/' . $link['id']) : base_url('admin/instrument-links/' . $link['id']) ?>"
                                method="post"
                                class="action-inline"
                                onsubmit="return confirm('Yakin ingin menghapus link ini?')"
                            >
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger">
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
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
