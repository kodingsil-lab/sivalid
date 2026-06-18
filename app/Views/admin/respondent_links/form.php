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

        <?= view('admin/partials/justification_builder', [
            'justificationTemplates' => $justificationTemplates ?? [],
            'justificationConfig' => $justificationConfig ?? [],
        ]) ?>

        <div class="form-row">
            <label class="form-label" for="pengantar_penyebaran">Pengantar Penyebaran</label>
            <textarea
                name="pengantar_penyebaran"
                id="pengantar_penyebaran"
                class="form-control rich-text-editor"
                data-placeholder="Tuliskan pengantar yang akan dibaca responden sebelum mengisi instrumen."
            ><?= esc((string) old('pengantar_penyebaran', $link['pengantar_penyebaran'] ?? '')) ?></textarea>
            <small class="text-muted">Gunakan bagian ini untuk menjelaskan tujuan, sasaran, dan arahan awal pengisian.</small>
        </div>

        <div class="form-row">
            <label class="form-label" for="petunjuk_penyebaran">Petunjuk Pengisian Angket</label>
            <textarea
                name="petunjuk_penyebaran"
                id="petunjuk_penyebaran"
                class="form-control rich-text-editor"
                data-placeholder="Tuliskan petunjuk khusus untuk responden/pengisi angket."
            ><?= esc((string) old('petunjuk_penyebaran', $link['petunjuk_penyebaran'] ?? '')) ?></textarea>
            <small class="text-muted">Petunjuk ini khusus untuk angket/instrumen yang diisi responden, berbeda dari petunjuk validasi instrumen.</small>
        </div>

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

<style>
    .respondent-link-form .tox-tinymce {
        border-color: #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof tinymce === 'undefined') {
            return;
        }

        tinymce.init({
            selector: '.rich-text-editor',
            height: 260,
            menubar: false,
            branding: false,
            promotion: false,
            plugins: 'lists table',
            toolbar: 'blocks | bold italic underline | numlist bullist | table | removeformat',
            block_formats: 'Paragraf=p;Judul 2=h2;Judul 3=h3',
            table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
            table_default_styles: {
                width: '100%',
                borderCollapse: 'collapse'
            },
            content_style: 'body{font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;font-size:15px;line-height:1.55;color:#1e293b} table{width:100%;border-collapse:collapse;margin:.65rem 0} th,td{border:1px solid #cbd5e1;padding:.45rem .55rem;vertical-align:top} th{background:#f1f5f9;font-weight:700}',
            setup: function (editor) {
                editor.on('init', function () {
                    var textarea = document.getElementById(editor.id);
                    var placeholder = textarea ? textarea.getAttribute('data-placeholder') : '';
                    if (placeholder) {
                        editor.getBody().setAttribute('data-placeholder', placeholder);
                    }
                });
            }
        });

        var form = document.querySelector('form[action]');
        if (form) {
            form.addEventListener('submit', function () {
                tinymce.triggerSave();
            });
        }
    });
</script>

<?= $this->endSection() ?>
