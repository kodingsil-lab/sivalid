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
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <form id="instrument-bundle-form" action="<?= esc($action) ?>" method="post">
        <?= csrf_field() ?>
        <?php if ($method === 'put'): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="card-header">
            <h3 class="card-title">Informasi Paket</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <label class="form-label" for="judul">Judul Paket Validasi Instrumen <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="judul"
                    id="judul"
                    class="form-control"
                    value="<?= old('judul', $bundle['judul'] ?? '') ?>"
                    placeholder="Contoh: Paket Validasi Instrumen Model Pembelajaran"
                    required
                >
            </div>

            <div class="form-row">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <?php $deskripsiValue = (string) old('deskripsi', $bundle['deskripsi'] ?? ''); ?>
                <input
                    type="hidden"
                    name="deskripsi"
                    id="deskripsi"
                    value="<?= esc($deskripsiValue, 'attr') ?>"
                >
                <div
                    id="deskripsi-editor"
                    class="bundle-quill-editor"
                    data-target-input="deskripsi"
                    data-initial="<?= esc($deskripsiValue, 'attr') ?>"
                    data-placeholder="Keterangan singkat tentang tujuan validasi instrumen ini"
                ></div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="sasaran">Validator</label>
                    <input
                        type="text"
                        name="sasaran"
                        id="sasaran"
                        class="form-control"
                        value="<?= old('sasaran', $bundle['sasaran'] ?? '') ?>"
                        placeholder="Contoh: Nama validator"
                    >
                </div>
                    <div class="form-row">
                        <label class="form-label" for="token">Token Link</label>
                        <?php
                        $tokenValue = old('token', $bundle['token'] ?? '');
                        $tokenPreviewBase = rtrim(base_url('paket'), '/');
                        ?>
                        <input
                            type="text"
                            name="token"
                            id="token"
                            class="form-control"
                            value="<?= esc($tokenValue) ?>"
                            placeholder="Contoh: validator1"
                            pattern="[A-Za-z0-9_-]{4,50}"
                            maxlength="50"
                        >
                        <small class="form-hint">Huruf/angka/_/- saja, 4-50 karakter. Kosongkan saat membuat paket untuk generate otomatis.</small>
                        <small class="form-hint">Link: <span id="token-preview"><?= esc($tokenPreviewBase . '/' . ($tokenValue !== '' ? $tokenValue : '{token}')) ?></span></small>
                    </div>

                <div class="form-row">
                    <label class="form-label" for="status">Status Paket <span class="text-danger">*</span></label>
                    <?php
                    $statusOptions = ['Aktif', 'Nonaktif', 'Ditutup'];
                    $selectedStatus = old('status', $bundle['status'] ?? 'Aktif');
                    ?>
                    <select name="status" id="status" class="form-control" required>
                        <?php foreach ($statusOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $selectedStatus === $opt ? 'selected' : '' ?>>
                                <?= $opt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                    <input
                        type="date"
                        name="tanggal_mulai"
                        id="tanggal_mulai"
                        class="form-control"
                        value="<?= old('tanggal_mulai', $bundle['tanggal_mulai'] ?? '') ?>"
                    >
                </div>

                <div class="form-row">
                    <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                    <input
                        type="date"
                        name="tanggal_selesai"
                        id="tanggal_selesai"
                        class="form-control"
                        value="<?= old('tanggal_selesai', $bundle['tanggal_selesai'] ?? '') ?>"
                    >
                </div>
            </div>

            <hr>
            <h4 class="mb-3">Keamanan Token</h4>

            <div class="form-row" style="max-width: 340px;">
                <label class="form-label" for="token_expires_at">Token Expired At</label>
                <?php
                $tokenExpiresAt = old('token_expires_at', $bundle['token_expires_at'] ?? '');
                if (!empty($tokenExpiresAt) && strlen($tokenExpiresAt) > 16) {
                    $tokenExpiresAt = date('Y-m-d\TH:i', strtotime($tokenExpiresAt));
                }
                ?>
                <input
                    type="datetime-local"
                    name="token_expires_at"
                    id="token_expires_at"
                    class="form-control"
                    value="<?= esc($tokenExpiresAt) ?>"
                >
            </div>

        </div>

        <div class="card-header border-top">
            <h3 class="card-title">Pilih Instrumen <span class="text-danger">*</span></h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Centang instrumen yang akan dimasukkan ke paket. Pengantar dan petunjuk validasi dapat diisi berbeda untuk setiap instrumen.</p>

            <?php if (empty($instruments)): ?>
                <div class="alert alert-warning mb-0">Belum ada instrumen yang tersedia. Tambahkan instrumen terlebih dahulu.</div>
            <?php else: ?>
                <?php
                $selectedInstrumentIds = old('instrument_ids', $selected ?? []);
                $selectedInstrumentIds = array_map('intval', (array) $selectedInstrumentIds);
                $selectedDetails = isset($selectedDetails) && is_array($selectedDetails) ? $selectedDetails : [];
                $oldPengantar = old('pengantar_validasi', []);
                $oldPetunjuk = old('petunjuk_validasi', []);
                $oldPengantar = is_array($oldPengantar) ? $oldPengantar : [];
                $oldPetunjuk = is_array($oldPetunjuk) ? $oldPetunjuk : [];
                ?>
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Pilih</th>
                                <th style="width: 140px;">Kode</th>
                                <th>Judul Instrumen</th>
                                <th style="width: 160px;">Jenis</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instruments as $instrument): ?>
                                <?php
                                $instrumentId = (int) $instrument['id'];
                                $checked = in_array($instrumentId, $selectedInstrumentIds, true);
                                $detail = $selectedDetails[$instrumentId] ?? [];
                                $pengantarValue = (string) ($oldPengantar[$instrumentId] ?? ($detail['pengantar_validasi'] ?? ''));
                                $petunjukValue = (string) ($oldPetunjuk[$instrumentId] ?? ($detail['petunjuk_validasi'] ?? ''));
                                $isConfigured = trim($pengantarValue) !== '' || trim($petunjukValue) !== '';
                                ?>
                                <tr>
                                    <td>
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="instrument_ids[]"
                                            value="<?= $instrumentId ?>"
                                            <?= $checked ? 'checked' : '' ?>
                                        >
                                    </td>
                                    <td><strong><?= esc((string) ($instrument['kode'] ?? '-')) ?></strong></td>
                                    <td><?= esc((string) ($instrument['judul'] ?? '-')) ?></td>
                                    <td><?= esc(title_case_label((string) ($instrument['jenis'] ?? '-'))) ?></td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions">
                                            <button
                                                type="button"
                                                class="btn btn-light btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalValidationText<?= $instrumentId ?>"
                                                title="Atur Pengantar/Petunjuk Validasi"
                                            >
                                                Atur
                                            </button>
                                            <?php if ($isConfigured): ?>
                                                <span class="badge bg-green text-green-fg">Sudah Diatur</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Simpan Paket</button>
            <a href="<?= base_url('admin/instrument-bundles') ?>" class="btn btn-light ms-2">Kembali</a>
        </div>
    </form>
</div>

<?php if (! empty($instruments)): ?>
    <?php foreach ($instruments as $instrument): ?>
        <?php
        $instrumentId = (int) $instrument['id'];
        $detail = $selectedDetails[$instrumentId] ?? [];
        $pengantarValue = (string) ($oldPengantar[$instrumentId] ?? ($detail['pengantar_validasi'] ?? ''));
        $petunjukValue = (string) ($oldPetunjuk[$instrumentId] ?? ($detail['petunjuk_validasi'] ?? ''));
        ?>
        <div class="modal instrument-validation-modal" id="modalValidationText<?= $instrumentId ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pengaturan Validasi Instrumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="fw-semibold"><?= esc((string) ($instrument['kode'] ?? '-')) ?> - <?= esc((string) ($instrument['judul'] ?? '-')) ?></div>
                            <div class="text-muted small">Atur teks khusus untuk validator pada instrumen ini.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pengantar_validasi_<?= $instrumentId ?>">Pengantar Validasi</label>
                            <input
                                type="hidden"
                                form="instrument-bundle-form"
                                name="pengantar_validasi[<?= $instrumentId ?>]"
                                id="pengantar_validasi_<?= $instrumentId ?>"
                                value="<?= esc($pengantarValue, 'attr') ?>"
                            >
                            <div
                                id="pengantar_validasi_editor_<?= $instrumentId ?>"
                                class="bundle-quill-editor"
                                data-target-input="pengantar_validasi_<?= $instrumentId ?>"
                                data-initial="<?= esc($pengantarValue, 'attr') ?>"
                                data-placeholder="Pengantar khusus untuk validator saat menilai instrumen ini."
                            ></div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="petunjuk_validasi_<?= $instrumentId ?>">Petunjuk Validasi</label>
                            <input
                                type="hidden"
                                form="instrument-bundle-form"
                                name="petunjuk_validasi[<?= $instrumentId ?>]"
                                id="petunjuk_validasi_<?= $instrumentId ?>"
                                value="<?= esc($petunjukValue, 'attr') ?>"
                            >
                            <div
                                id="petunjuk_validasi_editor_<?= $instrumentId ?>"
                                class="bundle-quill-editor"
                                data-target-input="petunjuk_validasi_<?= $instrumentId ?>"
                                data-initial="<?= esc($petunjukValue, 'attr') ?>"
                                data-placeholder="Petunjuk khusus penilaian untuk instrumen ini."
                            ></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
    .table-actions {
        display: flex;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .bundle-quill-editor {
        background: #fff;
        border-radius: 4px;
    }

    .bundle-quill-editor.ql-container,
    .bundle-quill-editor .ql-container {
        min-height: 150px;
        font-size: 0.875rem;
    }

    .bundle-quill-editor .ql-editor {
        min-height: 150px;
        white-space: pre-wrap;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('instrument-bundle-form');
        var editorMap = new Map();
        var tokenInput = document.getElementById('token');
        var tokenPreview = document.getElementById('token-preview');
        var tokenPreviewBase = <?= json_encode(rtrim(base_url('paket'), '/')) ?>;

        if (!form || typeof Quill === 'undefined') {
            return;
        }

        function updateTokenPreview() {
            if (!tokenInput || !tokenPreview) {
                return;
            }

            var rawValue = (tokenInput.value || '').trim().toLowerCase();
            var sanitized = rawValue.replace(/[^a-z0-9_-]/g, '');

            if (tokenInput.value !== sanitized) {
                tokenInput.value = sanitized;
            }

            tokenPreview.textContent = tokenPreviewBase + '/' + (sanitized !== '' ? sanitized : '{token}');
        }

        if (tokenInput) {
            tokenInput.addEventListener('input', updateTokenPreview);
            updateTokenPreview();
        }

        function syncEditor(item) {
            var text = item.quill.getText().replace(/\n$/, '').trim();
            item.input.value = text === '' ? '' : item.quill.root.innerHTML;
        }

        function initEditor(editorElement) {
            if (!editorElement || editorMap.has(editorElement)) {
                return editorMap.get(editorElement) || null;
            }

            var targetInputId = editorElement.getAttribute('data-target-input');
            var targetInput = document.getElementById(targetInputId);
            var initialContent = editorElement.getAttribute('data-initial') || '';

            if (!targetInput) {
                return null;
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

            var item = {
                quill: quill,
                input: targetInput
            };

            quill.on('text-change', function () {
                syncEditor(item);
            });

            editorMap.set(editorElement, item);
            syncEditor(item);

            return item;
        }

        document.querySelectorAll('.instrument-validation-modal').forEach(function (modalElement) {
            modalElement.addEventListener('shown.bs.modal', function () {
                modalElement.querySelectorAll('.bundle-quill-editor').forEach(initEditor);
            });
        });

        form.querySelectorAll('.bundle-quill-editor').forEach(initEditor);

        form.addEventListener('submit', function () {
            editorMap.forEach(syncEditor);
        });
    });
</script>

<?= $this->endSection() ?>
