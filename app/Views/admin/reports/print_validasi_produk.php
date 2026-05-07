<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Cetak Laporan Validasi Produk') ?></title>

    <style>
        @page {
            size: A4;
            margin: 18mm 15mm 18mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.45;
            color: #111;
            background: <?= empty($isPdf) ? '#e5e7eb' : '#fff' ?>;
        }

        .print-toolbar {
            width: 210mm;
            margin: 16px auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            font-family: Arial, sans-serif;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #333;
            background: #f8fafc;
            color: #111;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary {
            background: #1f4e79;
            border-color: #1f4e79;
            color: #fff;
        }

        .page {
            width: <?= empty($isPdf) ? '210mm' : 'auto' ?>;
            min-height: <?= empty($isPdf) ? '297mm' : 'auto' ?>;
            margin: <?= empty($isPdf) ? '0 auto 16px' : '0' ?>;
            padding: <?= empty($isPdf) ? '18mm 15mm' : '0' ?>;
            background: #fff;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .kop h1 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .kop p {
            margin: 4px 0 0;
            font-size: 11pt;
        }

        h2.title {
            text-align: center;
            font-size: 14pt;
            text-transform: uppercase;
            margin: 14px 0 18px;
        }

        h3 {
            font-size: 12.5pt;
            margin: 18px 0 8px;
        }

        p {
            margin: 0 0 8px;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            border: 1px solid #111;
            padding: 5px 6px;
            vertical-align: top;
            font-size: 10.5pt;
        }

        th {
            background: #f1f1f1;
            font-weight: bold;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .summary-box {
            border: 1px solid #111;
            padding: 10px;
            margin: 10px 0 14px;
        }

        .signature {
            width: 100%;
            margin-top: 30px;
        }

        .signature td {
            border: 0;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 10px;
        }

        .signature-space {
            height: 70px;
        }

        @media print {
            body {
                background: #fff;
            }

            .print-toolbar {
                display: none;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
            }

            a {
                color: #000;
                text-decoration: none;
            }
        }
    </style>
</head>
<body>

<?php if (empty($isPdf)): ?>
    <div class="print-toolbar">
        <button onclick="window.print()" class="btn btn-primary">Print / Cetak</button>
        <a href="<?= base_url('admin/reports/validasi-produk/' . $analysis['id']) ?>" class="btn">Kembali</a>
    </div>
<?php endif; ?>

<div class="page">
    <div class="kop">
        <h1>SIVALID</h1>
        <p>Sistem Informasi Validasi Instrumen Penelitian</p>
    </div>

    <h2 class="title">Laporan Validasi Produk</h2>

    <h3>1. Identitas Produk</h3>
    <table>
        <tr>
            <th style="width: 35%;">Kode Produk</th>
            <td><?= esc($link['product_kode'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Nama Produk</th>
            <td><?= esc($link['nama_produk'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Jenis Produk</th>
            <td><?= esc($link['jenis_produk'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Status Produk</th>
            <td><?= esc($link['product_status'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Deskripsi Produk</th>
            <td><?= nl2br(esc($link['product_deskripsi'] ?? '-')) ?></td>
        </tr>
    </table>

    <h3>2. Identitas Instrumen</h3>
    <table>
        <tr>
            <th style="width: 35%;">Kode Instrumen</th>
            <td><?= esc($link['kode']) ?></td>
        </tr>
        <tr>
            <th>Judul Instrumen</th>
            <td><?= esc($link['judul']) ?></td>
        </tr>
        <tr>
            <th>Jenis Instrumen</th>
            <td><?= esc($link['jenis']) ?></td>
        </tr>
        <tr>
            <th>Status Instrumen</th>
            <td><?= esc($link['instrument_status'] ?? '-') ?></td>
        </tr>
    </table>

    <h3>3. Identitas Validator</h3>
    <?php if (empty($responses)): ?>
        <p>Belum ada data validator produk.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Validator</th>
                    <th>Email</th>
                    <th>Bidang Keahlian</th>
                    <th>Instansi</th>
                    <th>Kesimpulan</th>
                    <th>Komentar Umum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $index => $response): ?>
                    <tr>
                        <td class="center"><?= $index + 1 ?></td>
                        <td><?= esc($response['nama']) ?></td>
                        <td><?= esc($response['email'] ?: '-') ?></td>
                        <td><?= esc($response['bidang_keahlian'] ?: '-') ?></td>
                        <td><?= esc($response['instansi'] ?: '-') ?></td>
                        <td><?= esc($response['kesimpulan'] ?: '-') ?></td>
                        <td><?= nl2br(esc($response['komentar_umum'] ?: '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>4. Rekap Skor Keseluruhan</h3>
    <table>
        <tr>
            <th style="width: 35%;">Jumlah Validator</th>
            <td><?= esc($analysis['jumlah_responden']) ?></td>
        </tr>
        <tr>
            <th>Jumlah Butir</th>
            <td><?= esc($analysis['jumlah_butir']) ?></td>
        </tr>
        <tr>
            <th>Total Skor</th>
            <td><?= esc($analysis['total_skor']) ?></td>
        </tr>
        <tr>
            <th>Skor Maksimal</th>
            <td><?= esc($analysis['skor_maksimal']) ?></td>
        </tr>
        <tr>
            <th>Rata-Rata</th>
            <td><?= esc($analysis['rata_rata']) ?></td>
        </tr>
        <tr>
            <th>Persentase Kelayakan</th>
            <td><strong><?= esc($analysis['persentase']) ?>%</strong></td>
        </tr>
        <tr>
            <th>Kategori Produk</th>
            <td><strong><?= esc($analysis['kategori']) ?></strong></td>
        </tr>
    </table>

    <h3>5. Rekap Skor Per Aspek</h3>
    <?php if (empty($aspectAnalysis)): ?>
        <p>Belum ada analisis aspek.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Aspek</th>
                    <th>Total Skor</th>
                    <th>Skor Maks.</th>
                    <th>Rata-Rata</th>
                    <th>Persentase</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($aspectAnalysis as $index => $aspect): ?>
                    <tr>
                        <td class="center"><?= $index + 1 ?></td>
                        <td><?= esc($aspect['nama_aspek']) ?></td>
                        <td class="center"><?= esc($aspect['total_skor']) ?></td>
                        <td class="center"><?= esc($aspect['skor_maksimal']) ?></td>
                        <td class="center"><?= esc($aspect['rata_rata']) ?></td>
                        <td class="center"><?= esc($aspect['persentase']) ?>%</td>
                        <td><?= esc($aspect['kategori']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>6. Rekap Skor Per Butir</h3>
    <?php if (empty($itemAnalysis)): ?>
        <p>Belum ada analisis butir.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 18%;">Aspek</th>
                    <th>Pernyataan</th>
                    <th style="width: 10%;">Total</th>
                    <th style="width: 10%;">Rata-Rata</th>
                    <th style="width: 14%;">Kategori</th>
                    <th style="width: 14%;">Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemAnalysis as $item): ?>
                    <tr>
                        <td class="center"><?= esc($item['nomor']) ?></td>
                        <td><?= esc($item['nama_aspek']) ?></td>
                        <td><?= nl2br(esc($item['pernyataan'])) ?></td>
                        <td class="center"><?= esc($item['total_skor']) ?></td>
                        <td class="center"><?= esc($item['rata_rata']) ?></td>
                        <td><?= esc($item['kategori']) ?></td>
                        <td><strong><?= esc($item['rekomendasi']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>7. Komentar dan Saran Validator</h3>
    <?php if (empty($comments)): ?>
        <p>Tidak ada komentar per butir.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">No Butir</th>
                    <th>Butir</th>
                    <th style="width: 20%;">Validator</th>
                    <th>Komentar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td class="center"><?= esc($comment['nomor']) ?></td>
                        <td><?= nl2br(esc($comment['pernyataan'])) ?></td>
                        <td><?= esc($comment['nama']) ?></td>
                        <td><?= nl2br(esc($comment['komentar'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>8. Kesimpulan</h3>
    <div class="summary-box">
        <p>
            Berdasarkan hasil validasi produk oleh
            <strong><?= esc($analysis['jumlah_responden']) ?></strong> validator,
            produk <strong><?= esc($link['nama_produk'] ?? '-') ?></strong>
            memperoleh total skor
            <strong><?= esc($analysis['total_skor']) ?></strong>
            dari skor maksimal
            <strong><?= esc($analysis['skor_maksimal']) ?></strong>,
            dengan persentase kelayakan sebesar
            <strong><?= esc($analysis['persentase']) ?>%</strong>.
            Dengan demikian, produk berada pada kategori
            <strong>"<?= esc($analysis['kategori']) ?>"</strong>.
        </p>
    </div>

    <table class="signature">
        <tr>
            <td></td>
            <td>
                Peneliti/Admin,<br>
                <div class="signature-space"></div>
                ___________________________
            </td>
        </tr>
    </table>
</div>

<?php if (empty($isPdf)): ?>
    <script>
        // Tidak auto print agar admin bisa cek dulu.
        // Jika ingin otomatis membuka dialog print, aktifkan baris berikut:
        // window.onload = function () { window.print(); };
    </script>
<?php endif; ?>

</body>
</html>
