<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Validasi Instrumen') ?></title>
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
        .public-textarea,
        .public-select {
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
        .public-textarea:focus,
        .public-select:focus {
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

        .attachment-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .attachment-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: .42rem .82rem;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            background: #eff6ff;
            color: var(--pub-blue);
            cursor: pointer;
            font-size: .92rem;
            font-weight: 650;
            line-height: 1.2;
        }

        .attachment-btn:hover,
        .attachment-btn:focus {
            border-color: var(--pub-blue);
            background: #dbeafe;
            outline: none;
        }

        .public-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, .46);
        }

        .public-modal-backdrop.show {
            display: flex;
        }

        .public-modal {
            width: min(940px, 100%);
            height: min(86vh, 780px);
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
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--pub-border);
        }

        .public-modal-title {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 720;
        }

        .public-modal-close {
            width: 38px;
            height: 38px;
            padding: 0;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            background: #94a3b8;
            color: #ffffff;
            cursor: pointer;
        }

        .public-modal-body {
            flex: 1;
            min-height: 0;
            background: #eef2f6;
        }

        .pdf-frame {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            background: #eef2f6;
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
$indicators = isset($indicators) && is_array($indicators) ? $indicators : [];
$items = isset($items) && is_array($items) ? $items : [];
$scale = isset($scale) && is_array($scale) ? $scale : [];
$attachments = isset($attachments) && is_array($attachments) ? $attachments : [];

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
$scaleOptions = isset($scale['options']) && is_array($scale['options'])
    ? $scale['options']
    : sivalid_scale_options(['skala_min' => $scaleMin, 'skala_max' => $scaleMax] + $link);
$linkToken = $text($link, 'token', '');
?>

<div class="public-shell">
    <div class="public-card">
        <h1 class="public-title"><?= esc($text($link, 'judul', 'Validasi Instrumen')) ?></h1>
        <div class="public-muted"><?= esc($text($link, 'judul_link', '')) ?></div>
    </div>

    <form action="<?= base_url('isi/' . $linkToken) ?>" method="post">
        <div class="public-card">
            <h2 class="public-heading">Identitas</h2>
            <?= view('public/partials/respondent_identity_summary', compact('respondentIdentity', 'link', 'identityFields')) ?>
        </div>

        <div class="public-card">
            <h2 class="public-heading">Informasi Instrumen</h2>
            <div class="public-table-wrap">
                <table class="public-table">
                    <tbody>
                        <tr>
                            <th style="width: 220px;">Kode dan Judul</th>
                            <td><strong><?= esc($text($link, 'kode')) ?></strong> - <?= esc($text($link, 'judul')) ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?= esc(title_case_label($text($link, 'jenis'))) ?></td>
                        </tr>
                        <tr>
                            <th>Sasaran</th>
                            <td><?= esc($text($link, 'instrument_sasaran', $text($link, 'sasaran'))) ?></td>
                        </tr>
                        <tr>
                            <th>Status Link</th>
                            <td><?= esc(status_display_label($text($link, 'status'))) ?></td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>
                                <?= !empty($link['tanggal_mulai']) ? esc(format_tanggal_indonesia($link['tanggal_mulai'])) : 'Tidak dibatasi' ?>
                                s.d.
                                <?= !empty($link['tanggal_selesai']) ? esc(format_tanggal_indonesia($link['tanggal_selesai'])) : 'Tidak dibatasi' ?>
                            </td>
                        </tr>
                        <?php if (!empty($attachments)): ?>
                            <tr>
                                <th>Lampiran</th>
                                <td>
                                    <div class="attachment-actions">
                                        <?php foreach ($attachments as $attachmentIndex => $attachment): ?>
                                            <?php
                                            $attachmentTitle = trim((string) ($attachment['title'] ?? 'Lampiran Instrumen'));
                                            ?>
                                            <button
                                                type="button"
                                                class="attachment-btn"
                                                data-open-pdf-modal="instrument-attachment-<?= esc((string) $attachmentIndex, 'attr') ?>"
                                            >
                                                <?= esc($attachmentTitle !== '' ? $attachmentTitle : 'Lampiran Instrumen') ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="public-card">
            <h2 class="public-heading">Petunjuk Pengisian</h2>
            <div class="public-muted" style="margin-bottom: .7rem;">
                <?= render_rich_text_content($text($link, 'petunjuk_penyebaran', $text($link, 'petunjuk', '-'))) ?>
            </div>

            <div class="public-table-wrap">
                <table class="public-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Skor</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scaleOptions as $option): ?>
                            <?php
                            $score = (int) ($option['score'] ?? 0);
                            $label = (string) ($option['label'] ?? ('Skor ' . $score));
                            ?>
                            <tr>
                                <td><?= esc((string) $score) ?></td>
                                <td><?= esc($label) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="public-card">
            <h2 class="public-heading">Kisi-Kisi Instrumen</h2>

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
                                $aspectIndicators = array_values(array_filter($indicators, function ($indicator) use ($aspect) {
                                    return (int) $indicator['aspect_id'] === (int) $aspect['id'];
                                }));
                                ?>

                                <?php if (empty($aspectIndicators)): ?>
                                    <tr>
                                        <td><?= $aspectIndex + 1 ?></td>
                                        <td><?= esc((string) ($aspect['nama_aspek'] ?? '-')) ?></td>
                                        <td><em>Belum ada indikator.</em></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($aspectIndicators as $indicatorIndex => $indicator): ?>
                                        <tr>
                                            <?php if ($indicatorIndex === 0): ?>
                                                <td rowspan="<?= count($aspectIndicators) ?>"><?= $aspectIndex + 1 ?></td>
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

        <div class="public-card">
            <h2 class="public-heading">Instrumen yang Divalidasi</h2>

            <?php if (empty($items)): ?>
                <p class="public-muted">Butir instrumen belum tersedia.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table class="public-table">
                        <thead>
                            <tr>
                                <th style="width: 76px;">No. Butir</th>
                                <th>Butir Pernyataan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $previewButirNo = 1; foreach ($items as $item): ?>
                                <?php
                                $aspectName = '-';

                                foreach ($aspects as $aspect) {
                                    if ((int) $aspect['id'] === (int) $item['aspect_id']) {
                                        $aspectName = $aspect['nama_aspek'];
                                        break;
                                    }
                                }
                                ?>
                                <tr>
                                    <td><?= esc((string) $previewButirNo++) ?></td>
                                    <td>
                                        <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
                                        <br><small class="public-required-note">Aspek: <?= esc((string) $aspectName) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="public-card">
            <h2 class="public-heading">Butir Instrumen</h2>
            <p class="public-muted">Berikan penilaian terhadap relevansi setiap butir instrumen.</p>

            <?php if (empty($items)): ?>
                <p class="public-muted">Belum ada butir yang dapat divalidasi.</p>
            <?php else: ?>
                <?= view('public/partials/fill_progress') ?>
                <div class="public-table-wrap">
                    <table class="public-table">
                        <thead>
                            <tr>
                                <th style="width: 76px;">No. Butir</th>
                                <th>Butir yang Dinilai</th>
                                <th style="width: 260px;">Jawaban</th>
                                <th style="width: 220px;">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $butirNo = 1; foreach ($items as $item): ?>
                                <?php
                                $aspectName = '-';

                                foreach ($aspects as $aspect) {
                                    if ((int) $aspect['id'] === (int) $item['aspect_id']) {
                                        $aspectName = $aspect['nama_aspek'];
                                        break;
                                    }
                                }
                                ?>
                                <tr class="instrument-item-row">
                                    <td><?= esc((string) $butirNo++) ?></td>
                                    <td>
                                        <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
                                        <br><small class="public-required-note">Aspek: <?= esc((string) $aspectName) ?></small>

                                        <?php if ((int) ($item['wajib'] ?? 1) === 1): ?>
                                            <br><small class="public-required-note">Wajib diisi</small>
                                        <?php else: ?>
                                            <br><small class="public-required-note">Opsional</small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php
                                        $tipeButir = $item['tipe_butir'] ?? 'skala';
                                        $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                                        ?>

                                        <?php if ($tipeButir === 'skala'): ?>
                                            <?php foreach ($scaleOptions as $option): ?>
                                                <?php
                                                $score = (int) ($option['score'] ?? 0);
                                                $label = (string) ($option['label'] ?? ('Skor ' . $score));
                                                $shortLabel = sivalid_scale_short_label($label, $score);
                                                ?>
                                                <label class="public-score-option" title="<?= esc($label, 'attr') ?>">
                                                    <input
                                                        type="radio"
                                                        name="answers[<?= $item['id'] ?>][skor]"
                                                        value="<?= esc((string) $score) ?>"
                                                        <?= $isRequired ?>
                                                    >
                                                    <?= esc($shortLabel) ?>
                                                </label>
                                            <?php endforeach; ?>

                                        <?php elseif ($tipeButir === 'isian'): ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                class="public-textarea"
                                                placeholder="Tuliskan jawaban"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>

                                        <?php elseif ($tipeButir === 'komentar'): ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                class="public-textarea"
                                                placeholder="Tuliskan komentar"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>

                                        <?php elseif ($tipeButir === 'catatan'): ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                class="public-textarea"
                                                placeholder="Tuliskan catatan"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>

                                        <?php elseif ($tipeButir === 'pilihan'): ?>
                                            <input
                                                type="text"
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                class="public-input"
                                                placeholder="Tuliskan pilihan/jawaban"
                                                value="<?= old('answers.' . $item['id'] . '.jawaban_teks') ?>"
                                                <?= $isRequired ?>
                                            >

                                            <small class="public-muted">
                                                Catatan: opsi pilihan khusus dapat ditambahkan pada tahap lanjutan.
                                            </small>

                                        <?php else: ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                class="public-textarea"
                                                placeholder="Tuliskan jawaban"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <textarea
                                            name="answers[<?= $item['id'] ?>][komentar]"
                                            class="public-textarea"
                                            placeholder="Komentar/saran perbaikan"
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
            <button type="submit" class="public-btn">Kirim Validasi</button>
        </div>
    </form>
</div>

<?php foreach ($attachments as $attachmentIndex => $attachment): ?>
    <?php
    $attachmentTitle = trim((string) ($attachment['title'] ?? 'Lampiran Instrumen'));
    $attachmentUrl = base_url((string) ($attachment['file_path'] ?? ''));
    $attachmentViewerUrl = $attachmentUrl . '#toolbar=0&navpanes=0&scrollbar=1&view=FitH';
    ?>
    <div id="instrument-attachment-<?= esc((string) $attachmentIndex, 'attr') ?>" class="public-modal-backdrop" aria-hidden="true">
        <div class="public-modal" role="dialog" aria-modal="true" aria-labelledby="instrument-attachment-title-<?= esc((string) $attachmentIndex, 'attr') ?>">
            <div class="public-modal-head">
                <h2 id="instrument-attachment-title-<?= esc((string) $attachmentIndex, 'attr') ?>" class="public-modal-title">
                    <?= esc($attachmentTitle !== '' ? $attachmentTitle : 'Lampiran Instrumen') ?>
                </h2>
                <button type="button" class="public-modal-close" data-close-modal aria-label="Tutup">x</button>
            </div>
            <div class="public-modal-body">
                <iframe class="pdf-frame" src="<?= esc($attachmentViewerUrl) ?>" title="<?= esc($attachmentTitle !== '' ? $attachmentTitle : 'Lampiran Instrumen', 'attr') ?>"></iframe>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var activeModal = null;

        function openModal(modal) {
            if (!modal) return;
            activeModal = modal;
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            var closeButton = modal.querySelector('[data-close-modal]');
            if (closeButton) closeButton.focus();
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (activeModal === modal) activeModal = null;
        }

        document.querySelectorAll('[data-open-pdf-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                openModal(document.getElementById(button.getAttribute('data-open-pdf-modal')));
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.public-modal-backdrop'));
            });
        });

        document.querySelectorAll('.public-modal-backdrop').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && activeModal) {
                closeModal(activeModal);
            }
        });
    });
</script>

</body>
</html>
