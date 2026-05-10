<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Paket Validasi') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-bg: var(--tblr-bg-surface-secondary, #f6f8fb);
            --pub-surface: var(--tblr-bg-surface, #ffffff);
            --pub-border: var(--tblr-border-color, #dce1e7);
            --pub-text: var(--tblr-body-color, #182433);
            --pub-muted: var(--tblr-muted, #667382);
            --pub-blue: var(--tblr-primary, #066fd1);
            --pub-blue-soft: var(--tblr-primary-lt, #e6f1fb);
            --pub-green: #16a34a;
            --pub-green-soft: #f0fdf4;
            --pub-radius: 8px;
        }

        body {
            margin: 0;
            background: var(--pub-bg);
            color: var(--pub-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .public-shell {
            width: min(860px, calc(100% - 24px));
            margin: 28px auto 48px;
        }

        .public-card {
            background: var(--pub-surface);
            border: 1px solid var(--pub-border);
            border-radius: var(--pub-radius);
            box-shadow: 0 1px 6px rgba(15, 23, 42, 0.06);
            padding: 1.4rem 1.5rem;
            margin-bottom: 1rem;
        }

        .bundle-header {
            border-bottom: 2px solid var(--pub-blue);
            margin-bottom: 1.2rem;
            padding-bottom: .8rem;
        }

        .bundle-title {
            margin: 0 0 .3rem;
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--pub-text);
        }

        .bundle-meta {
            color: var(--pub-muted);
            font-size: .88rem;
        }

        .bundle-meta span + span::before {
            content: ' | ';
        }

        .bundle-description {
            color: var(--pub-text);
            margin-bottom: 1.1rem;
            font-size: .99rem;
        }

        .bundle-description .rich-text-content {
            padding: 0;
            line-height: 1.42;
            text-align: justify;
            text-justify: inter-word;
        }

        .bundle-description .rich-text-content p,
        .bundle-description .rich-text-content ol,
        .bundle-description .rich-text-content ul {
            margin-bottom: .42rem;
            color: var(--pub-text);
        }

        .bundle-description .rich-text-content > :last-child {
            margin-bottom: 0;
        }

        /* Instrument list */
        .instrument-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .instrument-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--pub-border);
            border-radius: var(--pub-radius);
            padding: .9rem 1rem;
            margin-bottom: .6rem;
            background: #fff;
            transition: border-color .15s;
        }

        .instrument-item:hover {
            border-color: var(--pub-blue);
        }

        .instrument-no {
            width: 2.8rem;
            height: 2rem;
            border-radius: 999px;
            background: var(--pub-blue-soft);
            color: var(--pub-blue);
            font-weight: 700;
            font-size: .9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .instrument-info {
            flex: 1;
            min-width: 0;
        }

        .instrument-name {
            font-weight: 600;
            margin-bottom: .15rem;
        }

        .instrument-meta {
            font-size: .82rem;
            color: var(--pub-muted);
        }

        .pub-btn {
            display: inline-block;
            border: 1px solid var(--pub-blue);
            background: var(--pub-blue);
            color: #fff;
            padding: .5rem 1.1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            flex-shrink: 0;
        }

        .pub-btn,
        .pub-btn:hover,
        .pub-btn:focus,
        .pub-btn:active,
        .pub-btn:visited {
            text-decoration: none !important;
        }

        .pub-btn:hover {
            background: var(--tblr-primary-darken, #0054a6);
            border-color: var(--tblr-primary-darken, #0054a6);
            color: #fff;
        }

        .pub-btn-outline {
            background: #fff;
            color: var(--pub-blue);
        }

        .pub-btn-outline:hover {
            background: var(--pub-blue-soft);
            color: var(--pub-blue);
        }

        .pub-btn[disabled],
        .pub-btn.disabled {
            opacity: .56;
            cursor: not-allowed;
            pointer-events: none;
            background: #9ca3af;
            border-color: #9ca3af;
            color: #fff;
        }

        /* Identity form */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
            margin-bottom: .75rem;
        }

        @media (max-width: 580px) {
            .form-row { grid-template-columns: 1fr; }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }

        .form-group label {
            font-size: .88rem;
            font-weight: 600;
            color: var(--pub-text);
        }

        .form-group label .req {
            color: #e53e3e;
            margin-left: 2px;
        }

        .form-group input,
        .form-group select {
            border: 1px solid var(--pub-border);
            border-radius: 6px;
            padding: .5rem .75rem;
            font-size: .93rem;
            color: var(--pub-text);
            background: #fff;
            outline: none;
            transition: border-color .15s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--pub-blue);
            box-shadow: 0 0 0 .25rem rgba(var(--tblr-primary-rgb), .25);
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .8rem;
            font-weight: 600;
            padding: .2rem .55rem;
            border-radius: 999px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-belum  { background: #f1f5f9; color: #475569; }
        .status-proses { background: #fef9c3; color: #854d0e; }
        .status-selesai { background: #dcfce7; color: #166534; }

        .validator-strip {
            background: var(--pub-blue-soft);
            border: 1px solid rgba(var(--tblr-primary-rgb), .24);
            border-radius: var(--pub-radius);
            padding: .7rem 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .validator-name {
            font-weight: 600;
            color: var(--pub-blue);
        }

        .validator-meta {
            font-size: .84rem;
            color: var(--pub-muted);
        }

        .progress-summary {
            font-size: .84rem;
            color: var(--pub-muted);
            margin-bottom: 1rem;
        }

        .final-banner {
            background: var(--pub-green-soft);
            border: 1px solid #bbf7d0;
            border-radius: var(--pub-radius);
            padding: .8rem 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .final-banner-title {
            color: var(--pub-green);
            font-weight: 700;
            font-size: .95rem;
        }

        .final-banner-meta {
            color: #166534;
            font-size: .83rem;
        }

        .submit-final-box {
            margin-top: 1rem;
            border-top: 1px solid var(--pub-border);
            padding-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .submit-final-hint {
            font-size: .84rem;
            color: var(--pub-muted);
        }

        .identity-title {
            margin: .2rem 0 .45rem;
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--pub-text);
        }

        .identity-intro {
            margin-bottom: .9rem;
            font-size: .94rem;
            line-height: 1.45;
            color: var(--pub-text);
        }
    </style>
</head>
<body>

<?php
$bundle      = isset($bundle) && is_array($bundle) ? $bundle : [];
$instruments = isset($instruments) && is_array($instruments) ? $instruments : [];
$token       = $bundle['token'] ?? '';
$state       = $state ?? 'identity';
$progressMap = $progressMap ?? [];
$validatorSession = $validatorSession ?? null;
$isFinal = isset($validatorSession['status_session']) && $validatorSession['status_session'] === 'final';
?>

<div class="public-shell">

    <!-- Flash messages -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success mb-3" role="alert">
            <?= esc(session('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger mb-3" role="alert">
            <?= esc(session('error')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger mb-3" role="alert">
            <?php $errors = array_values((array) session('errors')); ?>
            <?php if (count($errors) === 1): ?>
                <?= esc($errors[0]) ?>
            <?php else: ?>
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="public-card">
        <!-- Bundle header -->
        <div class="bundle-header">
            <div class="bundle-title"><?= esc($bundle['judul'] ?? 'Paket Validasi') ?></div>
            <div class="bundle-meta">
                <?php if (!empty($bundle['sasaran'])): ?>
                    <span>Validator: <?= esc($bundle['sasaran']) ?></span>
                <?php endif; ?>
                <span><?= count($instruments) ?> instrumen</span>
                <?php if (!empty($bundle['tanggal_selesai'])): ?>
                    <span>Batas Pengisian: <?= esc(format_tanggal_indonesia($bundle['tanggal_selesai'])) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($state === 'identity'): ?>
            <!-- ═══ STATE: IDENTITY FORM ═══ -->
            <h3 class="identity-title">Identitas Validator</h3>
            <p class="identity-intro">
                Silakan lengkapi identitas Bapak/Ibu terlebih dahulu. Setelah itu, Bapak/Ibu dapat mulai
                memvalidasi <strong><?= count($instruments) ?> instrumen</strong> yang tersedia.
            </p>

            <form method="post" action="<?= base_url('paket/' . esc($token) . '/mulai') ?>" novalidate>
                <?= csrf_field() ?>

                <!-- Honeypot -->
                <div style="display:none;" aria-hidden="true">
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="nama" name="nama"
                               value="<?= esc(old('nama')) ?>"
                               placeholder="Nama Bapak/Ibu"
                               required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?= esc(old('email')) ?>"
                               placeholder="email@contoh.com"
                               maxlength="150">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="instansi">Instansi</label>
                        <input type="text" id="instansi" name="instansi"
                               value="<?= esc(old('instansi')) ?>"
                               placeholder="Nama instansi"
                               maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="bidang_keahlian">Bidang Keahlian</label>
                        <input type="text" id="bidang_keahlian" name="bidang_keahlian"
                               value="<?= esc(old('bidang_keahlian')) ?>"
                               placeholder="Misal: Pendidikan Matematika"
                               maxlength="150">
                    </div>
                </div>

                <button type="submit" class="pub-btn" style="margin-top: .5rem; padding: .65rem 1.5rem; font-size: 1rem;">
                    Mulai Validasi &rarr;
                </button>
            </form>

        <?php else: ?>
            <!-- ═══ STATE: PROGRESS VIEW ═══ -->
            <div class="validator-strip">
                <div>
                    <div class="validator-name"><?= esc($validatorSession['validator_nama'] ?? '') ?></div>
                    <div class="validator-meta">
                        <?php if (!empty($validatorSession['validator_instansi'])): ?>
                            <?= esc($validatorSession['validator_instansi']) ?>
                        <?php endif; ?>
                        <?php if (!empty($validatorSession['validator_bidang_keahlian'])): ?>
                            &nbsp;·&nbsp; <?= esc($validatorSession['validator_bidang_keahlian']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($isFinal): ?>
                <div class="final-banner">
                    <div>
                        <div class="final-banner-title">Validasi sudah disubmit final.</div>
                        <div class="final-banner-meta">
                            <?php if (!empty($validatorSession['submitted_at'])): ?>
                                Dikirim pada <?= esc(format_tanggal_indonesia($validatorSession['submitted_at'], true)) ?>.
                            <?php else: ?>
                                Jawaban tidak dapat diubah.
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?= base_url('paket/' . esc($token) . '/ringkasan') ?>" class="pub-btn">
                        Lihat Ringkasan
                    </a>
                </div>
            <?php endif; ?>

            <?php
            $selesaiCount = 0;
            foreach ($instruments as $instr) {
                $prog = $progressMap[(int) $instr['instrument_id']] ?? null;
                if ($prog && $prog['status'] === 'selesai') {
                    $selesaiCount++;
                }
            }
            ?>
            <div class="progress-summary">
                Progress: <strong><?= $selesaiCount ?>/<?= count($instruments) ?></strong> instrumen selesai.
            </div>

            <?php
            $totalInstrumen = count($instruments);
            $canSubmitFinal = $totalInstrumen > 0 && $selesaiCount >= $totalInstrumen;
            ?>

            <ul class="instrument-list">
                <?php foreach ($instruments as $i => $instr): ?>
                    <?php
                    $pos  = $i + 1;
                    $prog = $progressMap[(int) $instr['instrument_id']] ?? null;
                    $st   = $prog['status'] ?? 'belum';

                    if ($st === 'selesai') {
                        $badgeClass = 'status-selesai';
                        $badgeText  = 'Selesai';
                        $btnText    = $isFinal ? 'Lihat Penilaian' : 'Buka Penilaian';
                    } elseif ($st === 'proses') {
                        $badgeClass = 'status-proses';
                        $badgeText  = 'Dalam Proses';
                        $btnText    = $isFinal ? 'Lihat' : 'Lanjutkan';
                    } else {
                        $badgeClass = 'status-belum';
                        $badgeText  = 'Belum Diisi';
                        $btnText    = $isFinal ? 'Lihat' : 'Mulai';
                    }
                    ?>
                    <li class="instrument-item">
                        <div class="instrument-no"><?= esc(sprintf('%03d', $pos)) ?></div>
                        <div class="instrument-info">
                            <div class="instrument-name"><?= esc($instr['judul']) ?></div>
                            <div class="instrument-meta">
                                <?= esc($instr['kode']) ?>
                                <?php if (!empty($instr['jenis'])): ?>
                                    &nbsp;·&nbsp; <?= esc(title_case_label((string) ($instr['jenis'] ?? '-'))) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="status-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                        <a href="<?= base_url('paket/' . esc($token) . '/isi/' . $pos) ?>"
                           class="pub-btn <?= $st === 'selesai' ? 'pub-btn-outline' : '' ?>">
                            <?= $btnText ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (!$isFinal): ?>
                <div class="submit-final-box">
                    <div class="submit-final-hint">
                        <?php if ($selesaiCount < count($instruments)): ?>
                            Selesaikan semua instrumen sebelum submit final.
                        <?php else: ?>
                            Semua instrumen selesai. Submit final untuk mengunci jawaban.
                        <?php endif; ?>
                    </div>
                    <form method="post" action="<?= base_url('paket/' . esc($token) . '/submit') ?>">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="pub-btn"
                                <?= $canSubmitFinal ? '' : 'disabled title="Selesaikan semua instrumen terlebih dahulu" aria-disabled="true"' ?>
                                onclick="return confirm('Submit validasi ini secara final? Setelah submit, jawaban tidak dapat diubah.')">
                            Submit Final
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.alert').forEach(function (alertElement) {
            setTimeout(function () {
                alertElement.style.transition = 'opacity .35s ease, transform .35s ease';
                alertElement.style.opacity = '0';
                alertElement.style.transform = 'translateY(-4px)';

                setTimeout(function () {
                    alertElement.remove();
                }, 350);
            }, 5000);
        });
    });
</script>

</body>
</html>
