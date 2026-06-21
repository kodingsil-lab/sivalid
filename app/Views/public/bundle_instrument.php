<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Paket Validasi') ?> - Instrumen <?= esc(sprintf('%03d', (int) ($position ?? 1))) ?> dari <?= esc(sprintf('%03d', (int) ($total ?? 1))) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-bg: #f8fafc;
            --pub-surface: #ffffff;
            --pub-border: #e2e8f0;
            --pub-text: #1e293b;
            --pub-muted: #64748b;
            --pub-blue: #1d4ed8;
            --pub-blue-soft: #eff6ff;
            --pub-radius: 8px;
        }

        body {
            margin: 0;
            background-color: var(--pub-bg);
            background-image: linear-gradient(180deg, #eef2ff 0%, var(--pub-bg) 360px);
            background-repeat: no-repeat;
            color: var(--pub-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.6;
        }

        #toolbarContainer,
        #debug-icon,
        #debug-bar {
            display: none !important;
        }

        .public-shell {
            width: min(1080px, calc(100% - 24px));
            margin: 20px auto 36px;
        }

        .public-card {
            background: var(--pub-surface);
            border: 1px solid var(--pub-border);
            border-radius: var(--pub-radius);
            box-shadow: 0 1px 6px rgba(15, 23, 42, 0.06);
            padding: 1rem 1.1rem;
            margin-bottom: 0.9rem;
        }

        .bundle-nav {
            display: flex;
            align-items: flex-start;
            gap: .9rem;
            padding: .95rem 1.05rem;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: var(--pub-radius);
            margin-bottom: .9rem;
            font-size: .95rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }

        .bundle-nav a {
            color: var(--pub-blue);
            text-decoration: none;
            font-weight: 650;
        }

        .bundle-nav a:hover { text-decoration: underline; }

        .bundle-nav-main {
            min-width: 0;
            flex: 1 1 auto;
        }

        .bundle-nav-top {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            margin-bottom: .35rem;
        }

        .bundle-index {
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
        }

        .bundle-title {
            color: var(--pub-muted);
            font-size: .96rem;
            line-height: 1.45;
        }

        .bundle-progress {
            display: flex;
            gap: 4px;
            flex: 0 0 auto;
            padding-top: .35rem;
        }

        .bundle-step {
            width: 24px;
            height: 8px;
            border-radius: 999px;
            background: #bfdbfe;
        }

        .bundle-step.active { background: var(--pub-blue); }
        .bundle-step.done { background: #22c55e; }
        .bundle-step.proses { background: #f59e0b; }

        .public-title { margin: 0 0 .25rem; font-size: 1.72rem; font-weight: 700; }
        .public-muted { color: var(--pub-muted); font-size: 1rem; }
        .public-heading { margin: 0 0 .75rem; font-size: 1.18rem; font-weight: 700; color: var(--pub-text); }

        .public-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .6rem;
            flex-wrap: wrap;
            margin-bottom: .35rem;
        }

        .public-section-head .public-heading {
            margin-bottom: 0;
        }

        .public-progress-widget {
            margin-left: auto;
            width: min(240px, 100%);
            padding-top: .1rem;
        }

        .public-progress-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .3rem;
            color: #206bc4;
            font-size: .86rem;
            font-weight: 700;
        }

        .public-progress-count {
            color: var(--pub-muted);
            font-weight: 650;
        }

        .public-rich-text {
            max-width: 1040px;
            padding: 0;
            color: var(--pub-text);
            font-family: inherit;
            font-size: 1rem;
            line-height: 1.6;
            white-space: normal;
            word-break: break-word;
        }

        .public-rich-text .ql-align-center {
            text-align: center;
        }

        .public-rich-text .ql-align-right {
            text-align: right;
        }

        .public-rich-text .ql-align-justify {
            text-align: justify;
            text-justify: inter-word;
        }

        .public-rich-text p,
        .public-rich-text ol,
        .public-rich-text ul,
        .public-rich-text blockquote {
            margin-bottom: .75rem;
        }

        .public-rich-text p:last-child,
        .public-rich-text ol:last-child,
        .public-rich-text ul:last-child,
        .public-rich-text blockquote:last-child {
            margin-bottom: 0;
        }

        .public-rich-text ol,
        .public-rich-text ul {
            padding-left: 1.75rem;
        }

        .public-rich-text ol {
            counter-reset: public-list-0 public-list-1 public-list-2;
        }

        .public-rich-text ol > li {
            list-style: none;
            position: relative;
            counter-increment: public-list-0;
            padding-left: 1.65rem;
        }

        .public-rich-text ol > li::before {
            content: counter(public-list-0, decimal) ".";
            position: absolute;
            left: 0;
            color: var(--pub-text);
        }

        .public-rich-text ol > li.ql-indent-1 {
            counter-increment: public-list-1;
            margin-left: 1.75rem;
        }

        .public-rich-text ol > li.ql-indent-1::before {
            content: counter(public-list-1, lower-alpha) ".";
        }

        .public-rich-text ol > li.ql-indent-2 {
            counter-increment: public-list-2;
            margin-left: 3.5rem;
        }

        .public-rich-text ol > li.ql-indent-2::before {
            content: counter(public-list-2, lower-roman) ".";
        }

        .public-rich-text ul > li.ql-indent-1 {
            margin-left: 1.75rem;
        }

        .public-rich-text ul > li.ql-indent-2 {
            margin-left: 3.5rem;
        }

        .public-rich-text li {
            padding-left: .35rem;
            margin-bottom: .45rem;
        }

        .public-form-row { margin-bottom: .8rem; }

        .public-label {
            display: block;
            margin-bottom: .32rem;
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
        }

        .public-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: .62rem .72rem;
            font-size: 1rem;
            color: var(--pub-text);
            background: #fff;
            box-sizing: border-box;
            min-height: 88px;
            resize: vertical;
        }

        .public-textarea:focus {
            outline: none;
            border-color: var(--pub-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .public-table-wrap { overflow-x: auto; }

        .public-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .95rem;
            background: #fff;
        }

        .public-table th,
        .public-table td {
            border: 1px solid var(--pub-border);
            padding: .68rem .72rem;
            line-height: 1.55;
            vertical-align: top;
        }

        .public-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 600;
        }

        .public-mini-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #206bc4;
            background: #206bc4;
            color: #fff;
            min-height: 34px;
            padding: .42rem .82rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: .92rem;
            font-weight: 650;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(32, 107, 196, .14);
        }

        .public-mini-btn:hover,
        .public-mini-btn:focus {
            border-color: #206bc4;
            background: #206bc4;
            color: #fff;
            outline: none;
            filter: brightness(0.92);
            opacity: 0.95;
        }

        .identity-card {
            padding: .85rem 1rem;
        }

        .identity-card .public-heading {
            margin-bottom: .55rem;
            font-size: 1.06rem;
        }

        .identity-card .public-table {
            font-size: .92rem;
        }

        .identity-card .public-table th,
        .identity-card .public-table td {
            padding: .46rem .65rem;
            line-height: 1.42;
        }

        .identity-card .public-table th {
            width: 180px;
        }

        .identity-card .public-mini-btn {
            min-height: 34px;
            padding: .38rem .78rem;
            font-size: .88rem;
        }

        .public-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 22px;
            background: rgba(15, 23, 42, .42);
        }

        .public-modal-backdrop.show {
            display: flex;
        }

        .public-modal {
            width: min(980px, 100%);
            max-height: min(82vh, 760px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .24);
        }

        .public-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .95rem 1.1rem;
            border-bottom: 1px solid var(--pub-border);
        }

        .public-modal-title {
            margin: 0;
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 750;
        }

        .public-modal-close {
            flex: 0 0 auto;
            cursor: pointer;
            width: 36px;
            height: 36px;
            padding: 0;
            border-radius: 8px;
            border-color: #94a3b8;
            background: #94a3b8;
            color: #ffffff;
            transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .public-modal-close .icon {
            width: 18px;
            height: 18px;
            stroke-width: 2.3;
        }

        .public-modal-close:hover,
        .public-modal-close:focus {
            outline: none;
            border-color: #64748b;
            background: #64748b;
            color: #ffffff;
            box-shadow: 0 0 0 2px rgba(100, 116, 139, .22);
        }

        .public-modal-body {
            overflow: auto;
            padding: 1.1rem;
        }

        .public-modal-section {
            padding-bottom: .95rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--pub-border);
        }

        .public-modal-section:last-child {
            padding-bottom: 0;
            margin-bottom: 0;
            border-bottom: 0;
        }

        .public-modal-subtitle {
            margin: 0 0 .45rem;
            color: #0f172a;
            font-size: .98rem;
            font-weight: 750;
        }

        .public-score-cell {
            text-align: center;
            white-space: nowrap;
        }

        .public-number-cell,
        .public-number-head {
            text-align: center;
            white-space: nowrap;
        }

        .public-score-option {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            cursor: pointer;
            user-select: none;
        }

        .public-score-option .form-selectgroup-input {
            margin: 0;
        }

        .public-score-option .form-selectgroup-label {
            min-width: 42px;
            text-align: center;
            font-size: .92rem;
            font-weight: 700;
            color: #334155;
            border-radius: 999px;
            padding: .34rem .7rem;
        }

        .public-score-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 2px;
            min-height: 96px;
        }

        .public-score-check {
            cursor: pointer;
        }

        .item-fill-badge {
            display: inline-flex;
            align-items: center;
            margin-top: .35rem;
            padding: .1rem .42rem;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 600;
            vertical-align: middle;
        }

        .item-fill-badge.ok {
            background: #dcfce7;
            color: #166534;
        }

        .item-fill-badge.pending {
            background: #fee2e2;
            color: #991b1b;
        }

        .public-item-aspect {
            display: block;
            margin-top: .42rem;
            color: var(--pub-muted);
            font-size: .82rem;
            line-height: 1.35;
        }

        .public-item-aspect strong {
            color: #475569;
            font-weight: 650;
        }


        .public-alert {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
            padding: .72rem .85rem;
            border-radius: 6px;
            margin-bottom: .9rem;
            font-size: .98rem;
        }

        .public-success {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            padding: .72rem .85rem;
            border-radius: 6px;
            margin-bottom: .9rem;
            font-size: .98rem;
        }

        #validation-section {
            scroll-margin-top: 18px;
        }

        .public-btn {
            display: inline-block;
            border: 1px solid var(--pub-blue);
            background: var(--pub-blue);
            color: #fff;
            padding: .72rem 1.15rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
        }

        .public-btn:hover { background: #1a3fa8; color: #fff; }

        .public-btn-light {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: var(--pub-text);
        }


        .public-btn:hover,
        .public-btn:focus {
            filter: brightness(0.92);
            opacity: 0.95;
            transition: filter 0.15s, opacity 0.15s;
        }

        .public-btn[style*="#f59e0b"]:hover,
        .public-btn[style*="#f59e0b"]:focus {
            background: #d97706 !important;
            border-color: #d97706 !important;
            color: #fff !important;
        }
        .public-btn[style*="#206bc4"]:hover,
        .public-btn[style*="#206bc4"]:focus {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .back-to-top-btn {
            position: fixed;
            right: max(18px, calc((100vw - 1080px) / 2 - 56px));
            bottom: 24px;
            z-index: 20;
            width: 40px;
            height: 40px;
            border: 1px solid #206bc4;
            border-radius: 8px;
            background: #206bc4;
            color: #fff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.18);
            cursor: pointer;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1;
            opacity: 0;
            pointer-events: none;
            transform: translateY(10px);
            transition: opacity .18s ease, transform .18s ease, background .18s ease;
        }

        .back-to-top-btn:hover,
        .back-to-top-btn:focus {
            background: #1d4ed8;
            border-color: #1d4ed8;
            outline: none;
        }

        .back-to-top-btn.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .validator-strip {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-left: 4px solid var(--pub-blue);
            border-radius: var(--pub-radius);
            padding: .9rem 1.05rem;
            font-size: .95rem;
            margin-bottom: .9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }

        .validator-strip .vname {
            display: inline-block;
            margin-right: .55rem;
            color: #0f172a;
            font-size: 1.02rem;
            font-weight: 700;
        }

        .validator-strip .vmeta {
            color: var(--pub-muted);
            font-size: .92rem;
        }

        #autosave-indicator {
            font-size: .82rem;
            color: var(--pub-muted);
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        #autosave-indicator.saved { color: #166534; }
        #autosave-indicator.saving { color: #854d0e; }
        #autosave-indicator.error { color: #991b1b; }

        .readonly-banner {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            padding: .72rem .85rem;
            border-radius: 6px;
            margin-bottom: .9rem;
            font-size: .98rem;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .public-shell { width: min(1080px, calc(100% - 14px)); margin-top: 10px; }
            .bundle-nav { flex-direction: column; }
            .bundle-progress { width: 100%; padding-top: .1rem; }
            .back-to-top-btn { right: 14px; bottom: 18px; }
        }
    </style>
</head>
<body>

<?php
$bundle           = isset($bundle) && is_array($bundle) ? $bundle : [];
$instruments      = isset($instruments) && is_array($instruments) ? $instruments : [];
$instrumentEntry  = isset($instrumentEntry) && is_array($instrumentEntry) ? $instrumentEntry : [];
$aspects          = isset($aspects) && is_array($aspects) ? $aspects : [];
$indicators       = isset($indicators) && is_array($indicators) ? $indicators : [];
$items            = isset($items) && is_array($items) ? $items : [];
$scale            = isset($scale) && is_array($scale) ? $scale : [];
$position         = isset($position) ? (int) $position : 1;
$total            = isset($total) ? (int) $total : 1;
$positionLabel    = sprintf('%03d', $position);
$totalLabel       = sprintf('%03d', $total);
$nextPos          = $nextPos ?? null;
$prevPos          = $prevPos ?? null;
$saveUrl          = $saveUrl ?? '';
$autosaveUrl      = $autosaveUrl ?? '';
$validatorSession = $validatorSession ?? [];
$savedAnswers     = isset($savedAnswers) && is_array($savedAnswers) ? $savedAnswers : [];
$savedProgress    = isset($savedProgress) && is_array($savedProgress) ? $savedProgress : [];
$progressMap      = isset($progressMap) && is_array($progressMap) ? $progressMap : [];
$token            = $bundle['token'] ?? '';
$isFinal          = isset($isFinal) ? (bool) $isFinal : false;
$summaryUrl       = $summaryUrl ?? base_url('paket/' . $token . '/ringkasan');
$masterPengantar  = trim((string) ($instrumentEntry['pengantar'] ?? ''));
$masterPetunjuk   = trim((string) ($instrumentEntry['petunjuk'] ?? ''));
$pengantarValidasi = trim((string) ($instrumentEntry['pengantar_validasi'] ?? ''));
$petunjukValidasi = trim((string) ($instrumentEntry['petunjuk_validasi'] ?? ''));

$scaleMin   = isset($scale['min']) ? (int) $scale['min'] : (int) ($instrumentEntry['skala_min'] ?? 1);
$scaleMax   = isset($scale['max']) ? (int) $scale['max'] : (int) ($instrumentEntry['skala_max'] ?? 4);
$scaleRange = array_map('intval', $scale['range'] ?? range($scaleMin, $scaleMax));
$scaleLabels = isset($scale['labels']) && is_array($scale['labels']) ? $scale['labels'] : sivalid_scale_labels(['skala_min' => $scaleMin, 'skala_max' => $scaleMax] + $instrumentEntry);
$masterScaleMin = (int) ($instrumentEntry['master_skala_min'] ?? $instrumentEntry['skala_min'] ?? 1);
$masterScaleMax = (int) ($instrumentEntry['master_skala_max'] ?? $instrumentEntry['skala_max'] ?? 4);
if ($masterScaleMin <= 0) {
    $masterScaleMin = 1;
}
if ($masterScaleMax < $masterScaleMin) {
    $masterScaleMax = $masterScaleMin;
}
$masterScaleRange = range($masterScaleMin, $masterScaleMax);

$text = static function (array $row, string $key, string $default = '-'): string {
    $value = $row[$key] ?? $default;
    if (is_scalar($value)) {
        $value = (string) $value;
        return $value !== '' ? $value : $default;
    }
    return $default;
};
?>

<div class="public-shell">
    <div class="validator-strip">
        <div>
            <span class="vname"><?= esc((string) ($validatorSession['validator_nama'] ?? '')) ?></span>
            <?php if (!empty($validatorSession['validator_instansi'])): ?>
                <span class="vmeta">&nbsp;·&nbsp; <?= esc($validatorSession['validator_instansi']) ?></span>
            <?php endif; ?>
            <?php if (!empty($validatorSession['validator_bidang_keahlian'])): ?>
                <span class="vmeta">&nbsp;·&nbsp; <?= esc($validatorSession['validator_bidang_keahlian']) ?></span>
            <?php endif; ?>
        </div>
        <span id="autosave-indicator"></span>
    </div>

    <?php if ($isFinal): ?>
        <div class="readonly-banner">Sesi Anda sudah disubmit final. Jawaban tidak dapat diubah.</div>
    <?php endif; ?>

    <div class="bundle-nav">
        <div class="bundle-nav-main">
            <div class="bundle-nav-top">
                <a href="<?= base_url('paket/' . esc($token)) ?>">&larr; Daftar Instrumen</a>
                <span class="bundle-index">Instrumen <?= esc($positionLabel) ?> dari <?= esc($totalLabel) ?></span>
            </div>
            <div class="bundle-title"><?= esc($text($instrumentEntry, 'judul')) ?></div>
        </div>
        <div class="bundle-progress">
            <?php foreach ($instruments as $idx => $instr): ?>
                <?php
                $stepNum = $idx + 1;
                $prog = $progressMap[(int) $instr['instrument_id']] ?? null;
                $stepSt = $prog['status'] ?? 'belum';
                if ($stepNum === $position) {
                    $cls = 'active';
                } elseif ($stepSt === 'selesai') {
                    $cls = 'done';
                } elseif ($stepSt === 'proses') {
                    $cls = 'proses';
                } else {
                    $cls = '';
                }
                ?>
                <div class="bundle-step <?= $cls ?>" title="Instrumen <?= esc(sprintf('%03d', $stepNum)) ?>"></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="public-card identity-card">
        <h2 class="public-heading">Identitas Instrumen yang Divalidasi</h2>
        <div class="public-table-wrap">
            <table class="public-table">
                <tbody>
                <tr>
                    <th style="width: 200px;">Kode</th>
                    <td><strong><?= esc($text($instrumentEntry, 'kode')) ?></strong></td>
                </tr>
                <tr>
                    <th>Nama Instrumen</th>
                    <td><?= esc($text($instrumentEntry, 'judul')) ?></td>
                </tr>
                <tr>
                    <th>Jenis</th>
                    <td><?= esc(title_case_label($text($instrumentEntry, 'jenis'))) ?></td>
                </tr>
                <tr>
                    <th>Kisi-Kisi Instrumen</th>
                    <td>
                        <button type="button" class="public-mini-btn" data-open-modal="kisi-modal">
                            Lihat Kisi-Kisi Instrumen
                        </button>
                    </td>
                </tr>
                <tr>
                    <th>Instrumen</th>
                    <td>
                        <button type="button" class="public-mini-btn" data-open-modal="instrument-modal">
                            Lihat Instrumen
                        </button>
                    </td>
                </tr>
                <tr>
                    <th>Validator</th>
                    <td><?= esc(trim((string) ($validatorSession['validator_nama'] ?? '')) ?: '-') ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pengantarValidasi !== '' || $petunjukValidasi !== ''): ?>
        <div class="public-card">
            <?php if ($pengantarValidasi !== ''): ?>
                <h2 class="public-heading">A. Pengantar Validasi</h2>
                <div class="public-muted public-rich-text" style="margin-bottom: .6rem;">
                    <?= render_rich_text_content($pengantarValidasi) ?>
                </div>
            <?php endif; ?>

            <?php if ($petunjukValidasi !== ''): ?>
                <h2 class="public-heading">B. Petunjuk Validasi</h2>
                <div class="public-muted public-rich-text">
                    <?= render_rich_text_content($petunjukValidasi) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form id="bundle-form" action="<?= esc($saveUrl) ?>" method="post">
        <?= csrf_field() ?>

        <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>

        <?php if (session()->has('success')): ?>
            <div class="public-success"><?= esc(session('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->has('error')): ?>
            <div class="public-alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <div class="public-card" id="validation-section">
            <?php
            $filledItems = 0;
            foreach ($items as $item) {
                $itemId = (int) $item['id'];
                $saved = $savedAnswers[$itemId] ?? null;
                if (!$saved) {
                    continue;
                }

                $tipeButir = $item['tipe_butir'] ?? 'skala';
                $scoreRaw = $saved['skor'] ?? null;
                $scoreInt = is_numeric($scoreRaw) ? (int) $scoreRaw : null;
                $hasSkor = $scoreInt !== null && in_array($scoreInt, $scaleRange, true);
                $hasJawabanTeks = array_key_exists('jawaban_teks', $saved)
                    && trim((string) $saved['jawaban_teks']) !== '';

                if (($tipeButir === 'skala' && $hasSkor) || ($tipeButir !== 'skala' && $hasJawabanTeks)) {
                    $filledItems++;
                }
            }
            ?>

            <div class="public-section-head">
                <h2 class="public-heading">Lembar Validasi Instrumen</h2>
                <div class="public-progress-widget">
                    <div class="public-progress-label">
                        <span>Progres butir</span>
                        <span id="progress-badge" class="public-progress-count">
                            <?= esc((string) $filledItems) ?>/<?= esc((string) count($items)) ?> terisi
                        </span>
                    </div>
                    <div class="progress progress-sm">
                        <div
                            id="progress-bar"
                            class="progress-bar bg-primary"
                            style="width: <?= count($items) > 0 ? esc((string) round(($filledItems / count($items)) * 100, 2)) : '0' ?>%;"
                            role="progressbar"
                            aria-valuenow="<?= esc((string) $filledItems) ?>"
                            aria-valuemin="0"
                            aria-valuemax="<?= esc((string) count($items)) ?>"
                        ></div>
                    </div>
                </div>
            </div>
            <div id="form-success-alert" class="public-success" style="display:none;"></div>
            <p class="public-muted">Berikan penilaian terhadap setiap butir instrumen. Progres disimpan otomatis.</p>

            <?php if (empty($items)): ?>
                <p class="public-muted">Belum ada butir yang dapat divalidasi.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table id="validation-items-table" class="public-table">
                        <thead>
                        <tr>
                            <th class="public-number-head" style="width: 76px;">No. Butir</th>
                            <th>Butir yang Dinilai</th>
                            <th style="width: 280px;">Skor Penilaian</th>
                            <th style="width: 200px;">Komentar</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $butirNo = 1; foreach ($items as $item): ?>
                            <?php
                            $itemId = (int) $item['id'];
                            $aspectName = '-';
                            foreach ($aspects as $asp) {
                                if ((int) $asp['id'] === (int) $item['aspect_id']) {
                                    $aspectName = $asp['nama_aspek'];
                                    break;
                                }
                            }
                            $tipeButir = $item['tipe_butir'] ?? 'skala';
                            $isRequired = (int) ($item['wajib'] ?? 1) === 1;
                            $saved = $savedAnswers[$itemId] ?? null;
                            $scoreRaw = $saved['skor'] ?? null;
                            $scoreInt = is_numeric($scoreRaw) ? (int) $scoreRaw : null;
                            $hasSkor = $saved
                                && $scoreInt !== null
                                && in_array($scoreInt, $scaleRange, true);
                            $hasJawabanTeks = $saved
                                && array_key_exists('jawaban_teks', $saved)
                                && trim((string) $saved['jawaban_teks']) !== '';
                            $isFilled = ($tipeButir === 'skala' && $hasSkor)
                                || ($tipeButir !== 'skala' && $hasJawabanTeks);
                            ?>
                            <tr>
                                <td class="public-number-cell"><?= esc((string) $butirNo++) ?></td>
                                <td>
                                    <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
                                    <span class="public-item-aspect"><strong>Aspek:</strong> <?= esc((string) $aspectName) ?></span>
                                    <br>
                                    <span class="item-fill-badge <?= $isFilled ? 'ok' : 'pending' ?>" data-fill-badge><?= $isFilled ? 'Sudah Nilai' : 'Belum Dinilai' ?></span>
                                </td>
                                <td>
                                    <?php if ($tipeButir === 'skala'): ?>
                                        <?php $savedSkor = $saved['skor'] ?? null; ?>
                                        <div class="public-score-row form-selectgroup form-selectgroup-pills">
                                            <?php foreach ($scaleRange as $score): ?>
                                                <?php
                                                $scoreLabel = (string) ($scaleLabels[$score] ?? ('Skor ' . $score));
                                                $shortLabel = $scaleMin === 1 && $scaleMax === 2
                                                    ? ($score === 1 ? 'TS' : 'S')
                                                    : sivalid_scale_short_label($scoreLabel, $score);
                                                ?>
                                                <label class="public-score-option form-selectgroup-item">
                                                    <input
                                                        class="form-selectgroup-input public-score-check"
                                                        type="radio"
                                                        name="answers[<?= $itemId ?>][skor]"
                                                        value="<?= esc((string) $score) ?>"
                                                        <?= $savedSkor !== null && (int) $savedSkor === $score ? 'checked' : '' ?>
                                                        <?= $isFinal ? 'disabled' : '' ?>
                                                    >
                                                    <span class="form-selectgroup-label" title="<?= esc($scoreLabel, 'attr') ?>">
                                                        <?= esc($shortLabel) ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <textarea
                                            name="answers[<?= $itemId ?>][jawaban_teks]"
                                            class="public-textarea"
                                            placeholder="Tuliskan jawaban"
                                            <?= $isFinal ? 'readonly' : '' ?>
                                        ><?= esc((string) ($saved['jawaban_teks'] ?? '')) ?></textarea>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <textarea
                                        name="answers[<?= $itemId ?>][komentar]"
                                        class="public-textarea"
                                        placeholder="Komentar/saran"
                                        <?= $isFinal ? 'readonly' : '' ?>
                                    ><?= esc((string) ($saved['komentar'] ?? '')) ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="public-card">
            <h2 class="public-heading">E. Komentar Umum</h2>
            <textarea class="public-textarea" name="komentar_umum" <?= $isFinal ? 'readonly' : '' ?>
                      placeholder="Tuliskan komentar umum terhadap instrumen ini."><?= esc((string) ($savedProgress['komentar_umum'] ?? '')) ?></textarea>
        </div>

        <div class="public-card">
            <h2 class="public-heading">F. Kesimpulan Validasi</h2>
            <?php
            $kesimpulanOptions = [
                'Layak digunakan tanpa revisi',
                'Layak digunakan dengan revisi kecil',
                'Perlu revisi besar sebelum digunakan',
                'Tidak layak digunakan',
            ];
            $savedKesimpulan = (string) ($savedProgress['kesimpulan'] ?? '');
            ?>
            <?php foreach ($kesimpulanOptions as $opt): ?>
                <div class="public-form-row">
                    <label class="public-label">
                        <input type="radio" name="kesimpulan" value="<?= esc($opt) ?>"
                               <?= $savedKesimpulan === $opt ? 'checked' : '' ?> 
                               <?= $isFinal ? 'disabled' : '' ?>>
                        <?= esc($opt) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="public-card" style="display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap;">
            <div>
                <a href="<?= base_url('paket/' . esc($token)) ?>"
                   class="public-btn public-btn-light"
                   style="display:inline-block; padding:.6rem 1rem; border-radius:6px; text-decoration:none; font-size:.9rem;">
                    &larr; Daftar Instrumen
                </a>
                <?php if ($prevPos !== null): ?>
                    <a href="<?= base_url('paket/' . esc($token) . '/isi/' . $prevPos) ?>"
                       class="public-btn"
                       style="display:inline-block; margin-left:.5rem; padding:.6rem 1rem; border-radius:6px; text-decoration:none; font-size:.9rem; background:#f59e0b; border-color:#f59e0b; color:#fff;">
                        &larr; Instrumen Sebelumnya
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($isFinal): ?>
                <div style="display:flex; gap:.6rem; align-items:center;">
                    <span style="font-size:.88rem; font-weight:600; color:#d97706;"><?= esc($positionLabel) ?> / <?= esc($totalLabel) ?></span>
                    <a href="<?= esc($summaryUrl) ?>" class="public-btn" style="font-size:.9rem;">Lihat Ringkasan</a>
                </div>
            <?php else: ?>
                <div style="display:flex; gap:.6rem; align-items:center;">
                    <span style="font-size:.88rem; font-weight:600; color:#d97706;"><?= esc($positionLabel) ?> / <?= esc($totalLabel) ?></span>
                    <button type="button" id="btn-save-progress" class="public-btn" style="font-size:.9rem; background:#f59e0b; border-color:#f59e0b; color:#fff;">
                        Simpan Progres
                    </button>
                    <button type="submit" name="action" value="save_next" class="public-btn" style="font-size:.9rem; background:#206bc4; border-color:#206bc4; color:#fff;">
                        <?php if ($nextPos !== null): ?>
                            Simpan & Lanjut &rarr;
                        <?php else: ?>
                            Simpan & Selesai
                        <?php endif; ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<button type="button" id="back-to-top" class="back-to-top-btn" aria-label="Kembali ke atas" title="Kembali ke atas">&uarr;</button>

<div id="kisi-modal" class="public-modal-backdrop" aria-hidden="true">
    <div class="public-modal" role="dialog" aria-modal="true" aria-labelledby="kisi-modal-title">
        <div class="public-modal-head">
            <h2 id="kisi-modal-title" class="public-modal-title">Kisi-Kisi Instrumen</h2>
            <button type="button" class="btn btn-icon btn-ghost-secondary public-modal-close" data-close-modal aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="public-modal-body">
            <div class="public-modal-section">
                <h3 class="public-modal-subtitle">Kisi-Kisi Instrumen <?= esc($text($instrumentEntry, 'judul')) ?></h3>
            </div>
            <div class="public-modal-section">
            <?php if (empty($aspects)): ?>
                <p class="public-muted">Kisi-kisi belum tersedia.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table class="public-table">
                        <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th style="width: 260px;">Aspek</th>
                            <th>Indikator</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($aspects as $aspectIndex => $aspect): ?>
                            <?php
                            $aspectIndicators = array_values(array_filter($indicators, static function ($indicator) use ($aspect) {
                                return (int) ($indicator['aspect_id'] ?? 0) === (int) ($aspect['id'] ?? 0);
                            }));
                            ?>
                            <?php if (empty($aspectIndicators)): ?>
                                <tr>
                                    <td><?= esc((string) ($aspectIndex + 1)) ?></td>
                                    <td><?= esc((string) ($aspect['nama_aspek'] ?? '-')) ?></td>
                                    <td><em>Belum ada indikator.</em></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($aspectIndicators as $indicatorIndex => $indicator): ?>
                                    <tr>
                                        <?php if ($indicatorIndex === 0): ?>
                                            <td rowspan="<?= count($aspectIndicators) ?>"><?= esc((string) ($aspectIndex + 1)) ?></td>
                                            <td rowspan="<?= count($aspectIndicators) ?>"><?= esc((string) ($aspect['nama_aspek'] ?? '-')) ?></td>
                                        <?php endif; ?>
                                        <td><?= nl2br(esc((string) ($indicator['indikator'] ?? '-'))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="instrument-modal" class="public-modal-backdrop" aria-hidden="true">
    <div class="public-modal" role="dialog" aria-modal="true" aria-labelledby="instrument-modal-title">
        <div class="public-modal-head">
            <h2 id="instrument-modal-title" class="public-modal-title">Instrumen yang Divalidasi</h2>
            <button type="button" class="btn btn-icon btn-ghost-secondary public-modal-close" data-close-modal aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="public-modal-body">
            <div class="public-modal-section">
                <h3 class="public-modal-subtitle">Nama Instrumen</h3>
                <div class="public-rich-text"><?= esc($text($instrumentEntry, 'judul')) ?></div>
            </div>
            <div class="public-modal-section">
                <h3 class="public-modal-subtitle">Pengantar</h3>
                <?php if ($masterPengantar !== ''): ?>
                    <div class="public-muted public-rich-text"><?= render_rich_text_content($masterPengantar) ?></div>
                <?php else: ?>
                    <p class="public-muted">Pengantar belum tersedia.</p>
                <?php endif; ?>
            </div>
            <div class="public-modal-section">
                <h3 class="public-modal-subtitle">Petunjuk</h3>
                <?php if ($masterPetunjuk !== ''): ?>
                    <div class="public-muted public-rich-text"><?= render_rich_text_content($masterPetunjuk) ?></div>
                <?php else: ?>
                    <p class="public-muted">Petunjuk belum tersedia.</p>
                <?php endif; ?>
            </div>
            <div class="public-modal-section">
                <h3 class="public-modal-subtitle">Tabel Instrumen</h3>
            <?php if (empty($items)): ?>
                <p class="public-muted">Butir instrumen belum tersedia.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table class="public-table">
                        <thead>
                        <tr>
                            <th style="width: 76px;">No. Butir</th>
                            <th>Butir Pernyataan</th>
                            <?php foreach ($masterScaleRange as $score): ?>
                                <th class="public-score-cell" style="width: 56px;"><?= esc((string) $score) ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $modalButirNo = 1; foreach ($items as $item): ?>
                            <?php
                            $aspectName = '-';
                            foreach ($aspects as $aspect) {
                                if ((int) ($aspect['id'] ?? 0) === (int) ($item['aspect_id'] ?? 0)) {
                                    $aspectName = (string) ($aspect['nama_aspek'] ?? '-');
                                    break;
                                }
                            }
                            ?>
                            <tr>
                                <td><?= esc((string) $modalButirNo++) ?></td>
                                <td>
                                    <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
                                    <span class="public-item-aspect"><strong>Aspek:</strong> <?= esc($aspectName) ?></span>
                                </td>
                                <?php foreach ($masterScaleRange as $score): ?>
                                    <td class="public-score-cell">&bigcirc;</td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var AUTOSAVE_URL = <?= json_encode($autosaveUrl) ?>;
    var csrfName = <?= json_encode(csrf_token()) ?>;
    var csrfHash = <?= json_encode(csrf_hash()) ?>;
    var form = document.getElementById('bundle-form');
    var indicator = document.getElementById('autosave-indicator');
    var progressBadge = document.getElementById('progress-badge');
    var progressBar = document.getElementById('progress-bar');
    var validationSection = document.getElementById('validation-section');
    var saveAlert = document.getElementById('form-success-alert');
    var backToTop = document.getElementById('back-to-top');
    var activeModal = null;
    var saving = false;
    var isSubmitting = false;
    var autosaveTimer = null;
    var saveAlertTimer = null;
    var isFinal = <?= json_encode($isFinal) ?>;

    function setIndicator(type, msg) {
        if (!indicator) return;
        indicator.className = type;
        indicator.textContent = msg;
    }

    function showSaveAlert(type, msg) {
        if (!saveAlert) return;
        if (saveAlertTimer) {
            window.clearTimeout(saveAlertTimer);
            saveAlertTimer = null;
        }

        saveAlert.className = type === 'success' ? 'public-success' : 'public-alert';
        saveAlert.textContent = msg;
        saveAlert.style.display = '';

        window.setTimeout(function () {
            (validationSection || saveAlert).scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);

        if (type === 'success') {
            saveAlertTimer = window.setTimeout(function () {
                saveAlert.style.display = 'none';
                saveAlert.textContent = '';
                saveAlertTimer = null;
            }, 3500);
        }
    }

    function syncCsrfInput() {
        if (!form) return;
        var inp = form.querySelector('input[name="' + csrfName + '"]');
        if (inp) {
            inp.value = csrfHash;
        }
    }

    function toggleBackToTop() {
        if (!backToTop) return;
        backToTop.classList.toggle('show', window.scrollY > 360);
    }

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        window.addEventListener('scroll', toggleBackToTop, { passive: true });
        toggleBackToTop();
    }

    function openModal(modal) {
        if (!modal) return;
        activeModal = modal;
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        var closeBtn = modal.querySelector('[data-close-modal]');
        if (closeBtn) {
            closeBtn.focus();
        }
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (activeModal === modal) {
            activeModal = null;
        }
    }

    document.querySelectorAll('[data-open-modal]').forEach(function (button) {
        button.addEventListener('click', function () {
            openModal(document.getElementById(button.getAttribute('data-open-modal')));
        });
    });

    document.querySelectorAll('.public-modal-backdrop').forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            var closeTrigger = event.target && event.target.closest
                ? event.target.closest('[data-close-modal]')
                : null;

            if (event.target === modal || closeTrigger) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && activeModal) {
            closeModal(activeModal);
        }
    });

    function sendProgressSave(onDone, onFail) {
        if (!form) return;

        saving = true;

        var data = new FormData(form);
        data.set(csrfName, csrfHash);

        fetch(AUTOSAVE_URL, {
            method: 'POST',
            body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) {
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                return res.json();
            })
            .then(function (json) {
                saving = false;
                if (json.ok) {
                    csrfHash = json.csrf_hash;
                    csrfName = json.csrf_name;
                    syncCsrfInput();
                    onDone(json);
                } else {
                    onFail(json);
                }
            })
            .catch(function () {
                saving = false;
                onFail(null);
            });
    }

    function doAutosave() {
        if (saving || isSubmitting) return;

        setIndicator('saving', 'Menyimpan...');

        sendProgressSave(function (json) {
            setIndicator('saved', 'Tersimpan ' + json.saved_at);
        }, function () {
            setIndicator('error', 'Gagal menyimpan otomatis');
        });
    }

    // AJAX untuk tombol Simpan Progres
    var btnSaveProgress = document.getElementById('btn-save-progress');
    if (btnSaveProgress) {
        btnSaveProgress.addEventListener('click', function (e) {
            e.preventDefault();
            if (isSubmitting) return;
            if (autosaveTimer) {
                window.clearTimeout(autosaveTimer);
                autosaveTimer = null;
            }
            btnSaveProgress.disabled = true;
            btnSaveProgress.textContent = 'Menyimpan...';

            var runManualSave = function () {
                if (saving) {
                    window.setTimeout(runManualSave, 120);
                    return;
                }

                sendProgressSave(function (json) {
                    btnSaveProgress.disabled = false;
                    btnSaveProgress.textContent = 'Simpan Progres';
                    setIndicator('saved', 'Tersimpan ' + json.saved_at);
                    showSaveAlert('success', json.message || 'Progres berhasil disimpan.');
                }, function () {
                    btnSaveProgress.disabled = false;
                    btnSaveProgress.textContent = 'Simpan Progres';
                    setIndicator('error', 'Gagal menyimpan otomatis');
                    showSaveAlert('error', 'Gagal menyimpan progres!');
                });
            };

            runManualSave();
        });
    }

    function queueAutosave(delayMs) {
        if (isFinal || isSubmitting) return;
        if (autosaveTimer) {
            window.clearTimeout(autosaveTimer);
        }

        autosaveTimer = window.setTimeout(function () {
            autosaveTimer = null;
            doAutosave();
        }, delayMs);
    }

    function refreshItemBadge(row) {
        if (!row) return;

        var badge = row.querySelector('[data-fill-badge]');
        if (!badge) return;

        var scoreInputs = row.querySelectorAll('.public-score-check');
        var checkedRadio = row.querySelector('.public-score-check:checked');
        var textareas = row.querySelectorAll('textarea[name$="[jawaban_teks]"]');
        var hasTextValue = false;

        textareas.forEach(function (ta) {
            if ((ta.value || '').trim().length > 0) {
                hasTextValue = true;
            }
        });

        var isFilled = scoreInputs.length > 0 ? Boolean(checkedRadio) : hasTextValue;
        badge.classList.toggle('ok', isFilled);
        badge.classList.toggle('pending', !isFilled);
        badge.textContent = isFilled ? 'Sudah Nilai' : 'Belum Dinilai';
    }

    function refreshAllItemBadges() {
        document.querySelectorAll('#validation-items-table tbody tr').forEach(function (row) {
            refreshItemBadge(row);
        });

        refreshProgressBadge();
    }

    function refreshProgressBadge() {
        if (!progressBadge) return;

        var rows = document.querySelectorAll('#validation-items-table tbody tr');
        var total = rows.length;
        var filled = 0;

        rows.forEach(function (row) {
            var scoreInputs = row.querySelectorAll('.public-score-check');
            var checkedRadio = row.querySelector('.public-score-check:checked');
            var textareas = row.querySelectorAll('textarea[name$="[jawaban_teks]"]');
            var hasTextValue = false;

            textareas.forEach(function (ta) {
                if ((ta.value || '').trim().length > 0) {
                    hasTextValue = true;
                }
            });

            if (scoreInputs.length > 0 ? Boolean(checkedRadio) : hasTextValue) {
                filled++;
            }
        });

        progressBadge.textContent = filled + '/' + total + ' terisi';
        if (progressBar) {
            var percent = total > 0 ? (filled / total) * 100 : 0;
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', String(filled));
            progressBar.setAttribute('aria-valuemax', String(total));
        }
    }

    function selectScoreInput(input, row) {
        if (!input || input.disabled) return;
        if (!row) row = input.closest('tr');

        refreshItemBadge(row);
        refreshProgressBadge();
        queueAutosave(300);
    }

    if (isFinal) {
        setIndicator('saved', 'Mode baca saja');
        return;
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            if (isSubmitting) {
                return;
            }

            if (autosaveTimer) {
                window.clearTimeout(autosaveTimer);
                autosaveTimer = null;
            }

            // If autosave request is still running, wait briefly for newest CSRF hash.
            if (saving) {
                event.preventDefault();
                setIndicator('saving', 'Menunggu penyimpanan otomatis...');
                var retrySubmit = function () {
                    if (saving) {
                        window.setTimeout(retrySubmit, 120);
                        return;
                    }

                    if (!isSubmitting) {
                        isSubmitting = true;
                        syncCsrfInput();
                        form.submit();
                    }
                };

                retrySubmit();
                return;
            }

            isSubmitting = true;
            syncCsrfInput();
        });
    }

    refreshAllItemBadges();

    document.addEventListener('change', function (event) {
        var target = event.target;
        if (!target || !target.closest) return;
        var row = target.closest('tr');
        if (!row) return;
        if (target.matches('.public-score-check')) {
            selectScoreInput(target, row);
        }
    }, true);

    document.addEventListener('input', function (event) {
        var target = event.target;
        if (!target || !target.closest) return;
        var row = target.closest('tr');
        if (!row) return;
        if (target.matches('textarea[name^="answers["]')) {
            refreshItemBadge(row);
            refreshProgressBadge();
            queueAutosave(900);
        }
    }, true);

    window.setInterval(doAutosave, 60000);
    window.addEventListener('beforeunload', function () {
        if (isSubmitting) return;
        doAutosave();
    });
})();
</script>

</body>
</html>
