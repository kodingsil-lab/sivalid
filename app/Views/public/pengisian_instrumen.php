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

        .items-table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
            min-height: 42px;
            min-width: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            padding: .25rem .48rem;
            background: #fff;
            color: var(--pub-text);
            font-weight: 600;
            cursor: pointer;
        }

        .score-option input {
            margin: 0;
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
            font-size: .9rem;
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
            border-bottom: 1px solid var(--pub-border-soft);
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
$scaleLabels = isset($scale['labels']) && is_array($scale['labels']) ? $scale['labels'] : sivalid_scale_labels(['skala_min' => $scaleMin, 'skala_max' => $scaleMax] + $link);
$petunjukPenyebaran = trim((string) ($link['petunjuk_penyebaran'] ?? ''));
$petunjukMaster = trim((string) ($link['petunjuk'] ?? ''));
$petunjuk = $petunjukPenyebaran !== '' ? $petunjukPenyebaran : $petunjukMaster;
$jenisInstrumen = title_case_label((string) ($link['jenis'] ?? 'Instrumen'));
$previewLayout = instrument_preview_layout($link['jenis'] ?? '');
$layoutType = (string) ($previewLayout['type'] ?? 'standard');
$usesDocumentReview = $layoutType === 'document_review';
$usesInterview = $layoutType === 'interview_guide';
$usesObservation = $layoutType === 'observation_guide';
$usesRubric = $layoutType === 'rubric_assessment';
$usesQuestionnaire = in_array($layoutType, ['questionnaire', 'product_validation_questionnaire', 'user_response_questionnaire'], true);
$usesPerformanceTest = $layoutType === 'performance_test';
$attachments = isset($attachments) && is_array($attachments) ? $attachments : [];
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
                    <?php if (!empty($attachments)): ?>
                        <tr>
                            <th>Lampiran</th>
                            <td>
                                <div class="attachment-actions">
                                    <?php foreach ($attachments as $attachmentIndex => $attachment): ?>
                                        <?php $attachmentTitle = trim((string) ($attachment['title'] ?? 'Lampiran Instrumen')); ?>
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

            <?php if ($petunjuk !== ''): ?>
                <h2 class="section-title">Petunjuk Pengisian</h2>
                <div class="section-intro">
                    <?= render_rich_text_content($petunjuk) ?>
                </div>
            <?php endif; ?>

            <h2 class="section-title">Butir Instrumen</h2>
            <?php if (empty($items)): ?>
                <p class="section-intro">Butir instrumen belum tersedia.</p>
            <?php else: ?>
                <?= view('public/partials/fill_progress') ?>
                <div class="items-table-wrap">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 76px;">No. Butir</th>
                                <th><?= esc((string) ($previewLayout['item'] ?? 'Butir Pernyataan')) ?></th>
                                <?php if ($usesDocumentReview): ?>
                                    <th style="width: 150px;">Sumber Dokumen</th>
                                    <th style="width: 230px;">Skor</th>
                                    <th style="width: 220px;">Komentar</th>
                                <?php elseif ($usesRubric): ?>
                                    <?php foreach (range(1, 5) as $score): ?>
                                        <th style="width: 190px;">Skor <?= $score ?></th>
                                    <?php endforeach; ?>
                                    <th style="width: 230px;">Skor yang Diperoleh</th>
                                    <th style="width: 220px;">Catatan</th>
                                <?php elseif ($usesInterview): ?>
                                    <th style="width: 300px;"><?= esc((string) ($previewLayout['answer'] ?? 'Jawaban')) ?></th>
                                <?php elseif ($usesObservation): ?>
                                    <th style="width: 300px;"><?= esc((string) ($previewLayout['result'] ?? 'Hasil Pengamatan')) ?></th>
                                <?php else: ?>
                                    <th style="width: 250px;"><?= $usesQuestionnaire || $usesPerformanceTest ? 'Skor' : 'Jawaban' ?></th>
                                    <?php if ($usesPerformanceTest): ?>
                                        <th style="width: 220px;">Catatan</th>
                                    <?php endif; ?>
                                <?php endif; ?>
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
                                $tipeButir = $item['tipe_butir'] ?? 'skala';
                                $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                                $renderScoreInput = static function (array $scaleRange, array $item, string $isRequired, array $scaleLabels): string {
                                    ob_start();
                                    ?>
                                    <div class="score-options">
                                        <?php foreach ($scaleRange as $score): ?>
                                            <?php
                                            $score = (int) $score;
                                            $scoreLabel = (string) ($scaleLabels[$score] ?? ('Skor ' . $score));
                                            $shortLabel = sivalid_scale_short_label($scoreLabel, $score);
                                            ?>
                                            <label class="score-option">
                                                <input type="radio" name="answers[<?= $item['id'] ?>][skor]" value="<?= esc((string) $score) ?>" <?= $isRequired ?>>
                                                <span title="<?= esc($scoreLabel, 'attr') ?>"><?= esc($shortLabel) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php
                                    return (string) ob_get_clean();
                                };
                                $renderTextInput = static function (array $item, string $isRequired, string $placeholder): string {
                                    return '<textarea class="text-answer" name="answers[' . esc((string) $item['id'], 'attr') . '][jawaban_teks]" placeholder="' . esc($placeholder, 'attr') . '" ' . $isRequired . '>' . esc(old('answers.' . $item['id'] . '.jawaban_teks')) . '</textarea>';
                                };
                                ?>
                                <tr class="instrument-item-row">
                                    <td><span class="item-number"><?= esc((string) $butirNo++) ?></span></td>
                                    <td>
                                        <?= nl2br(esc($item['pernyataan'])) ?>
                                        <br><small class="text-muted">Aspek: <?= esc($aspectName) ?></small>
                                        <span class="item-required"><?= (int) ($item['wajib'] ?? 1) === 1 ? 'Wajib diisi' : 'Opsional' ?></span>
                                    </td>

                                    <?php if ($usesDocumentReview): ?>
                                        <td><?= esc(document_review_source_label($item['sumber_dokumen'] ?? '')) ?></td>
                                        <td><?= $renderScoreInput($scaleRange, $item, $isRequired, $scaleLabels) ?></td>
                                        <td>
                                            <textarea class="text-answer" name="answers[<?= $item['id'] ?>][komentar]" placeholder="Tuliskan komentar"><?= esc(old('answers.' . $item['id'] . '.komentar')) ?></textarea>
                                        </td>
                                    <?php elseif ($usesRubric): ?>
                                        <?php foreach (range(1, 5) as $score): ?>
                                            <td><?= nl2br(esc((string) ($item['skor_' . $score . '_deskripsi'] ?? '-'))) ?></td>
                                        <?php endforeach; ?>
                                        <td><?= $renderScoreInput($scaleRange, $item, $isRequired, $scaleLabels) ?></td>
                                        <td>
                                            <textarea class="text-answer" name="answers[<?= $item['id'] ?>][komentar]" placeholder="Tuliskan catatan"><?= esc(old('answers.' . $item['id'] . '.komentar')) ?></textarea>
                                        </td>
                                    <?php elseif ($usesInterview): ?>
                                        <td><?= $renderTextInput($item, $isRequired, 'Tuliskan jawaban wawancara') ?></td>
                                    <?php elseif ($usesObservation): ?>
                                        <td><?= $renderTextInput($item, $isRequired, 'Tuliskan hasil pengamatan') ?></td>
                                    <?php else: ?>
                                        <td>
                                            <?php if ($usesQuestionnaire || $usesPerformanceTest || $tipeButir === 'skala'): ?>
                                                <?= $renderScoreInput($scaleRange, $item, $isRequired, $scaleLabels) ?>
                                            <?php else: ?>
                                                <?= $renderTextInput($item, $isRequired, 'Tuliskan jawaban') ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($usesPerformanceTest): ?>
                                            <td>
                                                <textarea class="text-answer" name="answers[<?= $item['id'] ?>][komentar]" placeholder="Tuliskan catatan"><?= esc(old('answers.' . $item['id'] . '.komentar')) ?></textarea>
                                            </td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?= view('public/partials/justification_fields', compact('justificationConfig')) ?>

            <div class="form-actions">
                <button type="submit" class="pub-btn">Kirim Pengisian</button>
            </div>
        </form>
    </div>
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
