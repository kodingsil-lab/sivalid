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
    <form action="<?= base_url('admin/submissions') ?>" method="get" class="search-form">
        <select name="mode" class="form-control" style="min-width: 280px;">
            <option value="">-- Semua Mode --</option>
            <?php foreach ($allowedModes as $modeOption): ?>
                <option value="<?= esc($modeOption) ?>" <?= ($mode ?? '') === $modeOption ? 'selected' : '' ?>>
                    <?= esc(str_replace('_', ' ', strtoupper($modeOption))) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>

        <a href="<?= base_url('admin/submissions') ?>" class="btn btn-light">
            Reset
        </a>
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
                    <td><?= $index + 1 ?></td>
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
<?php endif; ?>

<?= $this->endSection() ?>
