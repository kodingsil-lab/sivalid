<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Butir Pernyataan Instrumen</h1>

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
    <form action="<?= base_url('admin/instrument-items') ?>" method="get" class="search-form">
        <select name="instrument_id" class="form-control" style="min-width: 420px;">
            <option value="">-- Semua Instrumen --</option>
            <?php foreach ($instruments as $instrument): ?>
                <option value="<?= $instrument['id'] ?>" <?= (int) ($instrumentId ?? 0) === (int) $instrument['id'] ? 'selected' : '' ?>>
                    <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>

        <?php if (!empty($instrumentId)): ?>
            <a href="<?= base_url('admin/instrument-items/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                + Tambah Butir
            </a>

            <a href="<?= base_url('admin/instrument-aspects?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                Lihat Kisi-Kisi
            </a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($instrumentId)): ?>
    <div class="empty-state">
        Silakan pilih instrumen terlebih dahulu untuk menampilkan butir pernyataan.
    </div>
<?php elseif (empty($items)): ?>
    <div class="empty-state">
        Belum ada butir pernyataan pada instrumen ini.
        <br><br>
        <a href="<?= base_url('admin/instrument-items/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
            Tambah Butir Pertama
        </a>
    </div>
<?php else: ?>
    <div class="card">
        <h3>Daftar Butir Pernyataan</h3>

        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th style="width: 180px;">Aspek</th>
                    <th style="width: 240px;">Indikator</th>
                    <th>Butir Pernyataan</th>
                    <th style="width: 110px;">Tipe</th>
                    <th style="width: 90px;">Status</th>
                    <th style="width: 190px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?= esc($item['nomor']) ?>
                        </td>
                        <td>
                            <?= esc($item['nama_aspek']) ?>
                        </td>
                        <td>
                            <?= !empty($item['indikator']) ? nl2br(esc($item['indikator'])) : '<em>-</em>' ?>
                        </td>
                        <td>
                            <?= nl2br(esc($item['pernyataan'])) ?>
                            <br>
                            <small>
                                Urutan: <?= esc($item['urutan']) ?> |
                                <?= (int) $item['wajib'] === 1 ? 'Wajib' : 'Tidak wajib' ?>
                            </small>
                        </td>
                        <td>
                            <?= esc($item['tipe_butir']) ?>
                        </td>
                        <td>
                            <span class="badge"><?= esc($item['status']) ?></span>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/instrument-items/' . $item['id'] . '/edit') ?>" class="btn btn-warning">
                                Edit
                            </a>

                            <form
                                action="<?= base_url('admin/instrument-items/' . $item['id']) ?>"
                                method="post"
                                class="action-inline"
                                onsubmit="return confirm('Yakin ingin menghapus butir ini?')"
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
    </div>
<?php endif; ?>

<?= $this->endSection() ?>