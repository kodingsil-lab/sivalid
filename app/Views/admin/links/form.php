<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h1 class="page-title"><?= esc($title) ?></h1>

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

<?php
$isProductValidation = isset($link['mode']) && $link['mode'] === 'validasi_produk';
?>

<div class="card">
    <form action="<?= esc($action) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="mode" value="<?= $isProductValidation ? 'validasi_produk' : 'validasi_instrumen' ?>">

        <?php if ($method === 'put'): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <?php if ($isProductValidation): ?>
            <div class="form-row">
                <label for="product_id">Produk yang Divalidasi</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>

                    <?php foreach ($products ?? [] as $product): ?>
                        <?php
                        $selectedProduct = old('product_id', $link['product_id'] ?? '');
                        ?>
                        <option value="<?= $product['id'] ?>" <?= (int) $selectedProduct === (int) $product['id'] ? 'selected' : '' ?>>
                            <?= esc($product['kode']) ?> - <?= esc($product['nama_produk']) ?> (<?= esc($product['jenis_produk']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>
                    Produk inilah yang akan dilihat oleh validator produk pada halaman publik.
                </small>
            </div>
        <?php endif; ?>

        <div class="form-row">
            <label for="instrument_id">
                <?= $isProductValidation ? 'Instrumen Validasi Produk' : 'Instrumen yang Divalidasi' ?>
            </label>
            <select name="instrument_id" id="instrument_id" class="form-control" required>
                <option value="">-- Pilih Instrumen --</option>

                <?php foreach ($instruments as $instrument): ?>
                    <?php
                    $selectedInstrument = old('instrument_id', $link['instrument_id'] ?? '');
                    ?>
                    <option value="<?= $instrument['id'] ?>" <?= (int) $selectedInstrument === (int) $instrument['id'] ? 'selected' : '' ?>>
                        <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>
                <?php if ($isProductValidation): ?>
                    Validator produk akan melihat produk yang divalidasi dan instrumen penilaian produk. Instrumen harus berstatus Valid dan sudah dihubungkan dengan produk.
                <?php else: ?>
                    Validator instrumen akan melihat kisi-kisi, instrumen, dan lembar validasi instrumen.
                <?php endif; ?>
            </small>
        </div>

        <div class="form-row">
            <label for="judul_link">Judul Link</label>
            <input
                type="text"
                name="judul_link"
                id="judul_link"
                class="form-control"
                value="<?= old('judul_link', $link['judul_link'] ?? '') ?>"
                placeholder="Contoh: Validasi Instrumen Form Penilaian Ahli terhadap Model Pembelajaran"
                required
            >
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label for="sasaran">Sasaran</label>
                <input
                    type="text"
                    name="sasaran"
                    id="sasaran"
                    class="form-control"
                    value="<?= old('sasaran', $link['sasaran'] ?? ($isProductValidation ? 'Validator Produk' : 'Validator Instrumen')) ?>"
                    placeholder="<?= $isProductValidation ? 'Contoh: Validator Produk' : 'Contoh: Validator Instrumen' ?>"
                >
            </div>

            <div class="form-row">
                <label for="status">Status Link</label>
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

        <div class="form-grid">
            <div class="form-row">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    name="tanggal_mulai"
                    id="tanggal_mulai"
                    class="form-control"
                    value="<?= old('tanggal_mulai', $link['tanggal_mulai'] ?? '') ?>"
                >
            </div>

            <div class="form-row">
                <label for="tanggal_selesai">Tanggal Selesai</label>
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
            <label for="maksimal_respon">Maksimal Respon</label>
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
                <small>Token tidak diubah saat edit.</small>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= $isProductValidation ? base_url('admin/validasi-produk') : base_url('admin/instrument-links') ?>" class="btn btn-light">Kembali</a>
    </form>
</div>

<?= $this->endSection() ?>
