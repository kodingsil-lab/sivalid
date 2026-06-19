<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Ringkasan Validasi') ?></title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-bg: #f8fafc;
            --pub-surface: #ffffff;
            --pub-border: #e2e8f0;
            --pub-text: #1e293b;
            --pub-muted: #64748b;
            --pub-blue: #1d4ed8;
            --pub-green: #16a34a;
            --pub-radius: 8px;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, #eef2ff 0%, var(--pub-bg) 28%);
            color: var(--pub-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .shell {
            width: min(980px, calc(100% - 24px));
            margin: 24px auto 40px;
        }

        .card {
            background: var(--pub-surface);
            border: 1px solid var(--pub-border);
            border-radius: var(--pub-radius);
            box-shadow: 0 1px 6px rgba(15, 23, 42, 0.06);
            padding: 1rem 1.1rem;
            margin-bottom: .9rem;
        }

        .title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .subtitle {
            margin-top: .35rem;
            color: var(--pub-muted);
            font-size: .92rem;
        }

        .status-box {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            border-radius: var(--pub-radius);
            padding: .75rem .9rem;
            font-weight: 600;
        }

        .status-box.draft {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #1e3a8a;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .6rem;
            font-size: .9rem;
        }

        .meta-label {
            color: var(--pub-muted);
            margin-right: .35rem;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }

        th, td {
            border: 1px solid var(--pub-border);
            padding: .55rem .6rem;
            vertical-align: top;
        }

        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: .15rem .5rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
        }

        .badge-belum { background: #f1f5f9; color: #475569; }
        .badge-proses { background: #fef9c3; color: #854d0e; }
        .badge-selesai { background: #dcfce7; color: #166534; }

        .actions {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-top: .8rem;
        }

        .btn {
            display: inline-block;
            border: 1px solid var(--pub-blue);
            background: var(--pub-blue);
            color: #fff;
            text-decoration: none;
            padding: .55rem 1rem;
            border-radius: 6px;
            font-size: .9rem;
            font-weight: 600;
        }

        .btn:hover { color: #fff; background: #1a3fa8; }

        .btn-success {
            border-color: var(--pub-green);
            background: var(--pub-green);
        }

        .btn-success:hover { background: #15803d; }

        @media (max-width: 700px) {
            .meta { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php
$bundle = isset($bundle) && is_array($bundle) ? $bundle : [];
$validatorSession = isset($validatorSession) && is_array($validatorSession) ? $validatorSession : [];
$instrumentSummaries = isset($instrumentSummaries) && is_array($instrumentSummaries) ? $instrumentSummaries : [];
$selesaiCount = isset($selesaiCount) ? (int) $selesaiCount : 0;
$total = isset($total) ? (int) $total : count($instrumentSummaries);
$token = $bundle['token'] ?? '';
?>

<div class="shell">
    <div class="card">
        <h1 class="title">Ringkasan Validasi</h1>
        <div class="subtitle">
            <?= esc($bundle['judul'] ?? 'Paket Validasi') ?> - Progress <?= $selesaiCount ?>/<?= $total ?> instrumen selesai
        </div>
    </div>

    <div class="card">
        <div class="status-box draft">
            Progres tersimpan otomatis. Validator masih dapat kembali mengisi atau melengkapi instrumen kapan saja selama paket aktif.
        </div>
    </div>

    <div class="card">
        <div class="meta">
            <div><span class="meta-label">Validator:</span><?= esc($validatorSession['validator_nama'] ?? '-') ?></div>
            <div><span class="meta-label">Email:</span><?= esc($validatorSession['validator_email'] ?? '-') ?></div>
            <div><span class="meta-label">Instansi:</span><?= esc($validatorSession['validator_instansi'] ?? '-') ?></div>
            <div><span class="meta-label">Bidang:</span><?= esc($validatorSession['validator_bidang_keahlian'] ?? '-') ?></div>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th style="width:56px;">No</th>
                    <th>Instrumen</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:120px;">Rata-rata</th>
                    <th style="width:100px;">Butir</th>
                    <th>Kesimpulan</th>
                    <th>Komentar Umum</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($instrumentSummaries as $row): ?>
                    <?php
                    $status = $row['status'] ?? 'belum';
                    $badgeClass = $status === 'selesai' ? 'badge-selesai' : ($status === 'proses' ? 'badge-proses' : 'badge-belum');
                    ?>
                    <tr>
                        <td><?= esc(sprintf('%03d', (int) ($row['position'] ?? 0))) ?></td>
                        <td>
                            <strong><?= esc($row['instrument']['judul'] ?? '-') ?></strong><br>
                            <small style="color:#64748b;"><?= esc($row['instrument']['kode'] ?? '-') ?></small>
                        </td>
                        <td><span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span></td>
                        <td><?= $row['avg_skor'] !== null ? esc((string) $row['avg_skor']) : '-' ?></td>
                        <td><?= (int) ($row['jumlah_butir'] ?? 0) ?></td>
                        <td><?= esc($row['kesimpulan'] ?: '-') ?></td>
                        <td><?= esc($row['komentar_umum'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="actions">
            <a href="<?= base_url('paket/' . esc($token)) ?>" class="btn">Kembali ke Daftar Instrumen</a>
        </div>
    </div>
</div>

</body>
</html>
