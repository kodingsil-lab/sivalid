<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Validasi Produk') ?></title>
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
        @media (max-width: 600px) { .public-wrap { padding: 0 .5rem; } }
    </style>
</head>
<body>

<?php
$scaleMin = $scale['min'] ?? (int) ($link['skala_min'] ?? 1);
$scaleMax = $scale['max'] ?? (int) ($link['skala_max'] ?? 4);
$scaleRange = $scale['range'] ?? range($scaleMin, $scaleMax);
?>

<div class="public-wrap">
    <div class="page-header d-print-none mb-3 mt-2">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Validasi Produk</h2>
                <div class="text-muted mt-1"><?= esc($link['judul_link']) ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-2">Produk yang Divalidasi</h4>
                    <p class="mb-1"><strong><?= esc($link['product_kode'] ?? '-') ?></strong> â€” <?= esc($link['nama_produk'] ?? '-') ?></p>
                    <p class="text-muted small mb-1">Jenis: <?= esc(title_case_label((string) ($link['jenis_produk'] ?? '-'))) ?> | Status: <?= esc(status_display_label((string) ($link['product_status'] ?? ''))) ?></p>
                    <?php if (!empty($link['product_deskripsi'])): ?>
                        <p class="mb-1"><?= nl2br(esc($link['product_deskripsi'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($link['link_produk'])): ?>
                        <a href="<?= esc($link['link_produk']) ?>" target="_blank" class="btn btn-sm btn-light mt-1">Buka Produk</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-2">Instrumen &amp; Info Pengisian</h4>
                    <p class="mb-1"><strong><?= esc($link['kode']) ?></strong> â€” <?= esc($link['judul']) ?></p>
                    <p class="text-muted small mb-1">Jenis: <?= esc(title_case_label((string) ($link['jenis'] ?? '-'))) ?> | Sasaran: <?= esc($link['instrument_sasaran'] ?: $link['sasaran'] ?: '-') ?></p>
                    <hr class="my-2">
                    <p class="mb-1 small">Status: <strong><?= esc(status_display_label((string) ($link['status'] ?? ''))) ?></strong></p>
                    <p class="mb-1 small">
                        Periode:
                        <?= !empty($link['tanggal_mulai']) ? esc(format_tanggal_indonesia($link['tanggal_mulai'])) : 'Tidak dibatasi' ?>
                        s.d.
                        <?= !empty($link['tanggal_selesai']) ? esc(format_tanggal_indonesia($link['tanggal_selesai'])) : 'Tidak dibatasi' ?>
                    </p>
                    <p class="mb-0 small">Kuota: <?= !empty($link['maksimal_respon']) ? esc($link['maksimal_respon']) . ' respon' : 'Tidak dibatasi' ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">A. Identitas Validator</h3>
        </div>
        <div class="card-body">
            <form action="<?= base_url('isi/' . $link['token']) ?>" method="post">
                <?= csrf_field() ?>

                <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
                    <label for="website">Website</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

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

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="nama">Nama Validator</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= old('nama') ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="bidang_keahlian">Bidang Keahlian</label>
                        <input type="text" name="bidang_keahlian" id="bidang_keahlian" class="form-control" value="<?= old('bidang_keahlian') ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="instansi">Instansi</label>
                        <input type="text" name="instansi" id="instansi" class="form-control" value="<?= old('instansi') ?>">
                    </div>
                </div>

                <hr class="my-4">
                <h4>B. Pengantar</h4>
                <p><?= nl2br(esc($link['pengantar'] ?: 'Pengantar instrumen belum diisi.')) ?></p>

                <hr class="my-4">
                <h4>C. Petunjuk Pengisian</h4>
                <p><?= nl2br(esc($link['petunjuk'] ?: 'Petunjuk pengisian belum diisi.')) ?></p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Skor</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scaleRange as $score): ?>
                                <tr>
                                    <td><?= esc($score) ?></td>
                                    <td>
                                        <?php if ($score === $scaleMin): ?>
                                            Tidak Sesuai
                                        <?php elseif ($score === $scaleMax): ?>
                                            Sangat Sesuai
                                        <?php else: ?>
                                            Tingkat penilaian <?= esc($score) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
                <h4>D. Instrumen Penilaian Produk</h4>

                <?php if (empty($items)): ?>
                    <div class="empty-state">Butir instrumen belum tersedia.</div>
                <?php else: ?>
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
                                    <tr>
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
                                                    <?php foreach ($scaleRange as $score): ?>
                                                        <label>
                                                            <input type="radio" name="answers[<?= $item['id'] ?>][skor]" value="<?= esc($score) ?>" <?= $isRequired ?>>
                                                            <?= esc($score) ?>
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

                <hr class="my-4">
                <h4>E. Komentar/Saran Umum</h4>
                <textarea class="form-control mb-3" name="komentar_umum" rows="4" placeholder="Tuliskan komentar atau saran umum terhadap produk."><?= old('komentar_umum') ?></textarea>

                <hr class="my-4">
                <h4>F. Kesimpulan Validasi Produk</h4>
                <div class="row g-2 mb-3">
                    <?php foreach (['Sangat Layak', 'Layak', 'Kurang Layak', 'Tidak Layak'] as $kesimpulan): ?>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-check">
                                <input class="form-check-input" type="radio" name="kesimpulan" value="<?= esc($kesimpulan) ?>" required>
                                <span class="form-check-label"><?= esc($kesimpulan) ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

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
