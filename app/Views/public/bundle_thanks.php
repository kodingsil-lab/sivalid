<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paket Selesai – <?= esc($bundle['judul'] ?? 'Paket Validasi') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-blue: #1d4ed8;
            --pub-blue-soft: #eff6ff;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 28%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .thanks-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(15,23,42,.07);
            padding: 2.5rem 2rem;
            max-width: 540px;
            width: calc(100% - 32px);
            text-align: center;
        }

        .thanks-icon {
            width: 64px;
            height: 64px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            font-size: 2rem;
        }

        .thanks-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: .5rem;
        }

        .thanks-sub {
            color: #64748b;
            font-size: .95rem;
            margin-bottom: 1.4rem;
        }

        .thanks-badge {
            display: inline-block;
            background: var(--pub-blue-soft);
            color: var(--pub-blue);
            font-weight: 600;
            font-size: .88rem;
            padding: .35rem .9rem;
            border-radius: 999px;
            margin-bottom: 1.4rem;
        }
    </style>
</head>
<body>

<div class="thanks-box">
    <div class="thanks-icon">&#10003;</div>
    <div class="thanks-title">Paket Selesai!</div>
    <div class="thanks-badge">
        <?= (int) ($total ?? 0) ?> instrumen berhasil dikirim
    </div>
    <div class="thanks-sub">
        Terima kasih telah menyelesaikan semua instrumen dalam paket
        <strong><?= esc($bundle['judul'] ?? 'Paket Validasi') ?></strong>.
        Penilaian Bapak/Ibu sangat berarti bagi perbaikan instrumen penelitian.
    </div>
</div>

</body>
</html>
