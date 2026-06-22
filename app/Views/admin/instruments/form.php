<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$pageTitle = isset($title) ? (string) $title : 'Form Instrumen';
$formAction = isset($action) ? (string) $action : base_url('admin/instruments');
$formMethod = isset($method) ? (string) $method : 'post';
$attachments = isset($attachments) && is_array($attachments) ? $attachments : [];
$isCreateForm = strtolower($formMethod) === 'post';
$codeValue = $isCreateForm
    ? (string) ($autoCode ?? old('kode', '01'))
    : (string) old('kode', $instrument['kode'] ?? '');
$scaleTemplates = sivalid_scale_templates();
$currentScaleLabels = sivalid_scale_labels(is_array($instrument ?? null) ? $instrument : []);
$selectedScaleTemplate = (string) old('scale_template', '');

if ($selectedScaleTemplate === '') {
    foreach ($scaleTemplates as $templateKey => $template) {
        if (
            (int) ($template['min'] ?? 0) === (int) ($instrument['skala_min'] ?? 1)
            && (int) ($template['max'] ?? 0) === (int) ($instrument['skala_max'] ?? 4)
            && ($template['labels'] ?? []) == $currentScaleLabels
        ) {
            $selectedScaleTemplate = (string) $templateKey;
            break;
        }
    }
}

if ($selectedScaleTemplate === '') {
    $selectedScaleTemplate = 'relevance_4';
}
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($pageTitle) ?></h2>
            <div class="text-muted mt-1">Lengkapi data instrumen secara terstruktur sebelum proses validasi.</div>
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

<form action="<?= esc($formAction) ?>" method="post" enctype="multipart/form-data">
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
                        value="<?= esc($codeValue) ?>"
                        placeholder="Contoh: INS-002"
                        required
                    >
                    <small class="text-muted">Kode boleh disesuaikan manual, misalnya INS-001, INS-002, atau kode lain yang unik.</small>
                </div>

                <div class="form-row">
                    <label class="form-label" for="jenis">Jenis/Bentuk Instrumen</label>
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

                        <option value="">-- Pilih Jenis/Bentuk --</option>
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

                <div class="form-row">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <input
                        type="text"
                        name="keterangan"
                        id="keterangan"
                        class="form-control"
                        value="<?= old('keterangan', $instrument['keterangan'] ?? '') ?>"
                        maxlength="255"
                        placeholder="Contoh: Tahap analisis, desain pengembangan, evaluasi"
                    >
                    <small class="text-muted">Catatan internal untuk menandai tahap penggunaan instrumen. Tidak tampil di halaman publik.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Pengantar dan Petunjuk</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <label class="form-label" for="pengantar">Pengantar</label>
                <textarea
                    name="pengantar"
                    id="pengantar"
                    class="form-control rich-text-editor"
                    rows="10"
                    data-placeholder="Tuliskan pengantar instrumen."
                ><?= esc((string) old('pengantar', $instrument['pengantar'] ?? '')) ?></textarea>
            </div>

            <div class="form-row">
                <label class="form-label" for="petunjuk">Petunjuk</label>
                <textarea
                    name="petunjuk"
                    id="petunjuk"
                    class="form-control rich-text-editor"
                    rows="10"
                    data-placeholder="Tuliskan petunjuk pengisian instrumen."
                ><?= esc((string) old('petunjuk', $instrument['petunjuk'] ?? '')) ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Lampiran Instrumen</h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                Opsional. Lampiran PDF akan tampil di halaman pengisian sebagai tombol pembuka popup PDF.
            </p>

            <?php if (!empty($attachments)): ?>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-vcenter">
                        <thead>
                            <tr>
                                <th>Judul Lampiran</th>
                                <th style="width: 140px;">File</th>
                                <th style="width: 110px;">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attachments as $attachment): ?>
                                <tr>
                                    <td><?= esc((string) ($attachment['title'] ?? '-')) ?></td>
                                    <td>
                                        <a href="<?= base_url((string) ($attachment['file_path'] ?? '')) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light">
                                            Lihat PDF
                                        </a>
                                    </td>
                                    <td>
                                        <label class="form-check mb-0">
                                            <input
                                                type="checkbox"
                                                name="delete_attachments[]"
                                                value="<?= esc((string) ($attachment['id'] ?? 0), 'attr') ?>"
                                                class="form-check-input"
                                            >
                                            <span class="form-check-label">Hapus</span>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div id="attachment-fields" class="attachment-fields">
                <div class="attachment-row">
                    <div class="form-row mb-0">
                        <label class="form-label">Judul Lampiran Baru</label>
                        <input
                            type="text"
                            name="attachment_titles[]"
                            class="form-control"
                            placeholder="Contoh: Buku Model Pembelajaran"
                        >
                    </div>
                    <div class="form-row mb-0">
                        <label class="form-label">File PDF</label>
                        <input
                            type="file"
                            name="attachment_files[]"
                            class="form-control"
                            accept="application/pdf,.pdf"
                        >
                    </div>
                    <button type="button" class="btn btn-light attachment-remove" aria-label="Hapus baris lampiran">Hapus</button>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary mt-2" id="add-attachment-row">Tambah Lampiran</button>
            <small class="text-muted d-block mt-2">Maksimal 10 MB per file PDF.</small>
        </div>
    </div>

    <input type="hidden" name="scale_template" value="<?= esc($selectedScaleTemplate, 'attr') ?>">
    <input type="hidden" name="skala_min" value="<?= esc((string) old('skala_min', $instrument['skala_min'] ?? 1), 'attr') ?>">
    <input type="hidden" name="skala_max" value="<?= esc((string) old('skala_max', $instrument['skala_max'] ?? 4), 'attr') ?>">
    <input type="hidden" name="status" value="<?= esc((string) old('status', $instrument['status'] ?? 'Draft'), 'attr') ?>">

    <div class="d-flex gap-2 mb-1">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
    </div>
</form>

<style>
    .tox-tinymce {
        border-color: #cbd5e1;
        border-radius: 4px;
        overflow: hidden;
    }

    .attachment-fields {
        display: grid;
        gap: .75rem;
    }

    .attachment-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(220px, .75fr) auto;
        align-items: end;
        gap: .75rem;
        padding: .75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #f8fafc;
    }

    @media (max-width: 767.98px) {
        .attachment-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('form[action]');
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.rich-text-editor',
                height: 300,
                menubar: false,
                branding: false,
                promotion: false,
                plugins: 'advlist lists table',
                toolbar: 'blocks | bold italic underline strikethrough | numlist bullist | outdent indent | alignleft aligncenter alignright alignjustify | table | removeformat',
                block_formats: 'Normal=p;Judul 1=h1;Judul 2=h2;Judul 3=h3',
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                table_default_styles: {
                    width: '100%',
                    borderCollapse: 'collapse'
                },
                advlist_number_styles: 'default,lower-alpha,lower-roman,upper-alpha,upper-roman',
                advlist_bullet_styles: 'default,circle,square',
                content_style: 'body{font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;font-size:15px;line-height:1.65;color:#1e293b} ol,ul{padding-left:1.65rem} ol ol{list-style-type:lower-alpha} ol ol ol{list-style-type:lower-roman} li{margin-bottom:.35rem;padding-left:.18rem} table{width:100%;table-layout:auto;border-collapse:collapse;margin:.65rem 0} th,td{min-width:120px;border:1px solid #cbd5e1;padding:.45rem .55rem;vertical-align:top;white-space:normal;overflow-wrap:anywhere} th:first-child,td:first-child{min-width:72px} th{background:#f1f5f9;font-weight:700}',
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
        }

        if (form) {
            form.addEventListener('submit', function () {
                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }
            });
        }

        var attachmentFields = document.getElementById('attachment-fields');
        var addAttachmentButton = document.getElementById('add-attachment-row');

        function updateAttachmentRemoveButtons() {
            if (!attachmentFields) return;

            var rows = attachmentFields.querySelectorAll('.attachment-row');
            rows.forEach(function (row) {
                var button = row.querySelector('.attachment-remove');
                if (button) {
                    button.disabled = rows.length <= 1;
                }
            });
        }

        if (attachmentFields && addAttachmentButton) {
            addAttachmentButton.addEventListener('click', function () {
                var firstRow = attachmentFields.querySelector('.attachment-row');
                if (!firstRow) return;

                var newRow = firstRow.cloneNode(true);
                newRow.querySelectorAll('input').forEach(function (input) {
                    input.value = '';
                });
                attachmentFields.appendChild(newRow);
                updateAttachmentRemoveButtons();
            });

            attachmentFields.addEventListener('click', function (event) {
                var button = event.target.closest('.attachment-remove');
                if (!button || button.disabled) return;

                var row = button.closest('.attachment-row');
                if (row) {
                    row.remove();
                    updateAttachmentRemoveButtons();
                }
            });

            updateAttachmentRemoveButtons();
        }
    });
</script>

<?= $this->endSection() ?>
