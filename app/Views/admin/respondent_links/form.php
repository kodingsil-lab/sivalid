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

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Form Link Penyebaran Instrumen</h3>
    </div>
    <div class="card-body">
    <form action="<?= esc($action) ?>" method="post" class="respondent-link-form">
        <?= csrf_field() ?>

        <?php if ($method === 'put'): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="form-row">
            <label class="form-label" for="instrument_id">Instrumen Valid</label>
            <select name="instrument_id" id="instrument_id" class="form-control" required>
                <option value="">-- Pilih Instrumen Valid --</option>

                <?php foreach ($instruments as $instrument): ?>
                    <?php
                    $selectedInstrument = old('instrument_id', $link['instrument_id'] ?? '');
                    ?>
                    <option value="<?= $instrument['id'] ?>" <?= (int) $selectedInstrument === (int) $instrument['id'] ? 'selected' : '' ?>>
                        <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Instrumen yang dapat disebarkan harus sudah ada di daftar Instrumen Valid.</small>
        </div>

        <div class="form-row">
            <label class="form-label" for="judul_link">Judul Link</label>
            <input
                type="text"
                name="judul_link"
                id="judul_link"
                class="form-control"
                value="<?= old('judul_link', $link['judul_link'] ?? '') ?>"
                placeholder="Contoh: Pengisian Instrumen Evaluasi Pembelajaran"
                required
            >
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label class="form-label" for="sasaran">Sasaran</label>
                <input
                    type="text"
                    name="sasaran"
                    id="sasaran"
                    class="form-control"
                    value="<?= old('sasaran', $link['sasaran'] ?? '') ?>"
                    placeholder="Contoh: Mahasiswa, guru, validator, observer, peserta pelatihan"
                >
            </div>

            <div class="form-row">
                <label class="form-label" for="status">Status Link</label>
                <?php
                $statusOptions = ['Aktif', 'Nonaktif', 'Ditutup'];
                $selectedStatus = old('status', $link['status'] ?? 'Aktif');
                ?>

                <select name="status" id="status" class="form-control" required>
                    <?php foreach ($statusOptions as $status): ?>
                        <option value="<?= esc($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
                            <?= esc($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?= view('admin/partials/identity_fields_builder', [
            'identityTemplates' => $identityTemplates ?? [],
            'identityFields' => $identityFields ?? [],
            'link' => $link ?? [],
        ]) ?>

        <div class="form-grid">
            <div class="form-row">
                <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    name="tanggal_mulai"
                    id="tanggal_mulai"
                    class="form-control"
                    value="<?= old('tanggal_mulai', $link['tanggal_mulai'] ?? '') ?>"
                >
            </div>

            <div class="form-row">
                <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                <input
                    type="date"
                    name="tanggal_selesai"
                    id="tanggal_selesai"
                    class="form-control"
                    value="<?= old('tanggal_selesai', $link['tanggal_selesai'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="maksimal_respon">Maksimal Respon</label>
            <input
                type="number"
                name="maksimal_respon"
                id="maksimal_respon"
                class="form-control"
                value="<?= old('maksimal_respon', $link['maksimal_respon'] ?? '') ?>"
                min="1"
                placeholder="Kosongkan jika tidak dibatasi"
            >
        </div>

        <?php if (!empty($link['token'])): ?>
            <div class="form-row">
                <label>Token Link</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?= esc($link['token']) ?>"
                    readonly
                >
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/respondent-links') ?>" class="btn btn-light">
            Kembali
        </a>
    </form>
    </div>
</div>

<?= $this->endSection() ?>
