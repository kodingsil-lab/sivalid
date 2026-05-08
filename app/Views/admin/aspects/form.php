<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title) ?></h2>
        </div>
    </div>
</div>

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

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Form Aspek Instrumen</h3>
    </div>
    <div class="card-body">
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
                    $selectedInstrument = old('instrument_id', $aspect['instrument_id'] ?? $instrumentId ?? '');
                    ?>
                    <option value="<?= $instrument['id'] ?>" <?= (int) $selectedInstrument === (int) $instrument['id'] ? 'selected' : '' ?>>
                        <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label class="form-label" for="nama_aspek">Nama Aspek</label>
                <input
                    type="text"
                    name="nama_aspek"
                    id="nama_aspek"
                    class="form-control"
                    value="<?= old('nama_aspek', $aspect['nama_aspek'] ?? '') ?>"
                    placeholder="Contoh: Kelayakan Isi Materi"
                    required
                >
            </div>

            <div class="form-row">
                <label class="form-label" for="urutan">Urutan</label>
                <input
                    type="number"
                    name="urutan"
                    id="urutan"
                    class="form-control"
                    value="<?= old('urutan', $aspect['urutan'] ?? 1) ?>"
                    min="1"
                    required
                >
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="deskripsi">Deskripsi Aspek</label>
            <textarea
                name="deskripsi"
                id="deskripsi"
                class="form-control"
                placeholder="Tuliskan deskripsi aspek jika diperlukan."
            ><?= old('deskripsi', $aspect['deskripsi'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/instrument-aspects' . (!empty($instrumentId) ? '?instrument_id=' . $instrumentId : '')) ?>" class="btn btn-light">
            Kembali
        </a>
    </form>
    </div>
</div>

<?= $this->endSection() ?>