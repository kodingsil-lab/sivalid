<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Pengisian Instrumen') ?></title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-bg: #edf2f5;
            --pub-surface: #ffffff;
            --pub-border: #cfd9e4;
            --pub-border-soft: #dde6ef;
            --pub-text: #0f172a;
            --pub-muted: #53657a;
            --pub-blue: #0b63b6;
            --pub-blue-soft: #f3f8fd;
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
            color: var(--pub-text);
        }

        .public-meta {
            color: var(--pub-muted);
            font-size: .94rem;
        }

        .public-meta span + span::before {
            content: ' / ';
            color: #94a3b8;
        }

        .rich-text-content table {
            width: 100%;
            border-collapse: collapse;
            margin: .7rem 0;
            background: #fff;
        }

        .rich-text-content th,
        .rich-text-content td {
            border: 1px solid var(--pub-border-soft);
            padding: .52rem .65rem;
            vertical-align: top;
        }

        .rich-text-content th {
            background: #f1f5f9;
            font-weight: 680;
            text-align: left;
        }

        .section-title {
            margin: 1.2rem 0 .55rem;
            color: var(--pub-text);
            font-size: 1.08rem;
            font-weight: 720;
            line-height: 1.35;
        }

        .section-intro {
            margin: 0 0 .85rem;
            color: var(--pub-text);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
            margin-bottom: .75rem;
        }

        .form-row.single {
            grid-template-columns: 1fr;
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
            color: var(--pub-text);
        }

        .form-group label .req {
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
            transition: border-color .15s, box-shadow .15s;
        }

        .form-group textarea {
            min-height: 104px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--pub-blue);
            box-shadow: 0 0 0 .25rem rgba(11, 99, 182, .16);
        }

        .instrument-info-table,
        .scale-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--pub-border-soft);
            background: #fff;
            font-size: .92rem;
        }

        .instrument-info-table th,
        .instrument-info-table td,
        .scale-table th,
        .scale-table td,
        .items-table th,
        .items-table td {
            border: 1px solid var(--pub-border-soft);
            padding: .52rem .65rem;
            vertical-align: top;
            line-height: 1.42;
        }

        .instrument-info-table th,
        .scale-table th,
        .items-table th {
            background: #f1f5f9;
            color: #1f2a3d;
            font-weight: 680;
            text-align: left;
        }

        .instrument-info-table th {
            width: 180px;
        }

        .item-number {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 999px;
            background: #edf5fc;
            color: var(--pub-blue);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .item-aspect {
            color: var(--pub-muted);
            font-size: .88rem;
            margin-bottom: .25rem;
        }

        .item-required {
            display: block;
            color: var(--pub-muted);
            font-size: .82rem;
            margin-top: .35rem;
        }

        .score-options {
            display: flex;
            flex-wrap: wrap;
            gap: .38rem;
        }

        .score-option {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .32rem;
            min-height: 34px;
            min-width: 46px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: .25rem .48rem;
            background: #fff;
            color: var(--pub-text);
            font-weight: 600;
            cursor: pointer;
        }

        .text-answer {
            width: 100%;
            min-height: 76px;
            border: 1px solid var(--pub-border);
            border-radius: 6px;
            padding: .5rem .65rem;
            resize: vertical;
        }

        .decision-list {
            display: grid;
            gap: .45rem;
            margin-bottom: 1rem;
        }

        .decision-item {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            border: 1px solid var(--pub-border-soft);
            border-radius: 6px;
            padding: .65rem .75rem;
            cursor: pointer;
        }

        .decision-item:hover {
            background: var(--pub-blue-soft);
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
            align-items: center;
            gap: .6rem;
            margin-top: .85rem;
            flex-wrap: wrap;
        }

        .alert {
            border-radius: 6px;
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
        }
    </style>
</head>
<body>

<?php
$scaleMin = $scale['min'] ?? (int) ($link['skala_min'] ?? 1);
$scaleMax = $scale['max'] ?? (int) ($link['skala_max'] ?? 4);
$scaleRange = $scale['range'] ?? range($scaleMin, $scaleMax);
$petunjukPenyebaran = trim((string) ($link['petunjuk_penyebaran'] ?? ''));
$petunjuk = $petunjukPenyebaran !== ''
    ? $petunjukPenyebaran
    : 'Pilih skor ' . $scaleMin . ' sampai ' . $scaleMax . ' sesuai tingkat persetujuan Anda.';
$jenisInstrumen = title_case_label((string) ($link['jenis'] ?? 'Instrumen'));
?>

<div class="public-shell">
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

    <div class="public-card">
        <div class="public-header">
            <h1 class="public-title"><?= esc($link['judul'] ?: ($link['judul_link'] ?: 'Pengisian Instrumen')) ?></h1>
            <div class="public-meta">
                <span><?= esc($jenisInstrumen) ?></span>
                <span>Sasaran: <?= esc($link['sasaran'] ?: 'Responden') ?></span>
                <?php if (!empty($link['tanggal_selesai'])): ?>
                    <span>Batas Pengisian: <?= esc(format_tanggal_indonesia($link['tanggal_selesai'])) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="section-title">Identitas</h2>

        <form action="<?= base_url('isi/' . $link['token']) ?>" method="post">
            <?= view('public/partials/respondent_identity_summary', compact('respondentIdentity', 'link', 'identityFields')) ?>

            <h2 class="section-title">Informasi Instrumen</h2>
            <table class="instrument-info-table mb-3">
                <tbody>
                    <tr>
                        <th>Kode Instrumen</th>
                        <td><?= esc($link['kode']) ?></td>
                    </tr>
                    <tr>
                        <th>Judul Instrumen</th>
                        <td><?= esc($link['judul']) ?></td>
                    </tr>
                    <tr>
                        <th>Skala</th>
                        <td><?= esc((string) $scaleMin) ?> sampai <?= esc((string) $scaleMax) ?></td>
                    </tr>
                    <tr>
                        <th>Kuota</th>
                        <td><?= !empty($link['maksimal_respon']) ? esc($link['maksimal_respon']) . ' respon' : 'Tidak dibatasi' ?></td>
                    </tr>
                </tbody>
            </table>

            <h2 class="section-title">Petunjuk Pengisian</h2>
            <div class="section-intro">
                <?= render_rich_text_content($petunjuk) ?>
            </div>

            <h2 class="section-title">Butir Instrumen</h2>
            <?php if (empty($items)): ?>
                <p class="section-intro">Butir instrumen belum tersedia.</p>
            <?php else: ?>
                <?= view('public/partials/fill_progress') ?>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 64px;">No</th>
                            <th>Pernyataan</th>
                            <th style="width: 250px;">Jawaban</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                            <?php
                            $aspectName = '-';
                            foreach ($aspects as $aspect) {
                                if ((int) $aspect['id'] === (int) $item['aspect_id']) {
                                    $aspectName = $aspect['nama_aspek'];
                                    break;
                                }
                            }
                            $tipeButir = $item['tipe_butir'] ?? 'skala';
                            $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                            ?>
                            <tr class="instrument-item-row">
                                <td><span class="item-number"><?= $index + 1 ?></span></td>
                                <td>
                                    <div class="item-aspect"><?= esc($aspectName) ?></div>
                                    <?= nl2br(esc($item['pernyataan'])) ?>
                                    <span class="item-required"><?= (int) ($item['wajib'] ?? 1) === 1 ? 'Wajib diisi' : 'Opsional' ?></span>
                                </td>
                                <td>
                                    <?php if ($tipeButir === 'skala'): ?>
                                        <div class="score-options">
                                            <?php foreach ($scaleRange as $score): ?>
                                                <label class="score-option">
                                                    <input type="radio" name="answers[<?= $item['id'] ?>][skor]" value="<?= esc((string) $score) ?>" <?= $isRequired ?>>
                                                    <?= esc((string) $score) ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <textarea class="text-answer" name="answers[<?= $item['id'] ?>][jawaban_teks]" placeholder="Tuliskan jawaban" <?= $isRequired ?>><?= esc(old('answers.' . $item['id'] . '.jawaban_teks')) ?></textarea>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?= view('public/partials/justification_fields', compact('justificationConfig')) ?>

            <div class="form-actions">
                <button type="submit" class="pub-btn">Kirim Pengisian</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
