<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title) ?></h2>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <?php if (empty($instruments)): ?>
        <div class="empty-state">
            Belum ada instrumen. Silakan buat data instrumen terlebih dahulu.
        </div>
    <?php elseif (empty($aspects)): ?>
        <div class="empty-state">
            Belum ada aspek untuk instrumen ini. Silakan buat aspek terlebih dahulu.
            <br><br>
            <a href="<?= base_url('admin/instrument-aspects/new' . (!empty($instrumentId) ? '?instrument_id=' . $instrumentId : '')) ?>" class="btn btn-primary">
                Tambah Aspek
            </a>
        </div>
    <?php else: ?>
        <form action="<?= esc($action) ?>" method="post">
            <?= csrf_field() ?>

            <?php if ($method === 'put'): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="form-row">
                <label class="form-label" for="instrument_id">Instrumen</label>
                <select name="instrument_id" id="instrument_id" class="form-control" required>
                    <option value="">-- Pilih Instrumen --</option>
                    <?php foreach ($instruments as $instrument): ?>
                        <?php
                        $selectedInstrument = old('instrument_id', $indicator['instrument_id'] ?? $instrumentId ?? '');
                        ?>
                        <option value="<?= $instrument['id'] ?>" <?= (int) $selectedInstrument === (int) $instrument['id'] ? 'selected' : '' ?>>
                            <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>
                    Untuk tahap sederhana, jika ingin memilih instrumen lain, kembali ke halaman kisi-kisi lalu pilih instrumen.
                </small>
            </div>

            <div class="form-row">
                <label class="form-label" for="aspect_id">Aspek</label>
                <select name="aspect_id" id="aspect_id" class="form-control" required>
                    <option value="">-- Pilih Aspek --</option>
                    <?php foreach ($aspects as $aspect): ?>
                        <?php
                        $selectedAspect = old('aspect_id', $indicator['aspect_id'] ?? '');
                        ?>
                        <option value="<?= $aspect['id'] ?>" <?= (int) $selectedAspect === (int) $aspect['id'] ? 'selected' : '' ?>>
                            <?= esc($aspect['urutan']) ?>. <?= esc($aspect['nama_aspek']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="indikator">Indikator</label>
                <textarea
                    name="indikator"
                    id="indikator"
                    class="form-control"
                    placeholder="Contoh: Kesesuaian isi instrumen dengan aspek yang diukur."
                    required
                ><?= old('indikator', $indicator['indikator'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <label class="form-label" for="urutan">Urutan</label>
                <input
                    type="number"
                    name="urutan"
                    id="urutan"
                    class="form-control"
                    value="<?= old('urutan', $indicator['urutan'] ?? 1) ?>"
                    min="1"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/instrument-aspects' . (!empty($instrumentId) ? '?instrument_id=' . $instrumentId : '')) ?>" class="btn btn-light">
                Kembali
            </a>
        </form>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>