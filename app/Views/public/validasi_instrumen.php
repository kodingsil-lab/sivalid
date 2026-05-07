<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Validasi Instrumen') ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .container {
            width: 960px;
            max-width: calc(100% - 30px);
            margin: 24px auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 24px;
            box-sizing: border-box;
        }

        h1, h2, h3 {
            margin-top: 0;
        }

        .muted {
            color: #666;
            font-size: 14px;
        }

        .section {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 9px;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: #f1f5f9;
        }

        .form-row {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            padding: 9px;
            border: 1px solid #bbb;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .radio-center {
            text-align: center;
        }

        .btn {
            padding: 10px 16px;
            border: 0;
            background: #1f4e79;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #ddd;
            padding: 12px;
            margin-top: 10px;
        }

        @media (max-width: 700px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .container {
                margin: 0;
                max-width: 100%;
                width: 100%;
                border: 0;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

<?php
$scaleMin = $scale['min'] ?? (int) ($link['skala_min'] ?? 1);
$scaleMax = $scale['max'] ?? (int) ($link['skala_max'] ?? 4);
$scaleRange = $scale['range'] ?? range($scaleMin, $scaleMax);
?>

<div class="container">
    <h1>Validasi Instrumen</h1>
    <p class="muted">
        <?= esc($link['judul_link']) ?>
    </p>

    <div class="info-box">
        <strong>Instrumen yang Divalidasi:</strong><br>
        <?= esc($link['kode']) ?> - <?= esc($link['judul']) ?><br>
        <span class="muted">
            Jenis: <?= esc($link['jenis']) ?> |
            Sasaran: <?= esc($link['instrument_sasaran'] ?: $link['sasaran'] ?: '-') ?>
        </span>
    </div>

    <div class="info-box">
        <strong>Informasi Pengisian:</strong><br>
        Status Link: <?= esc($link['status']) ?><br>
        Periode:
        <?= !empty($link['tanggal_mulai']) ? esc(date('d-m-Y', strtotime($link['tanggal_mulai']))) : 'Tidak dibatasi' ?>
        s.d.
        <?= !empty($link['tanggal_selesai']) ? esc(date('d-m-Y', strtotime($link['tanggal_selesai']))) : 'Tidak dibatasi' ?><br>
        Kuota:
        <?= !empty($link['maksimal_respon']) ? esc($link['maksimal_respon']) . ' respon' : 'Tidak dibatasi' ?>
    </div>

    <div class="section">
        <h2>A. Identitas Validator</h2>

        <form action="<?= base_url('isi/' . $link['token']) ?>" method="post">
            <?= csrf_field() ?>

            <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:#fdecea;color:#9f1c1c;border:1px solid #f5c2c0;padding:10px;margin-bottom:14px;">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div style="background:#fdecea;color:#9f1c1c;border:1px solid #f5c2c0;padding:10px;margin-bottom:14px;">
                    <strong>Periksa kembali input berikut:</strong>
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="grid">
                <div class="form-row">
                    <label for="nama">Nama Validator</label>
                    <input type="text" name="nama" id="nama" value="<?= old('nama') ?>" required>
                </div>

                <div class="form-row">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= old('email') ?>">
                </div>

                <div class="form-row">
                    <label for="bidang_keahlian">Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" id="bidang_keahlian" value="<?= old('bidang_keahlian') ?>">
                </div>

                <div class="form-row">
                    <label for="instansi">Instansi</label>
                    <input type="text" name="instansi" id="instansi" value="<?= old('instansi') ?>">
                </div>
            </div>

            <div class="section">
                <h2>B. Pengantar</h2>
                <p>
                    <?= nl2br(esc($link['pengantar'] ?: 'Pengantar instrumen belum diisi.')) ?>
                </p>
            </div>

            <div class="section">
                <h2>C. Petunjuk Pengisian</h2>
                <p>
                    <?= nl2br(esc($link['petunjuk'] ?: 'Petunjuk pengisian belum diisi.')) ?>
                </p>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 120px;">Skor</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scaleRange as $score): ?>
                            <tr>
                                <td><?= esc($score) ?></td>
                                <td>
                                    <?php if ($score === $scaleMin): ?>
                                        Tidak Relevan / Sangat Tidak Sesuai
                                    <?php elseif ($score === $scaleMax): ?>
                                        Sangat Relevan / Sangat Sesuai
                                    <?php else: ?>
                                        Tingkat penilaian <?= esc($score) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>D. Kisi-Kisi Instrumen</h2>

                <?php if (empty($aspects)): ?>
                    <p class="muted">Kisi-kisi belum tersedia.</p>
                <?php else: ?>
                    <table>
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
                                        <td><?= esc($aspect['nama_aspek']) ?></td>
                                        <td><em>Belum ada indikator.</em></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($aspectIndicators as $indicatorIndex => $indicator): ?>
                                        <tr>
                                            <?php if ($indicatorIndex === 0): ?>
                                                <td rowspan="<?= count($aspectIndicators) ?>"><?= $aspectIndex + 1 ?></td>
                                                <td rowspan="<?= count($aspectIndicators) ?>"><?= esc($aspect['nama_aspek']) ?></td>
                                            <?php endif; ?>

                                            <td><?= nl2br(esc($indicator['indikator'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>E. Instrumen yang Divalidasi</h2>

                <?php if (empty($items)): ?>
                    <p class="muted">Butir instrumen belum tersedia.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th style="width: 220px;">Aspek</th>
                                <th>Butir Pernyataan</th>
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
                                    <td><?= nl2br(esc($item['pernyataan'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>F. Lembar Validasi Instrumen</h2>
                <p class="muted">
                    Berikan penilaian terhadap relevansi setiap butir instrumen.
                </p>

                <?php if (empty($items)): ?>
                    <p class="muted">Belum ada butir yang dapat divalidasi.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 160px;">Aspek</th>
                                <th>Butir yang Dinilai</th>
                                <th style="width: 260px;">Jawaban</th>
                                <th style="width: 220px;">Komentar</th>
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

                                        <?php if ((int) ($item['wajib'] ?? 1) === 1): ?>
                                            <br><small class="muted">Wajib diisi</small>
                                        <?php else: ?>
                                            <br><small class="muted">Opsional</small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php
                                        $tipeButir = $item['tipe_butir'] ?? 'skala';
                                        $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                                        ?>

                                        <?php if ($tipeButir === 'skala'): ?>
                                            <?php foreach ($scaleRange as $score): ?>
                                                <label style="display:inline-block; margin-right:8px; font-weight:normal;">
                                                    <input
                                                        type="radio"
                                                        name="answers[<?= $item['id'] ?>][skor]"
                                                        value="<?= esc($score) ?>"
                                                        <?= $isRequired ?>
                                                    >
                                                    <?= esc($score) ?>
                                                </label>
                                            <?php endforeach; ?>

                                        <?php elseif ($tipeButir === 'isian'): ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                placeholder="Tuliskan jawaban"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>

                                        <?php elseif ($tipeButir === 'komentar'): ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                placeholder="Tuliskan komentar"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>

                                        <?php elseif ($tipeButir === 'catatan'): ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                placeholder="Tuliskan catatan"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>

                                        <?php elseif ($tipeButir === 'pilihan'): ?>
                                            <input
                                                type="text"
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                placeholder="Tuliskan pilihan/jawaban"
                                                value="<?= old('answers.' . $item['id'] . '.jawaban_teks') ?>"
                                                <?= $isRequired ?>
                                            >

                                            <small class="muted">
                                                Catatan: opsi pilihan khusus dapat ditambahkan pada tahap lanjutan.
                                            </small>

                                        <?php else: ?>
                                            <textarea
                                                name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                                placeholder="Tuliskan jawaban"
                                                <?= $isRequired ?>
                                            ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <textarea
                                            name="answers[<?= $item['id'] ?>][komentar]"
                                            placeholder="Komentar/saran perbaikan"
                                        ><?= old('answers.' . $item['id'] . '.komentar') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>G. Komentar Umum Validator</h2>
                <textarea name="komentar_umum" placeholder="Tuliskan komentar umum terhadap instrumen."><?= old('komentar_umum') ?></textarea>
            </div>

            <div class="section">
                <h2>H. Kesimpulan Validasi</h2>

                <div class="form-row">
                    <label>
                        <input type="radio" name="kesimpulan" value="Layak digunakan tanpa revisi" required>
                        Layak digunakan tanpa revisi
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        <input type="radio" name="kesimpulan" value="Layak digunakan dengan revisi kecil" required>
                        Layak digunakan dengan revisi kecil
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        <input type="radio" name="kesimpulan" value="Perlu revisi besar sebelum digunakan" required>
                        Perlu revisi besar sebelum digunakan
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        <input type="radio" name="kesimpulan" value="Tidak layak digunakan" required>
                        Tidak layak digunakan
                    </label>
                </div>
            </div>

            <div class="section">
                <button type="submit" class="btn">
                    Kirim Validasi
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
