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
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    class="form-control rich-text-editor"
                    rows="7"
                    data-placeholder="Keterangan singkat tentang tujuan validasi instrumen ini"
                ><?= esc($deskripsiValue) ?></textarea>
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
            <p class="text-muted mb-3">Centang instrumen yang akan dimasukkan ke paket. Pengantar, petunjuk, status, dan skala validasi dapat diatur berbeda untuk setiap instrumen.</p>

            <?php if (empty($instruments)): ?>
                <div class="alert alert-warning mb-0">Belum ada instrumen yang tersedia. Tambahkan instrumen terlebih dahulu.</div>
            <?php else: ?>
                <?php
                $selectedInstrumentIds = old('instrument_ids', $selected ?? []);
                $selectedInstrumentIds = array_map('intval', (array) $selectedInstrumentIds);
                $selectedDetails = isset($selectedDetails) && is_array($selectedDetails) ? $selectedDetails : [];
                $oldPengantar = old('pengantar_validasi', []);
                $oldPetunjuk = old('petunjuk_validasi', []);
                $oldSkalaMin = old('skala_min_validasi', []);
                $oldSkalaMax = old('skala_max_validasi', []);
                $oldSkalaLabels = old('skala_labels_validasi', []);
                $oldStatusValidasi = old('status_validasi', []);
                $oldPengantar = is_array($oldPengantar) ? $oldPengantar : [];
                $oldPetunjuk = is_array($oldPetunjuk) ? $oldPetunjuk : [];
                $oldSkalaMin = is_array($oldSkalaMin) ? $oldSkalaMin : [];
                $oldSkalaMax = is_array($oldSkalaMax) ? $oldSkalaMax : [];
                $oldSkalaLabels = is_array($oldSkalaLabels) ? $oldSkalaLabels : [];
                $oldStatusValidasi = is_array($oldStatusValidasi) ? $oldStatusValidasi : [];
                $scaleTemplates = sivalid_scale_templates();
                $validationStatusOptions = ['Siap Divalidasi', 'Draft', 'Perlu Revisi', 'Direvisi', 'Tidak Aktif'];
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
                                $statusValue = (string) ($oldStatusValidasi[$instrumentId] ?? ($detail['status_validasi'] ?? 'Siap Divalidasi'));
                                $isConfigured = trim($pengantarValue) !== '' || trim($petunjukValue) !== '' || $statusValue !== 'Siap Divalidasi';
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
        $skalaMinValue = (int) ($oldSkalaMin[$instrumentId] ?? ($detail['skala_min'] ?? ($instrument['skala_min'] ?? 1)));
        $skalaMaxValue = (int) ($oldSkalaMax[$instrumentId] ?? ($detail['skala_max'] ?? ($instrument['skala_max'] ?? 4)));
        $skalaLabelsValue = (string) ($oldSkalaLabels[$instrumentId] ?? ($detail['skala_labels'] ?? ($instrument['skala_labels'] ?? '')));
        $statusValue = (string) ($oldStatusValidasi[$instrumentId] ?? ($detail['status_validasi'] ?? 'Siap Divalidasi'));
        $selectedScaleTemplate = 'relevance_4';
        foreach ($scaleTemplates as $templateKey => $template) {
            if ((int) $template['min'] === $skalaMinValue && (int) $template['max'] === $skalaMaxValue) {
                $selectedScaleTemplate = (string) $templateKey;
                break;
            }
        }
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
                            <div class="text-muted small">Atur teks, status, dan skala khusus untuk validator pada instrumen ini.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="status_validasi_<?= $instrumentId ?>">Status Validasi Instrumen</label>
                                <select
                                    class="form-control"
                                    form="instrument-bundle-form"
                                    name="status_validasi[<?= $instrumentId ?>]"
                                    id="status_validasi_<?= $instrumentId ?>"
                                >
                                    <?php foreach ($validationStatusOptions as $statusOption): ?>
                                        <option value="<?= esc($statusOption) ?>" <?= $statusValue === $statusOption ? 'selected' : '' ?>>
                                            <?= esc($statusOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="scale_template_validasi_<?= $instrumentId ?>">Jenis Skala Validasi</label>
                                <select
                                    class="form-control bundle-scale-template"
                                    id="scale_template_validasi_<?= $instrumentId ?>"
                                    data-target-min="skala_min_validasi_<?= $instrumentId ?>"
                                    data-target-max="skala_max_validasi_<?= $instrumentId ?>"
                                    data-target-labels="skala_labels_validasi_<?= $instrumentId ?>"
                                    data-target-preview="scale_preview_validasi_<?= $instrumentId ?>"
                                >
                                    <?php foreach ($scaleTemplates as $templateKey => $template): ?>
                                        <option
                                            value="<?= esc((string) $templateKey) ?>"
                                            data-min="<?= esc((string) $template['min']) ?>"
                                            data-max="<?= esc((string) $template['max']) ?>"
                                            data-labels="<?= esc(json_encode($template['labels'], JSON_UNESCAPED_UNICODE), 'attr') ?>"
                                            <?= $selectedScaleTemplate === $templateKey ? 'selected' : '' ?>
                                        >
                                            <?= esc((string) $template['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" form="instrument-bundle-form" name="skala_min_validasi[<?= $instrumentId ?>]" id="skala_min_validasi_<?= $instrumentId ?>" value="<?= esc((string) $skalaMinValue) ?>">
                                <input type="hidden" form="instrument-bundle-form" name="skala_max_validasi[<?= $instrumentId ?>]" id="skala_max_validasi_<?= $instrumentId ?>" value="<?= esc((string) $skalaMaxValue) ?>">
                                <input type="hidden" form="instrument-bundle-form" name="skala_labels_validasi[<?= $instrumentId ?>]" id="skala_labels_validasi_<?= $instrumentId ?>" value="<?= esc($skalaLabelsValue, 'attr') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori Pilihan Validasi</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 120px;">Nilai</th>
                                            <th>Kategori</th>
                                        </tr>
                                    </thead>
                                    <tbody id="scale_preview_validasi_<?= $instrumentId ?>"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pengantar_validasi_<?= $instrumentId ?>">Pengantar Validasi</label>
                            <textarea
                                form="instrument-bundle-form"
                                name="pengantar_validasi[<?= $instrumentId ?>]"
                                id="pengantar_validasi_<?= $instrumentId ?>"
                                class="form-control rich-text-editor"
                                rows="7"
                                data-placeholder="Pengantar khusus untuk validator saat menilai instrumen ini."
                            ><?= esc($pengantarValue) ?></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="petunjuk_validasi_<?= $instrumentId ?>">Petunjuk Validasi</label>
                            <textarea
                                form="instrument-bundle-form"
                                name="petunjuk_validasi[<?= $instrumentId ?>]"
                                id="petunjuk_validasi_<?= $instrumentId ?>"
                                class="form-control rich-text-editor"
                                rows="7"
                                data-placeholder="Petunjuk khusus penilaian untuk instrumen ini."
                            ><?= esc($petunjukValue) ?></textarea>
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

<style>
    .table-actions {
        display: flex;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .tox-tinymce {
        border-color: #cbd5e1;
        border-radius: 4px;
        overflow: hidden;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('instrument-bundle-form');
        var tokenInput = document.getElementById('token');
        var tokenPreview = document.getElementById('token-preview');
        var tokenPreviewBase = <?= json_encode(rtrim(base_url('paket'), '/')) ?>;

        if (!form) {
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

        function refreshScaleTemplate(selectElement) {
            if (!selectElement) {
                return;
            }

            var selected = selectElement.options[selectElement.selectedIndex];
            var minInput = document.getElementById(selectElement.getAttribute('data-target-min'));
            var maxInput = document.getElementById(selectElement.getAttribute('data-target-max'));
            var labelsInput = document.getElementById(selectElement.getAttribute('data-target-labels'));
            var previewBody = document.getElementById(selectElement.getAttribute('data-target-preview'));

            if (!selected || !minInput || !maxInput || !labelsInput || !previewBody) {
                return;
            }

            var labels = {};
            try {
                labels = JSON.parse(selected.getAttribute('data-labels') || '{}');
            } catch (error) {
                labels = {};
            }

            minInput.value = selected.getAttribute('data-min') || minInput.value;
            maxInput.value = selected.getAttribute('data-max') || maxInput.value;
            labelsInput.value = JSON.stringify(labels);
            previewBody.innerHTML = '';

            Object.keys(labels).forEach(function (score) {
                var tr = document.createElement('tr');
                var tdScore = document.createElement('td');
                var tdLabel = document.createElement('td');

                tdScore.textContent = score;
                tdLabel.textContent = labels[score];
                tr.appendChild(tdScore);
                tr.appendChild(tdLabel);
                previewBody.appendChild(tr);
            });
        }

        document.querySelectorAll('.bundle-scale-template').forEach(function (selectElement) {
            selectElement.addEventListener('change', function () {
                refreshScaleTemplate(selectElement);
            });
            refreshScaleTemplate(selectElement);
        });

        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.rich-text-editor',
                height: 260,
                menubar: false,
                branding: false,
                promotion: false,
                plugins: 'lists table',
                toolbar: 'blocks | bold italic underline strikethrough | numlist bullist | outdent indent | alignleft aligncenter alignright alignjustify | table | removeformat',
                block_formats: 'Normal=p;Judul 1=h1;Judul 2=h2;Judul 3=h3',
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                table_default_styles: {
                    width: '100%',
                    borderCollapse: 'collapse'
                },
                content_style: 'body{font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;font-size:15px;line-height:1.6;color:#1e293b} table{width:100%;border-collapse:collapse;margin:.65rem 0} th,td{border:1px solid #cbd5e1;padding:.45rem .55rem;vertical-align:top} th{background:#f1f5f9;font-weight:700}',
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

        form.addEventListener('submit', function () {
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }
        });
    });
</script>

<?= $this->endSection() ?>
