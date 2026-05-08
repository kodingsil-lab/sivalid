<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Kisi-Kisi Instrumen</h2>
            <div class="text-muted mt-1">Kelola aspek, indikator, dan struktur kisi-kisi untuk instrumen terpilih.</div>
        </div>
        <?php if (!empty($instrumentId)): ?>
            <div class="col-auto ms-auto">
                <a href="<?= base_url('admin/instrument-aspects/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                    + Tambah Aspek
                </a>

                <a href="<?= base_url('admin/instrument-indicators/new?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                    + Tambah Indikator
                </a>

                <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                    Kelola Butir
                </a>
            </div>
        <?php endif; ?>
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

<div class="card mb-3">
    <div class="card-body">
    <form action="<?= base_url('admin/instrument-aspects') ?>" method="get" class="search-form search-form-wide">
        <select name="instrument_id" class="form-control" style="min-width: 420px;">
            <option value="">-- Pilih Instrumen --</option>
            <?php foreach ($instruments as $instrument): ?>
                <option value="<?= $instrument['id'] ?>" <?= (int) ($instrumentId ?? 0) === (int) $instrument['id'] ? 'selected' : '' ?>>
                    <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>
    </div>
</div>

<?php if (empty($instrumentId)): ?>
    <div class="empty-state">
        Silakan pilih instrumen terlebih dahulu untuk menampilkan kisi-kisi.
    </div>
<?php else: ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Daftar Aspek Instrumen</h3>
        </div>
        <div class="card-body p-0">

        <?php if (empty($aspects)): ?>
            <div class="empty-state">
                Belum ada aspek pada instrumen ini.
            </div>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">Urutan</th>
                        <th>Aspek</th>
                        <th>Deskripsi</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspects as $aspect): ?>
                        <tr>
                            <td><?= esc($aspect['urutan']) ?></td>
                            <td><?= esc($aspect['nama_aspek']) ?></td>
                            <td><?= nl2br(esc($aspect['deskripsi'] ?: '-')) ?></td>
                            <td class="table-actions-cell">
                                <div class="table-actions">
                                    <a href="<?= base_url('admin/instrument-aspects/' . $aspect['id'] . '/edit') ?>" class="btn btn-warning">
                                        Edit
                                    </a>

                                    <form
                                        action="<?= base_url('admin/instrument-aspects/' . $aspect['id']) ?>"
                                        method="post"
                                        class="action-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus aspek ini? Semua indikator di bawah aspek ini juga akan terhapus.')"
                                    >
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Tampilan Kisi-Kisi Instrumen</h3>
        </div>
        <div class="card-body p-0">

        <?php if (empty($aspects)): ?>
            <div class="empty-state">
                Kisi-kisi belum dapat ditampilkan karena aspek belum dibuat.
            </div>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-vcenter table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 240px;">Aspek</th>
                        <th>Indikator</th>
                        <th class="table-actions-cell">Aksi Indikator</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspects as $aspectIndex => $aspect): ?>
                        <?php
                        $aspectIndicators = array_values(array_filter($indicators, function ($indicator) use ($aspect) {
                            return (int) $indicator['aspect_id'] === (int) $aspect['id'];
                        }));
                        ?>

                        <?php if (empty($aspectIndicators)): ?>
                            <tr>
                                <td><?= $aspectIndex + 1 ?></td>
                                <td><?= esc($aspect['nama_aspek']) ?></td>
                                <td><em>Belum ada indikator.</em></td>
                                <td class="table-actions-cell">
                                    <div class="table-actions">
                                        <a href="<?= base_url('admin/instrument-indicators/new?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                                            Tambah
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($aspectIndicators as $indicatorIndex => $indicator): ?>
                                <tr>
                                    <?php if ($indicatorIndex === 0): ?>
                                        <td rowspan="<?= count($aspectIndicators) ?>"><?= $aspectIndex + 1 ?></td>
                                        <td rowspan="<?= count($aspectIndicators) ?>"><?= esc($aspect['nama_aspek']) ?></td>
                                    <?php endif; ?>

                                    <td>
                                        <?= esc($indicator['urutan']) ?>.
                                        <?= nl2br(esc($indicator['indikator'])) ?>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions">
                                            <a href="<?= base_url('admin/instrument-indicators/' . $indicator['id'] . '/edit') ?>" class="btn btn-warning">
                                                Edit
                                            </a>

                                            <form
                                                action="<?= base_url('admin/instrument-indicators/' . $indicator['id']) ?>"
                                                method="post"
                                                class="action-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus indikator ini?')"
                                            >
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
