<?php
$templates = isset($identityTemplates) && is_array($identityTemplates) ? $identityTemplates : [];
$fields = isset($identityFields) && is_array($identityFields) ? $identityFields : [];
$link = isset($link) && is_array($link) ? $link : [];
$selectedTemplate = old('identity_template', $link['identity_template'] ?? '');

if ($selectedTemplate === '' || !isset($templates[$selectedTemplate])) {
    $selectedTemplate = isset($templates['mahasiswa']) ? 'mahasiswa' : array_key_first($templates);
}

$oldKeys = old('identity_field_key');
$oldLabels = old('identity_field_label');
$oldTypes = old('identity_field_type');
$oldRequired = old('identity_field_required');

if (is_array($oldKeys) && is_array($oldLabels)) {
    $fields = [];

    foreach ($oldKeys as $index => $key) {
        $fields[] = [
            'key' => $key,
            'label' => $oldLabels[$index] ?? '',
            'type' => is_array($oldTypes) ? ($oldTypes[$index] ?? 'text') : 'text',
            'required' => is_array($oldRequired) && isset($oldRequired[$index]),
        ];
    }
}

if (empty($fields) && isset($templates[$selectedTemplate]['fields'])) {
    $fields = $templates[$selectedTemplate]['fields'];
}

$fieldTypes = [
    'text' => 'Teks',
    'email' => 'Email',
    'number' => 'Angka',
    'date' => 'Tanggal',
    'tel' => 'Telepon',
    'textarea' => 'Paragraf',
];
?>

<div class="identity-builder" data-templates="<?= esc(json_encode($templates, JSON_UNESCAPED_UNICODE), 'attr') ?>">
    <div class="form-row">
        <label class="form-label" for="identity_template">Template Identitas Responden</label>
        <select name="identity_template" id="identity_template" class="form-control identity-template-select">
            <?php foreach ($templates as $key => $template): ?>
                <option value="<?= esc((string) $key) ?>" <?= $selectedTemplate === $key ? 'selected' : '' ?>>
                    <?= esc((string) ($template['label'] ?? $key)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">
            Template ini menentukan field identitas yang muncul di halaman publik. Field dapat disesuaikan untuk setiap link.
        </small>
    </div>

    <div class="identity-fields-panel">
        <div class="identity-fields-header">
            <div>
                <label class="form-label mb-1">Field Identitas</label>
                <div class="text-muted small">Nama wajib ada. Gunakan key huruf kecil tanpa spasi, misalnya <code>jabatan</code>.</div>
            </div>
            <button type="button" class="btn btn-light btn-sm identity-add-field">Tambah Field</button>
        </div>

        <div class="identity-fields-list">
            <?php foreach ($fields as $index => $field): ?>
                <div class="identity-field-row">
                    <input type="text" name="identity_field_label[]" class="form-control" value="<?= esc((string) ($field['label'] ?? '')) ?>" placeholder="Label field">
                    <input type="text" name="identity_field_key[]" class="form-control" value="<?= esc((string) ($field['key'] ?? '')) ?>" placeholder="key_field">
                    <select name="identity_field_type[]" class="form-control">
                        <?php foreach ($fieldTypes as $type => $label): ?>
                            <option value="<?= esc($type) ?>" <?= (string) ($field['type'] ?? 'text') === $type ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label class="identity-required-check">
                        <input type="checkbox" name="identity_field_required[<?= esc((string) $index) ?>]" value="1" <?= !empty($field['required']) ? 'checked' : '' ?>>
                        Wajib
                    </label>
                    <button type="button" class="btn btn-light btn-sm identity-remove-field">Hapus</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<template id="identity-field-row-template">
    <div class="identity-field-row">
        <input type="text" name="identity_field_label[]" class="form-control" value="" placeholder="Label field">
        <input type="text" name="identity_field_key[]" class="form-control" value="" placeholder="key_field">
        <select name="identity_field_type[]" class="form-control">
            <?php foreach ($fieldTypes as $type => $label): ?>
                <option value="<?= esc($type) ?>"><?= esc($label) ?></option>
            <?php endforeach; ?>
        </select>
        <label class="identity-required-check">
            <input type="checkbox" name="identity_field_required[]" value="1">
            Wajib
        </label>
        <button type="button" class="btn btn-light btn-sm identity-remove-field">Hapus</button>
    </div>
</template>

<style>
    .identity-fields-panel {
        border: 1px solid #dbe3ec;
        border-radius: 6px;
        padding: .85rem;
        margin-bottom: 1rem;
        background: #f8fafc;
    }

    .identity-fields-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: .75rem;
    }

    .identity-fields-list {
        display: flex;
        flex-direction: column;
        gap: .55rem;
    }

    .identity-field-row {
        display: grid;
        grid-template-columns: minmax(160px, 1.2fr) minmax(130px, .9fr) 120px 88px 76px;
        gap: .5rem;
        align-items: center;
    }

    .identity-required-check {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        margin: 0;
        font-size: .9rem;
        color: #334155;
        white-space: nowrap;
    }

    @media (max-width: 900px) {
        .identity-field-row {
            grid-template-columns: 1fr;
        }

        .identity-fields-header {
            flex-direction: column;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.identity-builder').forEach(function (builder) {
            var select = builder.querySelector('.identity-template-select');
            var list = builder.querySelector('.identity-fields-list');
            var addButton = builder.querySelector('.identity-add-field');
            var template = document.getElementById('identity-field-row-template');
            var templates = {};

            try {
                templates = JSON.parse(builder.getAttribute('data-templates') || '{}');
            } catch (error) {
                templates = {};
            }

            function slug(value) {
                return String(value || '')
                    .toLowerCase()
                    .replace(/[^a-z0-9_]+/g, '_')
                    .replace(/^_+|_+$/g, '')
                    .slice(0, 60);
            }

            function renumberRequiredInputs() {
                list.querySelectorAll('.identity-field-row').forEach(function (row, index) {
                    var checkbox = row.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.name = 'identity_field_required[' + index + ']';
                    }
                });
            }

            function createRow(field) {
                var fragment = template.content.cloneNode(true);
                var row = fragment.querySelector('.identity-field-row');
                var labelInput = row.querySelector('input[name="identity_field_label[]"]');
                var keyInput = row.querySelector('input[name="identity_field_key[]"]');
                var typeSelect = row.querySelector('select[name="identity_field_type[]"]');
                var requiredInput = row.querySelector('input[type="checkbox"]');

                labelInput.value = field && field.label ? field.label : '';
                keyInput.value = field && field.key ? field.key : '';
                typeSelect.value = field && field.type ? field.type : 'text';
                requiredInput.checked = !!(field && field.required);

                labelInput.addEventListener('blur', function () {
                    if (keyInput.value.trim() === '') {
                        keyInput.value = slug(labelInput.value);
                    }
                });

                row.querySelector('.identity-remove-field').addEventListener('click', function () {
                    row.remove();
                    if (!list.querySelector('.identity-field-row')) {
                        list.appendChild(createRow({key: 'nama', label: 'Nama Lengkap', type: 'text', required: true}));
                    }
                    renumberRequiredInputs();
                });

                return row;
            }

            list.querySelectorAll('.identity-field-row').forEach(function (row) {
                var labelInput = row.querySelector('input[name="identity_field_label[]"]');
                var keyInput = row.querySelector('input[name="identity_field_key[]"]');

                if (labelInput && keyInput) {
                    labelInput.addEventListener('blur', function () {
                        if (keyInput.value.trim() === '') {
                            keyInput.value = slug(labelInput.value);
                        }
                    });
                }

                var removeButton = row.querySelector('.identity-remove-field');
                if (removeButton) {
                    removeButton.addEventListener('click', function () {
                        row.remove();
                        if (!list.querySelector('.identity-field-row')) {
                            list.appendChild(createRow({key: 'nama', label: 'Nama Lengkap', type: 'text', required: true}));
                        }
                        renumberRequiredInputs();
                    });
                }
            });

            if (select) {
                select.addEventListener('change', function () {
                    var selected = templates[select.value];
                    var fields = selected && Array.isArray(selected.fields) ? selected.fields : [];

                    list.innerHTML = '';
                    fields.forEach(function (field) {
                        list.appendChild(createRow(field));
                    });

                    if (!fields.length) {
                        list.appendChild(createRow({key: 'nama', label: 'Nama Lengkap', type: 'text', required: true}));
                    }

                    renumberRequiredInputs();
                });
            }

            if (addButton) {
                addButton.addEventListener('click', function () {
                    list.appendChild(createRow({key: '', label: '', type: 'text', required: false}));
                    renumberRequiredInputs();
                });
            }

            renumberRequiredInputs();
        });
    });
</script>
