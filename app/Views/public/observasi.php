<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Lembar Observasi') ?></title>
    <style>
        body { margin:0; font-family:Arial,sans-serif; background:#f4f6f8; color:#222; }
        .container { width:960px; max-width:calc(100% - 30px); margin:24px auto; background:#fff; border:1px solid #ddd; padding:24px; box-sizing:border-box; }
        h1,h2,h3 { margin-top:0; }
        .muted { color:#666; font-size:14px; }
        .section { margin-top:24px; padding-top:18px; border-top:1px solid #ddd; }
        .info-box { background:#f8fafc; border:1px solid #ddd; padding:12px; margin-top:10px; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th,td { border:1px solid #ddd; padding:9px; vertical-align:top; font-size:14px; }
        th { background:#f1f5f9; }
        .form-row { margin-bottom:14px; }
        label { display:block; margin-bottom:6px; font-weight:bold; font-size:14px; }
        input[type="text"], input[type="email"], input[type="date"], textarea {
            width:100%; padding:9px; border:1px solid #bbb; box-sizing:border-box; font-size:14px;
        }
        textarea { min-height:80px; resize:vertical; }
        .grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; }
        .radio-inline { display:inline-block; margin-right:8px; font-weight:normal; }
        .btn { padding:10px 16px; border:0; background:#1f4e79; color:#fff; cursor:pointer; font-size:14px; }
        .alert-error { background:#fdecea; color:#9f1c1c; border:1px solid #f5c2c0; padding:10px; margin-bottom:14px; }
        @media(max-width:700px){ .grid{grid-template-columns:1fr;} .container{margin:0;max-width:100%;width:100%;border:0;} table{display:block;overflow-x:auto;} }
    </style>
</head>
<body>

<?php
$scaleMin = $scale['min'] ?? (int) ($link['skala_min'] ?? 1);
$scaleMax = $scale['max'] ?? (int) ($link['skala_max'] ?? 4);
$scaleRange = $scale['range'] ?? range($scaleMin, $scaleMax);
?>

<div class="container">
    <h1>Lembar Observasi</h1>
    <p class="muted"><?= esc($link['judul_link']) ?></p>

    <div class="info-box">
        <strong>Instrumen Observasi:</strong><br>
        <?= esc($link['kode']) ?> - <?= esc($link['judul']) ?><br>
        <span class="muted">Sasaran: <?= esc($link['sasaran'] ?: 'Observer') ?></span>
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

    <form action="<?= base_url('isi/' . $link['token']) ?>" method="post">
        <?= csrf_field() ?>

        <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert-error">
                <strong>Periksa kembali input berikut:</strong>
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>A. Identitas Observer dan Kegiatan</h2>

            <div class="grid">
                <div class="form-row">
                    <label for="nama">Nama Observer</label>
                    <input type="text" name="nama" id="nama" value="<?= old('nama') ?>" required>
                </div>

                <div class="form-row">
                    <label for="instansi">Instansi/Unit</label>
                    <input type="text" name="instansi" id="instansi" value="<?= old('instansi') ?>">
                </div>

                <div class="form-row">
                    <label for="bidang_keahlian">Peran/Jabatan Observer</label>
                    <input type="text" name="bidang_keahlian" id="bidang_keahlian" value="<?= old('bidang_keahlian') ?>" placeholder="Contoh: Dosen pengamat, ahli pembelajaran, peneliti">
                </div>

                <div class="form-row">
                    <label for="program_studi">Program Studi/Konteks Kelas</label>
                    <input type="text" name="program_studi" id="program_studi" value="<?= old('program_studi') ?>">
                </div>

                <div class="form-row">
                    <label for="kelas">Kelas/Rombel yang Diobservasi</label>
                    <input type="text" name="kelas" id="kelas" value="<?= old('kelas') ?>">
                </div>

                <div class="form-row">
                    <label for="semester">Pertemuan/Semester</label>
                    <input type="text" name="semester" id="semester" value="<?= old('semester') ?>" placeholder="Contoh: Pertemuan 1 / Semester 4">
                </div>
            </div>
        </div>

        <div class="section">
            <h2>B. Pengantar Observasi</h2>
            <p>
                <?= nl2br(esc($link['pengantar'] ?: 'Lembar observasi ini digunakan untuk mencatat keterlaksanaan pembelajaran berdasarkan aspek dan indikator yang telah ditetapkan.')) ?>
            </p>
        </div>

        <div class="section">
            <h2>C. Petunjuk Pengisian</h2>
            <p>
                <?= nl2br(esc($link['petunjuk'] ?: 'Berikan penilaian sesuai kondisi yang diamati. Catatan observasi dapat ditambahkan pada setiap butir atau pada bagian catatan umum.')) ?>
            </p>

            <table>
                <tr>
                    <th style="width:120px;">Skor</th>
                    <th>Keterangan</th>
                </tr>
                <?php foreach ($scaleRange as $score): ?>
                    <tr>
                        <td><?= esc($score) ?></td>
                        <td>
                            <?php if ($score === $scaleMin): ?>
                                Tidak Terlaksana / Tidak Tampak
                            <?php elseif ($score === $scaleMax): ?>
                                Sangat Terlaksana / Sangat Tampak
                            <?php else: ?>
                                Tingkat keterlaksanaan <?= esc($score) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="section">
            <h2>D. Butir Observasi</h2>

            <?php if (empty($items)): ?>
                <p class="muted">Butir observasi belum tersedia.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="width:160px;">Aspek</th>
                            <th>Indikator/Butir yang Diamati</th>
                            <th style="width:260px;">Hasil Observasi</th>
                            <th style="width:220px;">Catatan Observer</th>
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

                            $tipeButir = $item['tipe_butir'] ?? 'skala';
                            $isRequired = (int) ($item['wajib'] ?? 1) === 1 ? 'required' : '';
                            ?>
                            <tr>
                                <td><?= esc($item['nomor']) ?></td>
                                <td><?= esc($aspectName) ?></td>
                                <td>
                                    <?= nl2br(esc($item['pernyataan'])) ?>
                                    <br>
                                    <small class="muted">
                                        <?= (int) ($item['wajib'] ?? 1) === 1 ? 'Wajib diisi' : 'Opsional' ?>
                                    </small>
                                </td>

                                <td>
                                    <?php if ($tipeButir === 'skala'): ?>
                                        <?php foreach ($scaleRange as $score): ?>
                                            <label class="radio-inline">
                                                <input
                                                    type="radio"
                                                    name="answers[<?= $item['id'] ?>][skor]"
                                                    value="<?= esc($score) ?>"
                                                    <?= $isRequired ?>
                                                >
                                                <?= esc($score) ?>
                                            </label>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <textarea
                                            name="answers[<?= $item['id'] ?>][jawaban_teks]"
                                            placeholder="Tuliskan hasil observasi"
                                            <?= $isRequired ?>
                                        ><?= old('answers.' . $item['id'] . '.jawaban_teks') ?></textarea>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <textarea
                                        name="answers[<?= $item['id'] ?>][komentar]"
                                        placeholder="Catatan khusus untuk butir ini"
                                    ><?= old('answers.' . $item['id'] . '.komentar') ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>E. Catatan Observasi Umum</h2>
            <textarea name="komentar_umum" placeholder="Tuliskan ringkasan temuan observasi, kejadian penting, atau catatan pelaksanaan pembelajaran."><?= old('komentar_umum') ?></textarea>
        </div>

        <div class="section">
            <button type="submit" class="btn">Kirim Hasil Observasi</button>
        </div>
    </form>
</div>

</body>
</html>
