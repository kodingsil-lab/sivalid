<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Hasil Pengisian</h1>

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
    <?php
    $activeFilters = array_filter($filters ?? [], static function ($value) {
        return $value !== null && $value !== '';
    });

    $exportUrl = base_url('admin/submissions/export');

    if (!empty($activeFilters)) {
        $exportUrl .= '?' . http_build_query($activeFilters);
    }
    ?>

    <form action="<?= base_url('admin/submissions') ?>" method="get">
        <div class="form-grid">
            <div class="form-row">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control">
                    <option value="">-- Semua Mode --</option>
                    <?php foreach ($allowedModes as $modeOption): ?>
                        <option value="<?= esc($modeOption) ?>" <?= ($filters['mode'] ?? '') === $modeOption ? 'selected' : '' ?>>
                            <?= esc(str_replace('_', ' ', strtoupper($modeOption))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="instrument_id">Instrumen</label>
                <select name="instrument_id" id="instrument_id" class="form-control">
                    <option value="">-- Semua Instrumen --</option>
                    <?php foreach ($instruments as $instrument): ?>
                        <option value="<?= esc($instrument['id']) ?>" <?= ($filters['instrument_id'] ?? '') === (string) $instrument['id'] ? 'selected' : '' ?>>
                            <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="instrument_link_id">Link Pengisian</label>
                <select name="instrument_link_id" id="instrument_link_id" class="form-control">
                    <option value="">-- Semua Link --</option>
                    <?php foreach ($links as $link): ?>
                        <option value="<?= esc($link['id']) ?>" <?= ($filters['instrument_link_id'] ?? '') === (string) $link['id'] ? 'selected' : '' ?>>
                            <?= esc($link['kode']) ?> - <?= esc($link['judul_link']) ?> (<?= esc($link['mode']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="product_id">Produk</label>
                <select name="product_id" id="product_id" class="form-control">
                    <option value="">-- Semua Produk --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= esc($product['id']) ?>" <?= ($filters['product_id'] ?? '') === (string) $product['id'] ? 'selected' : '' ?>>
                            <?= esc($product['kode']) ?> - <?= esc($product['nama_produk']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="date_from">Tanggal Dari</label>
                <input
                    type="date"
                    name="date_from"
                    id="date_from"
                    class="form-control"
                    value="<?= esc($filters['date_from'] ?? '') ?>"
                >
            </div>

            <div class="form-row">
                <label for="date_to">Tanggal Sampai</label>
                <input
                    type="date"
                    name="date_to"
                    id="date_to"
                    class="form-control"
                    value="<?= esc($filters['date_to'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="toolbar" style="margin-top: 10px; margin-bottom: 0;">
            <div>
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="<?= base_url('admin/submissions') ?>" class="btn btn-light">
                    Reset
                </a>
            </div>

            <a href="<?= esc($exportUrl) ?>" class="btn btn-light">
                Export CSV
            </a>
        </div>
    </form>
</div>

<div class="toolbar">
    <a href="<?= base_url('admin/submissions?mode=validasi_instrumen') ?>" class="btn btn-light">Validasi Instrumen</a>
    <a href="<?= base_url('admin/submissions?mode=validasi_produk') ?>" class="btn btn-light">Validasi Produk</a>
    <a href="<?= base_url('admin/submissions?mode=respon_mahasiswa') ?>" class="btn btn-light">Respon Mahasiswa</a>
    <a href="<?= base_url('admin/submissions?mode=observasi') ?>" class="btn btn-light">Observasi</a>
    <a href="<?= base_url('admin/submissions?mode=fgd') ?>" class="btn btn-light">FGD</a>
    <a href="<?= base_url('admin/submissions?mode=tes_kinerja') ?>" class="btn btn-light">Tes Kinerja</a>
</div>

<?php if (empty($responses)): ?>
    <div class="empty-state">
        Belum ada hasil pengisian.
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Responden</th>
                <th>Mode</th>
                <th>Instrumen</th>
                <th>Produk</th>
                <th>Kesimpulan</th>
                <th>Waktu Submit</th>
                <th style="width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responses as $index => $response): ?>
                <tr>
                    <td><?= ($offset ?? 0) + $index + 1 ?></td>
                    <td>
                        <strong><?= esc($response['nama']) ?></strong><br>
                        <small><?= esc($response['jenis_responden']) ?></small>

                        <?php if (!empty($response['nim'])): ?>
                            <br><small>NIM: <?= esc($response['nim']) ?></small>
                        <?php endif; ?>

                        <?php if (!empty($response['program_studi'])): ?>
                            <br><small>Prodi: <?= esc($response['program_studi']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge"><?= esc($response['mode']) ?></span></td>
                    <td>
                        <strong><?= esc($response['kode']) ?></strong><br>
                        <?= esc($response['judul']) ?><br>
                        <small><?= esc($response['judul_link']) ?></small>
                    </td>
                    <td><?= esc($response['nama_produk'] ?: '-') ?></td>
                    <td><?= esc($response['kesimpulan'] ?: '-') ?></td>
                    <td><?= esc($response['submitted_at'] ?: '-') ?></td>
                    <td>
                        <a href="<?= base_url('admin/submissions/' . $response['id']) ?>" class="btn btn-primary">
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
                            <button type="submit" class="btn btn-danger">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($pager)): ?>
        <div style="margin-top: 14px;">
            <?= $pager->links('submissions') ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>
