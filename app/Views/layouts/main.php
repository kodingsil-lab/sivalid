<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SIVALID') ?> - SIVALID</title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">

    <!-- Tabler CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">

    <!-- SIVALID custom styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/sivalid.css?v=' . filemtime(FCPATH . 'assets/css/sivalid.css')) ?>">
</head>
<body class="antialiased">

<div class="page">

    <!-- Sidebar -->
    <?= $this->include('layouts/sidebar') ?>

    <div class="page-wrapper">

        <!-- Topbar -->
        <?= $this->include('layouts/topbar') ?>

        <!-- Konten utama -->
        <div class="page-body">
            <div class="container-xl">

                <?= $this->renderSection('content') ?>

            </div>
        </div>

    </div>
</div>

<!-- Tabler JS -->
<script src="<?= base_url('assets/vendor/tabler/js/tabler.min.js') ?>"></script>

<!-- SIVALID custom scripts -->
<script src="<?= base_url('assets/js/sivalid.js') ?>"></script>

</body>
</html>

