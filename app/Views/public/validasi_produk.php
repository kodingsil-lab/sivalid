<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Validasi Produk') ?></title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sivalid.css') ?>">
    <style>
        body { background: var(--sv-bg, #f4f6f8); font-size: 16px; line-height: 1.6; }
        .public-wrap { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        .public-wrap .text-muted.small,
        .public-wrap .small { font-size: .92rem; }
        .public-wrap .form-label { font-size: 1rem; font-weight: 600; }
        .public-wrap .form-control,
        .public-wrap .form-select,
        .public-wrap .btn { font-size: 1rem; }
        .public-wrap .table { font-size: .95rem; line-height: 1.55; }
        .public-wrap .table > :not(caption) > * > * { padding: .68rem .72rem; }
        .radio-inline label { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; margin-right: 12px; padding: 4px 8px; font-weight: normal; }
        .instrument-info-table { width: 100%; border-collapse: collapse; border: 1px solid #dde6ef; background: #fff; font-size: .95rem; }
        .instrument-info-table th, .instrument-info-table td { border: 1px solid #dde6ef; padding: .62rem .72rem; vertical-align: top; }
        .instrument-info-table th { width: 210px; background: #f1f5f9; color: #1f2a3d; font-weight: 680; text-align: left; }
        @media (max-width: 600px) { .public-wrap { padding: 0 .5rem; } }
    </style>
</head>
<body>

<?php
$scaleMin = $scale['min'] ?? (int) ($link['skala_min'] ?? 1);
$scaleMax = $scale['max'] ?? (int) ($link['skala_max'] ?? 4);
$scaleRange = $scale['range'] ?? range($scaleMin, $scaleMax);
$scaleOptions = isset($scale['options']) && is_array($scale['options'])
    ? $scale['options']
    : sivalid_scale_options(['skala_min' => (int) $scaleMin, 'skala_max' => (int) $scaleMax] + $link);
?>

<div class="public-wrap">
    <div class="page-header d-print-none mb-3 mt-2">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?= esc($link['judul'] ?? ($link['judul_link'] ?? 'Validasi Produk')) ?></h2>
                <div class="text-muted mt-1"><?= esc($link['judul_link'] ?? 'Validasi Produk') ?></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Identitas</h3>
        </div>
        <div class="card-body">
            <form action="<?= base_url('isi/' . $link['token']) ?>" method="post">
                <?= view('public/partials/respondent_identity_summary', compact('respondentIdentity', 'link', 'identityFields')) ?>

                <hr class="my-4">
                <h4>Informasi Instrumen</h4>
                <table class="instrument-info-table mb-3">
                    <tbody>
                        <tr>
                            <th>Kode Instrumen</th>
                            <td><?= esc($link['kode'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Judul Instrumen</th>
                            <td><?= esc($link['judul'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?= esc(title_case_label((string) ($link['jenis'] ?? '-'))) ?></td>
                        </tr>
                        <tr>
                            <th>Sasaran</th>
                            <td><?= esc($link['instrument_sasaran'] ?: $link['sasaran'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Produk</th>
                            <td>
                                <strong><?= esc($link['product_kode'] ?? '-') ?></strong> - <?= esc($link['nama_produk'] ?? '-') ?>
                                <?php if (!empty($link['link_produk'])): ?>
                                    <div class="mt-1"><a href="<?= esc($link['link_produk']) ?>" target="_blank">Buka Produk</a></div>
                                <?php endif; ?>
                            </td>
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

                <h4>Petunjuk Pengisian</h4>
                <div><?= render_rich_text_content($link['petunjuk_penyebaran'] ?: ($link['petunjuk'] ?: '-')) ?></div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Skor</th>
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
                                    <td><?= esc($score) ?></td>
                                    <td><?= esc($label) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
                <h4>Butir Instrumen</h4>

                <?php if (empty($items)): ?>
                    <div class="empty-state">Butir instrumen belum tersedia.</div>
                <?php else: ?>
                    <?= view('public/partials/fill_progress') ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 150px;">Aspek</th>
                                    <th>Butir Pernyataan</th>
                                    <th style="width: 240px;">Jawaban</th>
                                    <th style="width: 200px;">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
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
                                        <td><?= esc($item['nomor']) ?></td>
                                        <td><?= esc($aspectName) ?></td>
                                        <td>
                                            <?= nl2br(esc($item['pernyataan'])) ?>
                                            <br><small class="text-muted"><?= (int) ($item['wajib'] ?? 1) === 1 ? 'Wajib diisi' : 'Opsional' ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $tipeButir = $item['tipe_butir'] ?? 'skala';
                                            $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                                            ?>
                                            <?php if ($tipeButir === 'skala'): ?>
                                                <div class="radio-inline">
                                                    <?php foreach ($scaleOptions as $option): ?>
                                                        <?php
                                                        $score = (int) ($option['score'] ?? 0);
                                                        $label = (string) ($option['label'] ?? ('Skor ' . $score));
                                                        ?>
                                                        <label>
                                                            <input type="radio" name="answers[<?= $item['id'] ?>][skor]" value="<?= esc($score) ?>" <?= $isRequired ?>>
                                                            <?= esc($label) ?>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <textarea class="form-control form-control-sm" name="answers[<?= $item['id'] ?>][jawaban_teks]" placeholder="Tuliskan jawaban" <?= $isRequired ?>><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="answers[<?= $item['id'] ?>][komentar]" placeholder="Komentar/saran"><?= old('answers.' . $item['id'] . '.komentar') ?></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?= view('public/partials/justification_fields', compact('justificationConfig')) ?>

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary">
                        Kirim Validasi Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/vendor/tabler/js/tabler.min.js') ?>"></script>
</body>
</html>
