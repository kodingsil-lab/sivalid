<?php
$templates = isset($justificationTemplates) && is_array($justificationTemplates) ? $justificationTemplates : [];
$config = isset($justificationConfig) && is_array($justificationConfig) ? $justificationConfig : [];
$selectedTemplate = old('justification_template', $config['template'] ?? array_key_first($templates));

if ($selectedTemplate === '' || !isset($templates[$selectedTemplate])) {
    $selectedTemplate = array_key_first($templates);
}

$isNoneTemplate = $selectedTemplate === 'none';
$commentLabel = old('justification_comment_label', $config['comment_label'] ?? 'Komentar/Saran');
$commentPlaceholder = old('justification_comment_placeholder', $config['comment_placeholder'] ?? '');
$commentRequired = old('justification_comment_required', !empty($config['comment_required']) ? '1' : '');
$conclusionLabel = old('justification_conclusion_label', $config['conclusion_label'] ?? 'Kesimpulan');
$conclusionRequired = old('justification_conclusion_required', !empty($config['conclusion_required']) ? '1' : '1');
$conclusionOptions = old('justification_conclusion_options', implode("\n", $config['conclusion_options'] ?? []));

if ($isNoneTemplate && old('justification_template') === null) {
    $commentLabel = '';
    $commentPlaceholder = '';
    $commentRequired = '';
    $conclusionLabel = '';
    $conclusionRequired = '';
    $conclusionOptions = '';
}
?>

<div class="justification-builder" data-templates="<?= esc(json_encode($templates, JSON_UNESCAPED_UNICODE), 'attr') ?>">
    <div class="form-row">
        <label class="form-label" for="justification_template">Template Justifikasi/Kesimpulan</label>
        <select name="justification_template" id="justification_template" class="form-control justification-template-select">
            <?php foreach ($templates as $key => $template): ?>
                <option value="<?= esc((string) $key) ?>" <?= $selectedTemplate === $key ? 'selected' : '' ?>>
                    <?= esc((string) ($template['label'] ?? $key)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Bagian ini mengatur komentar/saran dan kesimpulan akhir yang muncul di halaman publik.</small>
    </div>

    <div class="justification-panel <?= $isNoneTemplate ? 'd-none' : '' ?>">
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label" for="justification_comment_label">Label Komentar/Saran</label>
                <input type="text" name="justification_comment_label" id="justification_comment_label" class="form-control" value="<?= esc((string) $commentLabel) ?>">
            </div>
            <div class="form-row">
                <label class="form-label" for="justification_conclusion_label">Label Kesimpulan</label>
                <input type="text" name="justification_conclusion_label" id="justification_conclusion_label" class="form-control" value="<?= esc((string) $conclusionLabel) ?>">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="justification_comment_placeholder">Placeholder Komentar/Saran</label>
            <input type="text" name="justification_comment_placeholder" id="justification_comment_placeholder" class="form-control" value="<?= esc((string) $commentPlaceholder) ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="justification_conclusion_options">Pilihan Kesimpulan</label>
            <textarea name="justification_conclusion_options" id="justification_conclusion_options" class="form-control" rows="5" placeholder="Satu pilihan per baris"><?= esc((string) $conclusionOptions) ?></textarea>
            <small class="text-muted">Tulis satu opsi per baris, misalnya Sangat Layak, Layak, Kurang Layak, Tidak Layak.</small>
        </div>

        <div class="d-flex flex-wrap gap-3 mb-2">
            <label class="form-check">
                <input type="checkbox" name="justification_comment_required" value="1" class="form-check-input" <?= $commentRequired ? 'checked' : '' ?>>
                <span class="form-check-label">Komentar/Saran wajib diisi</span>
            </label>
            <label class="form-check">
                <input type="checkbox" name="justification_conclusion_required" value="1" class="form-check-input" <?= $conclusionRequired ? 'checked' : '' ?>>
                <span class="form-check-label">Kesimpulan wajib dipilih</span>
            </label>
        </div>
    </div>
</div>

<style>
    .justification-panel {
        border: 1px solid #dbe3ec;
        border-radius: 6px;
        padding: .85rem;
        margin-bottom: 1rem;
        background: #f8fafc;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.justification-builder').forEach(function (builder) {
            var select = builder.querySelector('.justification-template-select');
            var templates = {};

            try {
                templates = JSON.parse(builder.getAttribute('data-templates') || '{}');
            } catch (error) {
                templates = {};
            }

            if (!select) {
                return;
            }

            select.addEventListener('change', function () {
                var selected = templates[select.value] || {};
                var commentLabel = builder.querySelector('#justification_comment_label');
                var commentPlaceholder = builder.querySelector('#justification_comment_placeholder');
                var conclusionLabel = builder.querySelector('#justification_conclusion_label');
                var conclusionOptions = builder.querySelector('#justification_conclusion_options');
                var commentRequired = builder.querySelector('input[name="justification_comment_required"]');
                var conclusionRequired = builder.querySelector('input[name="justification_conclusion_required"]');
                var panel = builder.querySelector('.justification-panel');
                var isNone = select.value === 'none';

                if (commentLabel) commentLabel.value = selected.comment_label || '';
                if (commentPlaceholder) commentPlaceholder.value = selected.comment_placeholder || '';
                if (conclusionLabel) conclusionLabel.value = selected.conclusion_label || '';
                if (conclusionOptions) conclusionOptions.value = Array.isArray(selected.conclusion_options) ? selected.conclusion_options.join("\n") : '';
                if (commentRequired) commentRequired.checked = isNone ? false : !!selected.comment_required;
                if (conclusionRequired) conclusionRequired.checked = isNone ? false : selected.conclusion_required !== false;
                if (panel) panel.classList.toggle('d-none', isNone);
            });
        });
    });
</script>
