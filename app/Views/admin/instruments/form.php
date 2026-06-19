<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$pageTitle = isset($title) ? (string) $title : 'Form Instrumen';
$formAction = isset($action) ? (string) $action : base_url('admin/instruments');
$formMethod = isset($method) ? (string) $method : 'post';
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
            <h3 class="card-title">Pengantar dan Petunjuk Penyebaran</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <label class="form-label" for="pengantar">Pengantar Instrumen Siap Disebar</label>
                <textarea
                    name="pengantar"
                    id="pengantar"
                    class="quill-source"
                ><?= esc((string) old('pengantar', $instrument['pengantar'] ?? '')) ?></textarea>
                <div
                    id="pengantar-editor"
                    class="quill-editor"
                    data-placeholder="Tuliskan pengantar yang tampil saat instrumen disebarkan kepada responden."
                    data-target-input="pengantar"
                ></div>
            </div>

            <div class="form-row">
                <label class="form-label" for="petunjuk">Petunjuk Pengisian Responden</label>
                <textarea
                    name="petunjuk"
                    id="petunjuk"
                    class="quill-source"
                ><?= esc((string) old('petunjuk', $instrument['petunjuk'] ?? '')) ?></textarea>
                <div
                    id="petunjuk-editor"
                    class="quill-editor"
                    data-placeholder="Tuliskan petunjuk pengisian saat instrumen disebarkan kepada responden."
                    data-target-input="petunjuk"
                ></div>
            </div>
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

<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
    .quill-source {
        display: none;
    }

    .quill-editor {
        min-height: 180px;
        background: #fff;
        border-radius: 4px;
    }

    .quill-editor .ql-editor {
        min-height: 180px;
        font-size: 0.95rem;
        line-height: 1.65;
        white-space: pre-wrap;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('form[action]');
        var editors = [];
        var editorElements = document.querySelectorAll('.quill-editor');

        function syncQuillEditor(item) {
            var text = item.quill.getText().replace(/\n$/, '').trim();
            item.input.value = text === '' ? '' : item.quill.root.innerHTML;
        }

        if (typeof Quill !== 'undefined') {
            editorElements.forEach(function (editorElement) {
                var targetInputId = editorElement.getAttribute('data-target-input');
                var targetInput = document.getElementById(targetInputId);

                if (!targetInput) {
                    return;
                }

                var quill = new Quill(editorElement, {
                    theme: 'snow',
                    placeholder: editorElement.getAttribute('data-placeholder') || '',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ indent: '-1' }, { indent: '+1' }],
                            [{ align: [] }],
                            ['clean']
                        ]
                    }
                });

                var initialContent = targetInput.value || '';
                if (/<[a-z][\s\S]*>/i.test(initialContent)) {
                    quill.clipboard.dangerouslyPasteHTML(initialContent);
                } else {
                    quill.setText(initialContent);
                }

                editors.push({
                    quill: quill,
                    input: targetInput
                });

                var item = editors[editors.length - 1];
                syncQuillEditor(item);
                quill.on('text-change', function () {
                    syncQuillEditor(item);
                });
            });
        } else {
            document.querySelectorAll('.quill-source').forEach(function (textarea) {
                textarea.style.display = 'block';
                textarea.classList.add('form-control');
                textarea.setAttribute('rows', '7');
            });
        }

        if (form) {
            form.addEventListener('submit', function () {
                editors.forEach(syncQuillEditor);
            });
        }
    });
</script>

<?= $this->endSection() ?>
