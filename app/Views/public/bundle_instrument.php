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
            background: linear-gradient(180deg, #eef2ff 0%, var(--pub-bg) 28%);
            color: var(--pub-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.6;
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
            align-items: center;
            gap: .75rem;
            padding: .65rem .9rem;
            background: var(--pub-blue-soft);
            border: 1px solid #bfdbfe;
            border-radius: var(--pub-radius);
            margin-bottom: .9rem;
            font-size: .95rem;
            flex-wrap: wrap;
        }

        .bundle-nav a {
            color: var(--pub-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .bundle-nav a:hover { text-decoration: underline; }

        .bundle-progress {
            display: flex;
            gap: 4px;
            margin-left: auto;
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

        .public-progress-badge {
            margin-left: auto;
            font-size: .82rem;
            font-weight: 600;
            padding: .35rem .58rem;
        }

        .public-rich-text,
        .public-rich-text.ql-editor {
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

        .public-score-option {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            font-size: .95rem;
            font-weight: 500;
            color: #334155;
            border: 1px solid #dbeafe;
            background: var(--pub-blue-soft);
            border-radius: 999px;
            min-height: 34px;
            padding: 4px 10px;
            white-space: nowrap;
        }

        .public-score-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .public-required-note { color: #334155; font-size: .88rem; }

        .item-fill-badge {
            display: inline-flex;
            align-items: center;
            margin-left: .4rem;
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

        .public-btn[style*="#38bdf8"]:hover,
        .public-btn[style*="#38bdf8"]:focus {
            background: #0ea5e9 !important;
            border-color: #0ea5e9 !important;
            color: #fff !important;
        }
        .public-btn[style*="#206bc4"]:hover,
        .public-btn[style*="#206bc4"]:focus {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .validator-strip {
            background: var(--pub-blue-soft);
            border: 1px solid #bfdbfe;
            border-radius: var(--pub-radius);
            padding: .6rem .9rem;
            font-size: .95rem;
            margin-bottom: .9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .validator-strip .vname { font-weight: 600; color: var(--pub-blue); }
        .validator-strip .vmeta { color: var(--pub-muted); font-size: .83rem; }

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
$pengantarValidasi = trim((string) ($instrumentEntry['pengantar_validasi'] ?? ''));
$petunjukValidasi = trim((string) ($instrumentEntry['petunjuk_validasi'] ?? ''));

$scaleMin   = isset($scale['min']) ? (int) $scale['min'] : (int) ($instrumentEntry['skala_min'] ?? 1);
$scaleMax   = isset($scale['max']) ? (int) $scale['max'] : (int) ($instrumentEntry['skala_max'] ?? 4);
$scaleRange = array_map('intval', $scale['range'] ?? range($scaleMin, $scaleMax));

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
        <a href="<?= base_url('paket/' . esc($token)) ?>">&larr; Daftar Instrumen</a>
        <strong>Instrumen <?= esc($positionLabel) ?> dari <?= esc($totalLabel) ?></strong>
        <span style="color: var(--pub-muted);">- <?= esc($text($instrumentEntry, 'judul')) ?></span>
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

    <div class="public-card">
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

        <div class="public-card">
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
                <span id="progress-badge" class="badge bg-primary-lt text-primary public-progress-badge">
                    Progres butir: <?= esc((string) $filledItems) ?>/<?= esc((string) count($items)) ?> terisi
                </span>
            </div>
            <p class="public-muted">Berikan penilaian terhadap setiap butir instrumen. Progres disimpan otomatis.</p>

            <?php if (empty($items)): ?>
                <p class="public-muted">Belum ada butir yang dapat divalidasi.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table id="validation-items-table" class="public-table">
                        <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 140px;">Aspek</th>
                            <th style="width: 50px;">No. Butir</th>
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
                                <td><?= esc((string) ($item['nomor'] ?? '-')) ?></td>
                                <td><?= esc((string) $aspectName) ?></td>
                                <td><?= $butirNo++ ?></td>
                                <td>
                                    <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
                                    <br>
                                    <small class="public-required-note"><?= $isRequired ? 'Wajib diisi' : 'Opsional' ?></small>
                                    <span class="item-fill-badge <?= $isFilled ? 'ok' : 'pending' ?>" data-fill-badge><?= $isFilled ? 'Sudah Nilai' : 'Belum Dinilai' ?></span>
                                </td>
                                <td>
                                    <?php if ($tipeButir === 'skala'): ?>
                                        <?php $savedSkor = $saved['skor'] ?? null; ?>
                                        <div class="public-score-row">
                                            <?php foreach ($scaleRange as $score): ?>
                                                <label class="public-score-option">
                                                    <input
                                                        type="radio"
                                                        name="answers[<?= $itemId ?>][skor]"
                                                        value="<?= esc((string) $score) ?>"
                                                        <?= $savedSkor !== null && (int) $savedSkor === $score ? 'checked' : '' ?>
                                                        <?= $isFinal ? 'disabled' : '' ?>
                                                    >
                                                    <?= esc((string) $score) ?>
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
                <?php if ($prevPos !== null): ?>
                    <a href="<?= base_url('paket/' . esc($token) . '/isi/' . $prevPos) ?>"
                       class="public-btn"
                       style="display:inline-block; padding:.6rem 1rem; border-radius:6px; text-decoration:none; font-size:.9rem; background:#38bdf8; border-color:#38bdf8; color:#fff;">
                        &larr; Instrumen Sebelumnya
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($isFinal): ?>
                <div style="display:flex; gap:.6rem; align-items:center;">
                    <span style="font-size:.88rem; color:var(--pub-muted);"><?= esc($positionLabel) ?> / <?= esc($totalLabel) ?></span>
                    <a href="<?= esc($summaryUrl) ?>" class="public-btn" style="font-size:.9rem;">Lihat Ringkasan</a>
                </div>
            <?php else: ?>
                <div style="display:flex; gap:.6rem; align-items:center;">
                    <span style="font-size:.88rem; color:var(--pub-muted);"><?= esc($positionLabel) ?> / <?= esc($totalLabel) ?></span>
                    <button type="button" id="btn-save-progress" class="public-btn" style="font-size:.9rem; background:#38bdf8; border-color:#38bdf8; color:#fff;">
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

<script>
(function () {
    'use strict';

    var AUTOSAVE_URL = <?= json_encode($autosaveUrl) ?>;
    var csrfName = <?= json_encode(csrf_token()) ?>;
    var csrfHash = <?= json_encode(csrf_hash()) ?>;
    var form = document.getElementById('bundle-form');
    var indicator = document.getElementById('autosave-indicator');
    var progressBadge = document.getElementById('progress-badge');
    var saving = false;
    var isSubmitting = false;
    var autosaveTimer = null;
    var isFinal = <?= json_encode($isFinal) ?>;

    function setIndicator(type, msg) {
        if (!indicator) return;
        indicator.className = type;
        indicator.textContent = msg;
    }

    function syncCsrfInput() {
        if (!form) return;
        var inp = form.querySelector('input[name="' + csrfName + '"]');
        if (inp) {
            inp.value = csrfHash;
        }
    }

    function doAutosave() {
        if (saving || isSubmitting) return;
        if (!form) return;

        saving = true;
        setIndicator('saving', 'Menyimpan...');

        var data = new FormData(form);
        data.set(csrfName, csrfHash);

        fetch(AUTOSAVE_URL, {
            method: 'POST',
            body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                saving = false;
                if (json.ok) {
                    csrfHash = json.csrf_hash;
                    csrfName = json.csrf_name;
                    syncCsrfInput();
                    setIndicator('saved', 'Tersimpan ' + json.saved_at);
                } else {
                    setIndicator('error', 'Gagal menyimpan otomatis');
                }
            })
            .catch(function () {
                saving = false;
                setIndicator('error', 'Gagal menyimpan otomatis');
            });
    }

    // AJAX untuk tombol Simpan Progres
    var btnSaveProgress = document.getElementById('btn-save-progress');
    if (btnSaveProgress) {
        btnSaveProgress.addEventListener('click', function (e) {
            e.preventDefault();
            if (saving || isSubmitting) return;
            var data = new FormData(form);
            data.set(csrfName, csrfHash);
            btnSaveProgress.disabled = true;
            btnSaveProgress.textContent = 'Menyimpan...';
            fetch(AUTOSAVE_URL, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                btnSaveProgress.disabled = false;
                btnSaveProgress.textContent = 'Simpan Progres';
                if (json.ok) {
                    csrfHash = json.csrf_hash;
                    csrfName = json.csrf_name;
                    syncCsrfInput();
                    // Tampilkan alert sukses
                    var alert = document.getElementById('form-success-alert');
                    if (alert) {
                        alert.className = 'public-success';
                        alert.textContent = json.message || 'Progres berhasil disimpan.';
                        alert.style.display = '';
                    }
                    // Scroll ke alert
                    setTimeout(function() {
                        alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                } else {
                    var alert = document.getElementById('form-success-alert');
                    if (alert) {
                        alert.className = 'public-alert';
                        alert.textContent = 'Gagal menyimpan progres!';
                        alert.style.display = '';
                        alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            })
            .catch(function () {
                btnSaveProgress.disabled = false;
                btnSaveProgress.textContent = 'Simpan Progres';
                var alert = document.getElementById('form-success-alert');
                if (alert) {
                    alert.className = 'public-alert';
                    alert.textContent = 'Gagal menyimpan progres!';
                    alert.style.display = '';
                    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
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

        var checkedRadio = row.querySelector('input[type="radio"][name^="answers["]:checked');
        var textareas = row.querySelectorAll('textarea[name^="answers["]');
        var hasTextValue = false;

        textareas.forEach(function (ta) {
            if ((ta.value || '').trim().length > 0) {
                hasTextValue = true;
            }
        });

        var isFilled = Boolean(checkedRadio) || hasTextValue;
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
            var checkedRadio = row.querySelector('input[type="radio"][name^="answers["]:checked');
            var textareas = row.querySelectorAll('textarea[name^="answers["]');
            var hasTextValue = false;

            textareas.forEach(function (ta) {
                if ((ta.value || '').trim().length > 0) {
                    hasTextValue = true;
                }
            });

            if (Boolean(checkedRadio) || hasTextValue) {
                filled++;
            }
        });

        progressBadge.textContent = 'Progres butir: ' + filled + '/' + total + ' terisi';
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
        if (target.matches('input[type="radio"][name^="answers["]')) {
            refreshItemBadge(row);
            refreshProgressBadge();
            queueAutosave(300);
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
