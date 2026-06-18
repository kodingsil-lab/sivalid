<?php
$identity = isset($respondentIdentity) && is_array($respondentIdentity) ? $respondentIdentity : [];
$link = isset($link) && is_array($link) ? $link : [];
$token = (string) ($link['token'] ?? '');
$identityFields = isset($identityFields) && is_array($identityFields) ? $identityFields : [];
?>

<?= csrf_field() ?>

<div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
    <label for="website">Website</label>
    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
</div>

<?php foreach ($identityFields as $field): ?>
    <?php $identityField = (string) ($field['key'] ?? ''); ?>
    <?php if ($identityField === '') {
        continue;
    } ?>
    <input type="hidden" name="<?= esc($identityField) ?>" value="<?= esc((string) ($identity[$identityField] ?? '')) ?>">
<?php endforeach; ?>

<style>
    .identity-summary-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #dde6ef;
        background: #fff;
        font-size: .92rem;
    }

    .identity-summary-table th,
    .identity-summary-table td {
        border: 1px solid #dde6ef;
        padding: .52rem .65rem;
        vertical-align: top;
        line-height: 1.42;
    }

    .identity-summary-table th {
        background: #f1f5f9;
        color: #1f2a3d;
        font-weight: 680;
        text-align: left;
    }

    .identity-summary-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: .75rem;
    }

    .identity-summary-edit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: .55rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #ffffff;
        color: #0b63b6;
        font-size: .95rem;
        font-weight: 650;
        line-height: 1.2;
        text-decoration: none;
    }

    .identity-summary-edit-btn:hover,
    .identity-summary-edit-btn:focus {
        border-color: #0b63b6;
        background: #f3f8fd;
        color: #0b63b6;
        text-decoration: none;
    }
</style>

<div class="public-table-wrap">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mb-3">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger mb-3">
            <strong>Periksa kembali input berikut:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <table class="identity-summary-table">
        <tbody>
        <?php foreach ($identityFields as $field): ?>
            <?php
            $key = (string) ($field['key'] ?? '');
            $label = (string) ($field['label'] ?? $key);
            $value = $key !== '' ? ($identity[$key] ?? '') : '';
            ?>
            <?php if ($key === '') {
                continue;
            } ?>
            <tr>
                <th style="width: 220px;"><?= esc($label) ?></th>
                <td><?= trim((string) $value) !== '' ? esc((string) $value) : '<span class="text-muted">-</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($token !== ''): ?>
    <div class="identity-summary-actions">
        <a href="<?= base_url('isi/' . $token . '?identitas=edit') ?>" class="identity-summary-edit-btn">Ubah Identitas</a>
    </div>
<?php endif; ?>
