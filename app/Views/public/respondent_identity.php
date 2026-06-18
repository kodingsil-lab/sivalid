<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Identitas Responden') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-bg: #edf2f5;
            --pub-surface: #ffffff;
            --pub-border: #cfd9e4;
            --pub-text: #0f172a;
            --pub-muted: #53657a;
            --pub-blue: #0b63b6;
            --pub-radius: 8px;
        }

        body {
            margin: 0;
            background: var(--pub-bg);
            color: var(--pub-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.65;
        }

        .public-shell {
            width: min(946px, calc(100% - 32px));
            margin: 32px auto 52px;
        }

        .public-card {
            background: var(--pub-surface);
            border: 1px solid var(--pub-border);
            border-radius: var(--pub-radius);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
            padding: 1.55rem 1.6rem 1.35rem;
            margin-bottom: 1rem;
        }

        .public-header {
            border-bottom: 1px solid #d5e0ec;
            margin-bottom: 1.15rem;
            padding-bottom: .95rem;
        }

        .public-title {
            margin: 0 0 .25rem;
            font-size: 1.46rem;
            font-weight: 720;
            line-height: 1.25;
        }

        .public-meta,
        .identity-intro {
            color: var(--pub-muted);
            font-size: .95rem;
        }

        .identity-intro {
            margin-bottom: 1rem;
            color: var(--pub-text);
        }

        .intro-content {
            color: var(--pub-text);
            font-size: .99rem;
            text-align: justify;
            text-justify: inter-word;
        }

        .intro-content .rich-text-content {
            padding: 0;
            line-height: 1.58;
        }

        .intro-content .rich-text-content p,
        .intro-content .rich-text-content ol,
        .intro-content .rich-text-content ul {
            margin-bottom: .55rem;
        }

        .intro-content .rich-text-content > :last-child {
            margin-bottom: 0;
        }

        .intro-content table {
            width: 100%;
            border-collapse: collapse;
            margin: .7rem 0;
            background: #fff;
        }

        .intro-content th,
        .intro-content td {
            border: 1px solid #dde6ef;
            padding: .52rem .65rem;
            vertical-align: top;
        }

        .intro-content th {
            background: #f1f5f9;
            font-weight: 680;
            text-align: left;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
            margin-bottom: .75rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            min-width: 0;
        }

        .form-group label {
            font-size: .88rem;
            font-weight: 600;
        }

        .req {
            color: #e53e3e;
            margin-left: 2px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border: 1px solid var(--pub-border);
            border-radius: 6px;
            padding: .5rem .75rem;
            font-size: .93rem;
            color: var(--pub-text);
            background: #fff;
            outline: none;
        }

        .form-group textarea {
            min-height: 92px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--pub-blue);
            box-shadow: 0 0 0 .25rem rgba(11, 99, 182, .16);
        }

        .pub-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--pub-blue);
            background: #0b6fc8;
            color: #fff;
            min-height: 44px;
            padding: .65rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 650;
            text-decoration: none;
            line-height: 1.2;
        }

        .pub-btn:hover {
            background: #095fae;
            border-color: #095fae;
            color: #fff;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: .9rem;
        }

        @media (max-width: 720px) {
            .public-shell {
                width: min(100% - 20px, 946px);
                margin: 18px auto 34px;
            }

            .public-card {
                padding: 1.1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .public-title {
                font-size: 1.24rem;
            }

            .pub-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php
$link = isset($link) && is_array($link) ? $link : [];
$identity = isset($respondentIdentity) && is_array($respondentIdentity) ? $respondentIdentity : [];
$identityFields = isset($identityFields) && is_array($identityFields) ? $identityFields : [];
$pengantarPenyebaran = trim((string) ($link['pengantar_penyebaran'] ?? ''));
$pengantarMaster = trim((string) ($link['pengantar'] ?? ''));
$pengantar = $pengantarPenyebaran !== '' ? $pengantarPenyebaran : $pengantarMaster;
$instrumentTitle = trim((string) ($link['judul'] ?? ''));

if ($instrumentTitle === '') {
    $instrumentTitle = trim((string) ($link['judul_link'] ?? 'Pengisian Instrumen'));
}
?>

<div class="public-shell">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
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

    <div class="public-card">
        <div class="public-header" style="<?= $pengantar === '' ? 'border-bottom:0;margin-bottom:0;padding-bottom:0;' : '' ?>">
            <h1 class="public-title"><?= esc($instrumentTitle) ?></h1>
            <div class="public-meta">
                <?= esc($link['judul_link'] ?: 'Pengisian Instrumen') ?>
                <?php if (!empty($link['tanggal_selesai'])): ?>
                    / Batas Pengisian: <?= esc(format_tanggal_indonesia($link['tanggal_selesai'])) ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($pengantar !== ''): ?>
            <div class="intro-content">
                <?= render_rich_text_content($pengantar) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="public-card">
        <div class="public-header">
            <h2 class="public-title"><?= esc($title ?? 'Identitas Responden') ?></h2>
            <div class="public-meta">Lengkapi identitas sebelum melanjutkan pengisian instrumen.</div>
        </div>

        <p class="identity-intro">
            Silakan lengkapi identitas terlebih dahulu. Setelah identitas tersimpan, Bapak/Ibu dapat melanjutkan pengisian instrumen.
        </p>

        <form action="<?= base_url('isi/' . $link['token']) ?>" method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="start_identity">

            <div style="display:none;" aria-hidden="true">
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
            </div>

            <?php foreach (array_chunk($identityFields, 2) as $fieldRow): ?>
                <div class="form-row">
                    <?php foreach ($fieldRow as $field): ?>
                        <?php
                        $key = (string) ($field['key'] ?? '');
                        $label = (string) ($field['label'] ?? $key);
                        $type = (string) ($field['type'] ?? 'text');
                        $required = !empty($field['required']);
                        $value = old($key, $identity[$key] ?? '');
                        ?>
                        <?php if ($key === '') {
                            continue;
                        } ?>
                        <div class="form-group">
                            <label for="<?= esc($key) ?>">
                                <?= esc($label) ?><?= $required ? ' <span class="req">*</span>' : '' ?>
                            </label>
                            <?php if ($type === 'textarea'): ?>
                                <textarea
                                    id="<?= esc($key) ?>"
                                    name="<?= esc($key) ?>"
                                    class="form-control"
                                    <?= $required ? 'required' : '' ?>
                                    maxlength="1000"
                                    rows="3"
                                ><?= esc((string) $value) ?></textarea>
                            <?php else: ?>
                                <input
                                    type="<?= esc(in_array($type, ['email', 'number', 'date', 'tel'], true) ? $type : 'text') ?>"
                                    id="<?= esc($key) ?>"
                                    name="<?= esc($key) ?>"
                                    value="<?= esc((string) $value) ?>"
                                    <?= $required ? 'required' : '' ?>
                                    maxlength="<?= in_array($key, ['nim', 'semester', 'kelas'], true) ? '50' : '150' ?>"
                                >
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <div class="form-actions">
                <button type="submit" class="pub-btn">Lanjut Mengisi</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
