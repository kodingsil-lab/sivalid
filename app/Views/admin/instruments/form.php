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
            <h3 class="card-title">Pengantar dan Petunjuk</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <label class="form-label" for="pengantar">Pengantar</label>
                <input
                    type="hidden"
                    name="pengantar"
                    id="pengantar"
                    value="<?= esc((string) old('pengantar', $instrument['pengantar'] ?? ''), 'attr') ?>"
                >
                <div
                    id="pengantar-editor"
                    class="quill-editor"
                    data-placeholder="Tuliskan pengantar yang akan tampil pada instrumen."
                    data-target-input="pengantar"
                    data-initial="<?= esc((string) old('pengantar', $instrument['pengantar'] ?? ''), 'attr') ?>"
                ></div>
            </div>

            <div class="form-row">
                <label class="form-label" for="petunjuk">Petunjuk Pengisian</label>
                <input
                    type="hidden"
                    name="petunjuk"
                    id="petunjuk"
                    value="<?= esc((string) old('petunjuk', $instrument['petunjuk'] ?? ''), 'attr') ?>"
                >
                <div
                    id="petunjuk-editor"
                    class="quill-editor"
                    data-placeholder="Tuliskan petunjuk pengisian instrumen."
                    data-target-input="petunjuk"
                    data-initial="<?= esc((string) old('petunjuk', $instrument['petunjuk'] ?? ''), 'attr') ?>"
                ></div>
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
                    <?php
                    $manualStatuses = ['Draft', 'Aktif', 'Perlu Revisi', 'Direvisi', 'Tidak Aktif'];
                    $selectedStatus = old('status', $instrument['status'] ?? 'Draft');
                    $selectedStatusLabel = status_display_label($selectedStatus);
                    $isManualValid = (bool) ($isManualValid ?? false);
                    $isWorkflowStatus = $isManualValid || !in_array($selectedStatus, $manualStatuses, true);
                    ?>

                    <?php if ($isCreateForm): ?>
                        <input type="hidden" name="status" value="Draft">
                        <div class="form-control" style="background-color: #f8f9fa;">Draft (otomatis saat instrumen dibuat)</div>
                        <small class="text-muted">Status berikutnya akan berubah otomatis saat link validasi dibuat, analisis selesai, revisi, dan penetapan valid.</small>
                    <?php elseif ($isManualValid): ?>
                        <input type="hidden" name="status" value="<?= esc($selectedStatus, 'attr') ?>">
                        <div class="form-control" style="background-color: #f8f9fa;"><?= esc($selectedStatusLabel) ?> (dikunci)</div>
                        <small class="text-muted">Instrumen masih masuk daftar Instrumen Valid. Hapus dari daftar itu terlebih dahulu jika ingin mengubah status master.</small>
                    <?php elseif ($isWorkflowStatus): ?>
                        <input type="hidden" name="status" value="<?= esc($selectedStatus, 'attr') ?>">
                        <div class="form-control" style="background-color: #f8f9fa;"><?= esc($selectedStatusLabel) ?> (otomatis dari alur validasi)</div>
                        <small class="text-muted">Status ini dikontrol dari alur validasi instrumen.</small>
                    <?php else: ?>
                        <select name="status" id="status" class="form-control" required>
                            <?php foreach ($manualStatuses as $status): ?>
                                <option value="<?= esc($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
                                    <?= esc($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
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

<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
    .quill-editor {
        background: #fff;
        border-radius: 4px;
    }

    .quill-editor.ql-container,
    .quill-editor .ql-container {
        min-height: 150px;
        font-size: 0.875rem;
    }

    .quill-editor .ql-editor {
        min-height: 150px;
        white-space: pre-wrap;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editorElements = document.querySelectorAll('.quill-editor');

        if (editorElements.length === 0) {
            return;
        }

        var editors = [];

        editorElements.forEach(function (editorElement) {
            var targetInputId = editorElement.getAttribute('data-target-input');
            var initialContent = editorElement.getAttribute('data-initial') || '';
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

            if (/<[a-z][\s\S]*>/i.test(initialContent)) {
                quill.clipboard.dangerouslyPasteHTML(initialContent);
            } else {
                quill.setText(initialContent);
            }

            editors.push({
                quill: quill,
                input: targetInput
            });
        });

        var form = document.querySelector('form[action]');

        if (!form) {
            return;
        }

        form.addEventListener('submit', function () {
            editors.forEach(function (item) {
                var text = item.quill.getText().replace(/\n$/, '').trim();
                item.input.value = text === '' ? '' : item.quill.root.innerHTML;
            });
        });
    });
</script>

<?= $this->endSection() ?>
