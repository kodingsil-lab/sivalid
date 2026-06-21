<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Lembar Observasi') ?></title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
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

        .public-title {
            margin: 0 0 .25rem;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .public-muted {
            color: var(--pub-muted);
            font-size: 1rem;
        }

        .public-heading {
            margin: 0 0 .75rem;
            font-size: 1.18rem;
            font-weight: 700;
            color: var(--pub-text);
        }

        .public-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .8rem;
        }

        .public-form-row {
            margin-bottom: .8rem;
        }

        .public-label {
            display: block;
            margin-bottom: .32rem;
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
        }

        .public-input,
        .public-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: .62rem .72rem;
            font-size: 1rem;
            color: var(--pub-text);
            background: #fff;
            box-sizing: border-box;
        }

        .public-input:focus,
        .public-textarea:focus {
            outline: none;
            border-color: var(--pub-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .public-textarea {
            min-height: 88px;
            resize: vertical;
        }

        .public-table-wrap {
            overflow-x: auto;
        }

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

        .rich-text-content table {
            width: 100%;
            border-collapse: collapse;
            margin: .7rem 0;
            background: #fff;
        }

        .rich-text-content th,
        .rich-text-content td {
            border: 1px solid var(--pub-border);
            padding: .62rem .72rem;
            vertical-align: top;
        }

        .rich-text-content th {
            background: #f1f5f9;
            font-weight: 600;
            text-align: left;
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
            margin: 0 8px 6px 0;
            font-size: .95rem;
            font-weight: 500;
            color: #334155;
            border: 1px solid #dbeafe;
            background: var(--pub-blue-soft);
            border-radius: 999px;
            min-height: 36px;
            padding: 5px 12px;
        }

        .public-required-note {
            color: #334155;
            font-size: .88rem;
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
        }

        @media (max-width: 900px) {
            .public-grid {
                grid-template-columns: 1fr;
            }

            .public-shell {
                width: min(1080px, calc(100% - 14px));
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<?php
$link = isset($link) && is_array($link) ? $link : [];
$aspects = isset($aspects) && is_array($aspects) ? $aspects : [];
$items = isset($items) && is_array($items) ? $items : [];
$scale = isset($scale) && is_array($scale) ? $scale : [];

$text = static function (array $row, string $key, string $default = '-'): string {
    $value = $row[$key] ?? $default;

    if (is_scalar($value)) {
        $value = (string) $value;
        return $value !== '' ? $value : $default;
    }

    return $default;
};

$scaleMin = isset($scale['min']) ? (int) $scale['min'] : (int) ($link['skala_min'] ?? 1);
$scaleMax = isset($scale['max']) ? (int) $scale['max'] : (int) ($link['skala_max'] ?? 4);
$rawScaleRange = $scale['range'] ?? range($scaleMin, $scaleMax);
$scaleRange = array_map(static fn($value): int => (int) $value, is_array($rawScaleRange) ? $rawScaleRange : []);
$pengantarPenyebaran = trim((string) ($link['pengantar_penyebaran'] ?? ''));
$pengantarMaster = trim((string) ($link['pengantar'] ?? ''));
$pengantar = $pengantarPenyebaran !== '' ? $pengantarPenyebaran : $pengantarMaster;
$petunjukPenyebaran = trim((string) ($link['petunjuk_penyebaran'] ?? ''));
$petunjukMaster = trim((string) ($link['petunjuk'] ?? ''));
$petunjuk = $petunjukPenyebaran !== '' ? $petunjukPenyebaran : $petunjukMaster;
$linkToken = $text($link, 'token', '');
?>

<div class="public-shell">
    <div class="public-card">
        <h1 class="public-title">Lembar Observasi</h1>
        <div class="public-muted"><?= esc($text($link, 'judul_link', '')) ?></div>
    </div>

    <div class="public-card">
        <h2 class="public-heading">Identitas Instrumen Observasi</h2>
        <div class="public-table-wrap">
            <table class="public-table">
                <tbody>
                    <tr>
                        <th style="width: 220px;">Kode dan Judul</th>
                        <td><strong><?= esc($text($link, 'kode', '-')) ?></strong> - <?= esc($text($link, 'judul', '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Sasaran</th>
                        <td><?= esc($text($link, 'sasaran', 'Observer')) ?></td>
                    </tr>
                    <tr>
                        <th>Status Link</th>
                        <td><?= esc(status_display_label($text($link, 'status', '-'))) ?></td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>
                            <?= !empty($link['tanggal_mulai']) ? esc(format_tanggal_indonesia($link['tanggal_mulai'])) : 'Tidak dibatasi' ?>
                            s.d.
                            <?= !empty($link['tanggal_selesai']) ? esc(format_tanggal_indonesia($link['tanggal_selesai'])) : 'Tidak dibatasi' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Kuota</th>
                        <td><?= !empty($link['maksimal_respon']) ? esc($text($link, 'maksimal_respon', '-')) . ' respon' : 'Tidak dibatasi' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <form action="<?= base_url('isi/' . $linkToken) ?>" method="post">
        <div class="public-card">
            <h2 class="public-heading">A. Identitas Observer</h2>
            <?= view('public/partials/respondent_identity_summary', compact('respondentIdentity', 'link', 'identityFields')) ?>
        </div>

        <div class="public-card">
            <h2 class="public-heading">B. Pengantar Observasi</h2>
            <div class="public-muted">
                <?= $pengantar !== '' ? render_rich_text_content($pengantar) : '-' ?>
            </div>
        </div>

        <div class="public-card">
            <h2 class="public-heading">C. Petunjuk Pengisian</h2>
            <div class="public-muted" style="margin-bottom: .7rem;">
                <?= $petunjuk !== '' ? render_rich_text_content($petunjuk) : '-' ?>
            </div>

        </div>

        <div class="public-card">
            <h2 class="public-heading">D. Rubrik Keterlaksanaan/Tampak</h2>

            <?php if (empty($items)): ?>
                <p class="public-muted">Butir observasi belum tersedia.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table class="public-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Indikator/Butir yang Diamati</th>
                                <th style="width: 260px;">Keterlaksanaan/Tampak</th>
                                <th style="width: 220px;">Catatan Observasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <?php
                                $aspectName = '-';
                                foreach ($aspects as $aspect) {
                                    if ((int) ($aspect['id'] ?? 0) === (int) ($item['aspect_id'] ?? 0)) {
                                        $aspectName = $aspect['nama_aspek'] ?? '-';
                                        break;
                                    }
                                }

                                $tipeButir = $item['tipe_butir'] ?? 'skala';
                                $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                                ?>
                                <tr>
                                    <td><?= esc((string) ($item['nomor'] ?? '-')) ?></td>
                                    <td>
                                        <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
                                        <br><small class="public-required-note">Aspek: <?= esc((string) $aspectName) ?></small>
                                        <br><small class="public-required-note"><?= (int) ($item['wajib'] ?? 1) === 1 ? 'Wajib diisi' : 'Opsional' ?></small>
                                    </td>
                                    <td>
                                        <?php if ($tipeButir === 'skala'): ?>
                                            <?php foreach ($scaleRange as $score): ?>
                                                <label class="public-score-option">
                                                    <input type="radio" name="answers[<?= $item['id'] ?>][skor]" value="<?= esc((string) $score) ?>" <?= $isRequired ?>>
                                                    <?= esc((string) $score) ?>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                class="public-textarea"
                                                placeholder="Tuliskan hasil observasi"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <textarea
                                            name="answers[<?= $item['id'] ?>][komentar]"
                                            class="public-textarea"
                                            placeholder="Catatan observasi per butir"
                                        ><?= old('answers.' . $item['id'] . '.komentar') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?= view('public/partials/justification_fields', compact('justificationConfig')) ?>

        <div class="public-card" style="text-align: right;">
            <button type="submit" class="public-btn">Kirim Hasil Observasi</button>
        </div>
    </form>
</div>

</body>
</html>


