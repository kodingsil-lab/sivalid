<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$pageTitle = isset($title) ? (string) $title : 'Form Instrumen';
$formAction = isset($action) ? (string) $action : base_url('admin/instruments');
$formMethod = isset($method) ? (string) $method : 'post';
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($pageTitle) ?></h2>
            <div class="text-muted mt-1">Lengkapi data instrumen secara terstruktur sebelum proses validasi.</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= esc($formAction) ?>" method="post">
    <?= csrf_field() ?>

    <?php if (strtolower($formMethod) === 'put'): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Identitas Instrumen</h3>
        </div>
        <div class="card-body">

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="kode">Kode Instrumen</label>
                    <input
                        type="text"
                        name="kode"
                        id="kode"
                        class="form-control"
                        value="<?= old('kode', $instrument['kode'] ?? '') ?>"
                        placeholder="Contoh: INS-001"
                        required
                    >
                </div>

                <div class="form-row">
                    <label class="form-label" for="jenis">Jenis Instrumen</label>
                    <select name="jenis" id="jenis" class="form-control" required>
                        <?php
                        $jenisOptions = isset($jenisOptions) && is_array($jenisOptions)
                            ? $jenisOptions
                            : [];
                        $selectedJenis = old('jenis', $instrument['jenis'] ?? '');

                        if ($selectedJenis !== '' && !in_array($selectedJenis, $jenisOptions, true)) {
                            array_unshift($jenisOptions, $selectedJenis);
                        }
                        ?>

                        <option value="">-- Pilih Jenis --</option>
                        <?php foreach ($jenisOptions as $jenis): ?>
                            <option value="<?= esc($jenis) ?>" <?= $selectedJenis === $jenis ? 'selected' : '' ?>>
                                <?= esc($jenis) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <label class="form-label" for="judul">Judul Instrumen</label>
                <input
                    type="text"
                    name="judul"
                    id="judul"
                    class="form-control"
                    value="<?= old('judul', $instrument['judul'] ?? '') ?>"
                    placeholder="Contoh: Form Penilaian Ahli terhadap Model Pembelajaran"
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
                        value="<?= old('sasaran', $instrument['sasaran'] ?? '') ?>"
                        placeholder="Contoh: Validator ahli, mahasiswa, observer"
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Deskripsi dan Petunjuk</h3>
        </div>
        <div class="card-body">

            <div class="form-row">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    class="form-control"
                    rows="4"
                    placeholder="Tuliskan deskripsi singkat instrumen."
                ><?= old('deskripsi', $instrument['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <label class="form-label" for="pengantar">Pengantar</label>
                <textarea
                    name="pengantar"
                    id="pengantar"
                    class="form-control"
                    rows="5"
                    placeholder="Tuliskan pengantar yang akan tampil pada instrumen."
                ><?= old('pengantar', $instrument['pengantar'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <label class="form-label" for="petunjuk">Petunjuk Pengisian</label>
                <textarea
                    name="petunjuk"
                    id="petunjuk"
                    class="form-control"
                    rows="5"
                    placeholder="Tuliskan petunjuk pengisian instrumen."
                ><?= old('petunjuk', $instrument['petunjuk'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Skala dan Status</h3>
        </div>
        <div class="card-body">

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <?php
                        $statusOptions = [
                            'Draft',
                            'Aktif',
                            'Dalam Validasi Instrumen',
                            'Perlu Revisi',
                            'Direvisi',
                            'Layak Ditetapkan Valid',
                            'Valid',
                            'Siap Disebar',
                            'Tidak Aktif',
                            'Arsip',
                        ];

                        $selectedStatus = old('status', $instrument['status'] ?? 'Draft');

                        if ($selectedStatus !== '' && !in_array($selectedStatus, $statusOptions, true)) {
                            array_unshift($statusOptions, $selectedStatus);
                        }
                        ?>

                        <?php foreach ($statusOptions as $status): ?>
                            <option value="<?= esc($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
                                <?= esc($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label class="form-label" for="skala_min">Skala Minimal</label>
                    <input
                        type="number"
                        name="skala_min"
                        id="skala_min"
                        class="form-control"
                        value="<?= old('skala_min', $instrument['skala_min'] ?? 1) ?>"
                        min="1"
                        required
                    >
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="skala_max">Skala Maksimal</label>
                    <input
                        type="number"
                        name="skala_max"
                        id="skala_max"
                        class="form-control"
                        value="<?= old('skala_max', $instrument['skala_max'] ?? 4) ?>"
                        min="2"
                        required
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-1">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
    </div>
</form>

<?= $this->endSection() ?>
