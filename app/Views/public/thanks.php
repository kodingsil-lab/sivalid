<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Informasi') ?></title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sivalid.css') ?>">
    <style>
        body { background: var(--sv-bg, #f4f6f8); }
        .public-wrap { max-width: 560px; margin: 4rem auto; padding: 0 1rem; }
    </style>
</head>
<body>

<div class="public-wrap">
    <div class="card text-center">
        <div class="card-body py-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-success mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M5 12l5 5l10 -10" />
            </svg>
            <h2 class="card-title"><?= esc($title ?? 'Informasi') ?></h2>
            <p class="text-muted"><?= esc($message ?? 'Terima kasih.') ?></p>
            <p class="text-muted small"><?= esc($note ?? 'Silakan hubungi admin/peneliti jika Bapak/Ibu merasa pesan ini muncul karena kesalahan.') ?></p>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/vendor/tabler/js/tabler.min.js') ?>"></script>
</body>
</html>
