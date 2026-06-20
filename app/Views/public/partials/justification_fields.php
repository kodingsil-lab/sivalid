<?php
$config = isset($justificationConfig) && is_array($justificationConfig) ? $justificationConfig : [];

if (($config['template'] ?? '') === 'none') {
    return;
}

$showComment = !array_key_exists('show_comment', $config) || !empty($config['show_comment']);
$showConclusion = !array_key_exists('show_conclusion', $config) || !empty($config['show_conclusion']);

if (!$showComment && !$showConclusion) {
    return;
}

$commentLabel = (string) ($config['comment_label'] ?? 'Komentar/Saran');
$commentPlaceholder = (string) ($config['comment_placeholder'] ?? 'Tuliskan komentar atau saran.');
$commentRequired = !empty($config['comment_required']);
$conclusionLabel = (string) ($config['conclusion_label'] ?? 'Kesimpulan');
$conclusionRequired = !empty($config['conclusion_required']);
$conclusionOptions = isset($config['conclusion_options']) && is_array($config['conclusion_options'])
    ? $config['conclusion_options']
    : ['Sangat Layak', 'Layak', 'Kurang Layak', 'Tidak Layak'];
$selectedConclusion = old('kesimpulan');
?>

<?php if ($showComment): ?>
<div class="public-card">
    <h2 class="public-heading"><?= esc($commentLabel) ?><?= $commentRequired ? ' <span class="public-required-note">*</span>' : '' ?></h2>
    <textarea
        class="public-textarea"
        name="komentar_umum"
        placeholder="<?= esc($commentPlaceholder) ?>"
        <?= $commentRequired ? 'required' : '' ?>
    ><?= esc(old('komentar_umum')) ?></textarea>
</div>
<?php endif; ?>

<?php if ($showConclusion): ?>
<div class="public-card">
    <h2 class="public-heading"><?= esc($conclusionLabel) ?><?= $conclusionRequired ? ' <span class="public-required-note">*</span>' : '' ?></h2>

    <div class="public-decision-list">
        <?php foreach ($conclusionOptions as $option): ?>
            <?php $option = trim((string) $option); ?>
            <?php if ($option === '') {
                continue;
            } ?>
            <label class="public-decision-item">
                <input
                    type="radio"
                    name="kesimpulan"
                    value="<?= esc($option) ?>"
                    <?= $selectedConclusion === $option ? 'checked' : '' ?>
                    <?= $conclusionRequired ? 'required' : '' ?>
                >
                <span><?= esc($option) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<style>
    .public-card {
        background: #fff;
        border: 1px solid #cfd9e4;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .public-heading {
        margin: 0 0 .7rem;
        font-size: 1.08rem;
        font-weight: 720;
        line-height: 1.35;
        color: #0f172a;
    }

    .public-textarea {
        width: 100%;
        min-height: 104px;
        border: 1px solid #cfd9e4;
        border-radius: 6px;
        padding: .55rem .7rem;
        resize: vertical;
        font: inherit;
        color: #0f172a;
        background: #fff;
    }

    .public-textarea:focus {
        outline: none;
        border-color: #0b63b6;
        box-shadow: 0 0 0 .25rem rgba(11, 99, 182, .16);
    }

    .public-decision-list {
        display: grid;
        gap: .45rem;
    }

    .public-decision-item {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        border: 1px solid #dde6ef;
        border-radius: 6px;
        padding: .65rem .75rem;
        cursor: pointer;
    }

    .public-decision-item:hover {
        background: #f3f8fd;
    }
</style>
