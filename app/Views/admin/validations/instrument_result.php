<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title"><?= esc($title ?? 'Analisis Validasi Instrumen') ?></h1>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Daftar Link Validasi Instrumen</h3>
    <p>
        Pilih link validasi instrumen yang sudah memiliki respon validator, lalu proses analisis.
    </p>
</div>

<div class="toolbar">
    <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-primary">
        Lihat Instrumen Valid
    </a>

    <a href="<?= base_url('admin/instrument-revisions') ?>" class="btn btn-light">
        Revisi Butir
    </a>
</div>

<?php if (empty($links)): ?>
    <div class="empty-state">
        Belum ada link validasi instrumen.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Judul Link</th>
                <th>Instrumen</th>
                <th>Status Link</th>
                <th style="width: 90px;">Respon</th>
                <th>Analisis Terakhir</th>
                <th style="width: 260px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $index => $link): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($link['judul_link']) ?></td>
                    <td>
                        <strong><?= esc($link['kode']) ?></strong><br>
                        <?= esc($link['judul']) ?>
                    </td>
                    <td>
                        <span class="badge"><?= esc($link['status']) ?></span>
                    </td>
                    <td><?= esc($link['jumlah_respon'] ?? 0) ?></td>
                    <td>
                        <?php if (!empty($link['analysis'])): ?>
                            <strong><?= esc($link['analysis']['persentase']) ?>%</strong><br>
                            <?= esc($link['analysis']['kategori']) ?>
                        <?php else: ?>
                            <span class="badge">Belum dianalisis</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form
                            action="<?= base_url('admin/validasi-instrumen/proses/' . $link['id']) ?>"
                            method="post"
                            class="action-inline"
                        >
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary">
                                Proses Analisis
                            </button>
                        </form>

                        <?php if (!empty($link['analysis'])): ?>
                            <a href="<?= base_url('admin/validasi-instrumen/analisis/' . $link['analysis']['id']) ?>" class="btn btn-light">
                                Lihat
                            </a>

                            <?php if (($link['instrument_status'] ?? '') !== 'Valid'): ?>
                                <form
                                    action="<?= base_url('admin/validasi-instrumen/tetapkan-valid/' . $link['id']) ?>"
                                    method="post"
                                    class="action-inline"
                                    onsubmit="return confirm('Tetapkan instrumen ini sebagai Valid dan tutup link validasi?')"
                                >
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-warning">
                                        Tetapkan Valid
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
