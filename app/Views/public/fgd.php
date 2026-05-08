<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Lembar FGD') ?></title>
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
            font-size: 1.65rem;
            font-weight: 700;
        }

        .public-muted {
            color: var(--pub-muted);
            font-size: .92rem;
        }

        .public-heading {
            margin: 0 0 .75rem;
            font-size: 1.06rem;
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
            font-size: .9rem;
            font-weight: 600;
            color: #334155;
        }

        .public-input,
        .public-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: .5rem .62rem;
            font-size: .92rem;
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
            min-height: 90px;
            resize: vertical;
        }

        .public-table-wrap {
            overflow-x: auto;
        }

        .public-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
            background: #fff;
        }

        .public-table th,
        .public-table td {
            border: 1px solid var(--pub-border);
            padding: .55rem .6rem;
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
            font-size: .86rem;
            font-weight: 500;
            color: #334155;
            border: 1px solid #dbeafe;
            background: var(--pub-blue-soft);
            border-radius: 999px;
            padding: 2px 9px;
        }

        .public-required-note {
            color: #334155;
            font-size: .78rem;
        }

        .public-alert {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
            padding: .72rem .85rem;
            border-radius: 6px;
            margin-bottom: .9rem;
            font-size: .9rem;
        }

        .public-btn {
            display: inline-block;
            border: 1px solid var(--pub-blue);
            background: var(--pub-blue);
            color: #fff;
            padding: .6rem 1.05rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: .92rem;
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
$linkToken = $text($link, 'token', '');
?>

<div class="public-shell">
    <div class="public-card">
        <h1 class="public-title">Lembar Masukan FGD</h1>
        <div class="public-muted"><?= esc($text($link, 'judul_link', '')) ?></div>
    </div>

    <div class="public-card">
        <h2 class="public-heading">Identitas Instrumen FGD</h2>
        <div class="public-table-wrap">
            <table class="public-table">
                <tbody>
                    <tr>
                        <th style="width: 220px;">Kode dan Judul</th>
                        <td><strong><?= esc($text($link, 'kode', '-')) ?></strong> - <?= esc($text($link, 'judul', '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Sasaran</th>
                        <td><?= esc($text($link, 'sasaran', 'Peserta FGD')) ?></td>
                    </tr>
                    <tr>
                        <th>Status Link</th>
                        <td><?= esc($text($link, 'status', '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>
                            <?= !empty($link['tanggal_mulai']) ? esc(date('d-m-Y', strtotime($link['tanggal_mulai']))) : 'Tidak dibatasi' ?>
                            s.d.
                            <?= !empty($link['tanggal_selesai']) ? esc(date('d-m-Y', strtotime($link['tanggal_selesai']))) : 'Tidak dibatasi' ?>
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
            <h2 class="public-heading">A. Identitas Peserta FGD</h2>
            <?= csrf_field() ?>

            <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="public-alert"><?= esc((string) session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="public-alert">
                    <strong>Periksa kembali input berikut:</strong>
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc((string) $error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="public-grid">
                <div class="public-form-row">
                    <label for="nama" class="public-label">Nama Peserta FGD</label>
                    <input type="text" name="nama" id="nama" class="public-input" value="<?= old('nama') ?>" required>
                </div>

                <div class="public-form-row">
                    <label for="email" class="public-label">Email</label>
                    <input type="email" name="email" id="email" class="public-input" value="<?= old('email') ?>">
                </div>

                <div class="public-form-row">
                    <label for="instansi" class="public-label">Instansi</label>
                    <input type="text" name="instansi" id="instansi" class="public-input" value="<?= old('instansi') ?>">
                </div>

                <div class="public-form-row">
                    <label for="bidang_keahlian" class="public-label">Jabatan/Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" id="bidang_keahlian" class="public-input" value="<?= old('bidang_keahlian') ?>" placeholder="Contoh: Ahli pembelajaran, dosen, praktisi, pengguna produk">
                </div>

                <div class="public-form-row">
                    <label for="program_studi" class="public-label">Konteks/Program Studi</label>
                    <input type="text" name="program_studi" id="program_studi" class="public-input" value="<?= old('program_studi') ?>">
                </div>

                <div class="public-form-row">
                    <label for="kelas" class="public-label">Kelompok/Sesi FGD</label>
                    <input type="text" name="kelas" id="kelas" class="public-input" value="<?= old('kelas') ?>" placeholder="Contoh: FGD Produk Buku Ajar / FGD Ahli">
                </div>
            </div>
        </div>

        <div class="public-card">
            <h2 class="public-heading">B. Pengantar FGD</h2>
            <div class="public-muted"><?= nl2br(esc($text($link, 'pengantar', 'Form ini digunakan untuk menghimpun masukan, catatan, dan rekomendasi peserta FGD terhadap instrumen atau produk yang sedang dikembangkan.'))) ?></div>
        </div>

        <div class="public-card">
            <h2 class="public-heading">C. Petunjuk Pengisian</h2>
            <div class="public-muted" style="margin-bottom: .7rem;">
                <?= nl2br(esc($text($link, 'petunjuk', 'Berikan tanggapan, catatan, atau rekomendasi secara jelas sesuai pengalaman dan keahlian Bapak/Ibu. Jika tersedia butir skala, pilih skor yang paling sesuai.'))) ?>
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
                        <?php foreach ($scaleRange as $score): ?>
                            <tr>
                                <td><?= esc((string) $score) ?></td>
                                <td>
                                    <?php if ($score === $scaleMin): ?>
                                        Sangat Tidak Setuju / Sangat Kurang Relevan
                                    <?php elseif ($score === $scaleMax): ?>
                                        Sangat Setuju / Sangat Relevan
                                    <?php else: ?>
                                        Tingkat tanggapan <?= esc((string) $score) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="public-card">
            <h2 class="public-heading">D. Tanggapan dan Rekomendasi</h2>

            <?php if (empty($items)): ?>
                <p class="public-muted">Butir FGD belum tersedia.</p>
            <?php else: ?>
                <div class="public-table-wrap">
                    <table class="public-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 160px;">Aspek</th>
                                <th>Topik/Butir Diskusi</th>
                                <th style="width: 300px;">Tanggapan</th>
                                <th style="width: 220px;">Rekomendasi</th>
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

                                $tipeButir = $item['tipe_butir'] ?? 'komentar';
                                $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                                ?>
                                <tr>
                                    <td><?= esc((string) ($item['nomor'] ?? '-')) ?></td>
                                    <td><?= esc((string) $aspectName) ?></td>
                                    <td>
                                        <?= nl2br(esc((string) ($item['pernyataan'] ?? '-'))) ?>
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
                                                placeholder="Tuliskan tanggapan"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <textarea
                                            name="answers[<?= $item['id'] ?>][komentar]"
                                            class="public-textarea"
                                            placeholder="Tuliskan rekomendasi"
                                        ><?= old('answers.' . $item['id'] . '.komentar') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="public-card">
            <h2 class="public-heading">E. Rekomendasi Umum FGD</h2>
            <textarea class="public-textarea" name="komentar_umum" placeholder="Tuliskan kesimpulan umum, masukan utama, atau rekomendasi FGD."><?= old('komentar_umum') ?></textarea>
        </div>

        <div class="public-card" style="text-align: right;">
            <button type="submit" class="public-btn">Kirim Masukan FGD</button>
        </div>
    </form>
</div>

</body>
</html>


