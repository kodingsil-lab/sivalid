<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title">Revisi Butir Instrumen</h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-error">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Informasi Butir</h3>

    <table>
        <tr>
            <th style="width: 220px;">Instrumen</th>
            <td>
                <strong><?= esc($item['kode']) ?></strong><br>
                <?= esc($item['judul']) ?>
            </td>
        </tr>
        <tr>
            <th>Aspek</th>
            <td><?= esc($item['nama_aspek']) ?></td>
        </tr>
        <tr>
            <th>Indikator</th>
            <td><?= !empty($item['indikator']) ? nl2br(esc($item['indikator'])) : '-' ?></td>
        </tr>
        <tr>
            <th>Nomor Butir</th>
            <td><?= esc($item['nomor']) ?></td>
        </tr>
        <tr>
            <th>Status Butir</th>
            <td><span class="badge"><?= esc($item['status']) ?></span></td>
        </tr>

        <?php if (!empty($analysisItem)): ?>
            <tr>
                <th>Rata-Rata Skor</th>
                <td><?= esc($analysisItem['rata_rata']) ?></td>
            </tr>
            <tr>
                <th>Kategori Butir</th>
                <td><?= esc($analysisItem['kategori']) ?></td>
            </tr>
            <tr>
                <th>Rekomendasi</th>
                <td><strong><?= esc($analysisItem['rekomendasi']) ?></strong></td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card">
    <h3>Komentar Validator terhadap Butir Ini</h3>

    <?php if (empty($comments)): ?>
        <div class="empty-state">
            Belum ada komentar validator untuk butir ini.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Validator</th>
                    <th>Bidang/Instansi</th>
                    <th>Komentar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $index => $comment): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($comment['nama']) ?></td>
                        <td>
                            <?= esc($comment['bidang_keahlian'] ?: '-') ?><br>
                            <?= esc($comment['instansi'] ?: '-') ?>
                        </td>
                        <td><?= nl2br(esc($comment['komentar'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Form Revisi Butir</h3>

    <form action="<?= esc($action) ?>" method="post">
        <?= csrf_field() ?>

        <input type="hidden" name="instrument_item_id" value="<?= esc($item['id']) ?>">
        <input type="hidden" name="analysis_result_id" value="<?= esc($analysisResultId ?? '') ?>">

        <div class="form-row">
            <label>Butir Lama</label>
            <textarea class="form-control" readonly style="min-height: 120px;"><?= esc($item['pernyataan']) ?></textarea>
        </div>

        <div class="form-row">
            <label for="pernyataan_baru">Butir Hasil Revisi</label>
            <textarea
                name="pernyataan_baru"
                id="pernyataan_baru"
                class="form-control"
                style="min-height: 140px;"
                required
            ><?= old('pernyataan_baru', $item['pernyataan']) ?></textarea>
        </div>

        <div class="form-row">
            <label for="alasan_revisi">Alasan / Dasar Revisi</label>
            <textarea
                name="alasan_revisi"
                id="alasan_revisi"
                class="form-control"
                placeholder="Contoh: Redaksi diperjelas berdasarkan komentar validator dan hasil rata-rata skor butir."
            ><?= old('alasan_revisi') ?></textarea>
        </div>

        <div class="form-row">
            <label for="sumber_revisi">Sumber Revisi</label>
            <?php
            $selectedSumber = old('sumber_revisi', 'Komentar validator dan hasil analisis');
            $sumberOptions = [
                'Komentar validator dan hasil analisis',
                'Komentar validator',
                'Hasil analisis per butir',
                'Keputusan peneliti',
                'Revisi redaksi',
                'Revisi aspek/indikator',
            ];
            ?>

            <select name="sumber_revisi" id="sumber_revisi" class="form-control" required>
                <?php foreach ($sumberOptions as $option): ?>
                    <option value="<?= esc($option) ?>" <?= $selectedSumber === $option ? 'selected' : '' ?>>
                        <?= esc($option) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Revisi</button>
        <a href="<?= base_url('admin/instrument-revisions?instrument_id=' . $item['instrument_id']) ?>" class="btn btn-light">
            Kembali
        </a>
    </form>
</div>

<div class="card">
    <h3>Riwayat Revisi Sebelumnya</h3>

    <?php if (empty($revisions)): ?>
        <div class="empty-state">
            Belum ada riwayat revisi sebelumnya untuk butir ini.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Pernyataan Lama</th>
                    <th>Pernyataan Baru</th>
                    <th>Alasan</th>
                    <th style="width: 130px;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($revisions as $index => $revision): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= nl2br(esc($revision['pernyataan_lama'])) ?></td>
                        <td><?= nl2br(esc($revision['pernyataan_baru'])) ?></td>
                        <td><?= nl2br(esc($revision['alasan_revisi'] ?: '-')) ?></td>
                        <td><?= esc($revision['tanggal_revisi'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>