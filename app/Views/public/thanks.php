<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Informasi') ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .container {
            width: 720px;
            max-width: calc(100% - 30px);
            margin: 60px auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 28px;
            box-sizing: border-box;
            text-align: center;
        }

        h1 {
            margin-top: 0;
            color: #1f4e79;
        }

        p {
            line-height: 1.6;
        }

        .muted {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?= esc($title ?? 'Informasi') ?></h1>
    <p><?= esc($message ?? 'Terima kasih.') ?></p>

    <p class="muted">
        Silakan hubungi admin/peneliti jika Bapak/Ibu merasa pesan ini muncul karena kesalahan.
    </p>
</div>

</body>
</html>
