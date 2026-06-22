<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Paket Validasi') ?></title>
    <link rel="icon" href="<?= sivalid_favicon_url() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/tabler/css/tabler.min.css') ?>">
    <style>
        :root {
            --pub-bg: #edf2f5;
            --pub-surface: var(--tblr-bg-surface, #ffffff);
            --pub-border: #cfd9e4;
            --pub-border-soft: #dde6ef;
            --pub-text: #0f172a;
            --pub-muted: #53657a;
            --pub-blue: #0b63b6;
            --pub-blue-soft: #f3f8fd;
            --pub-green: #16a34a;
            --pub-green-soft: #f0fdf4;
            --pub-radius: 8px;
        }

        body {
            margin: 0;
            background: var(--pub-bg);
            color: var(--pub-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.65;
        }

        .public-shell {
            width: min(946px, calc(100% - 32px));
            margin: 32px auto 52px;
        }

        .public-card {
            background: var(--pub-surface);
            border: 1px solid var(--pub-border);
            border-radius: var(--pub-radius);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
            padding: 1.55rem 1.6rem 1.35rem;
            margin-bottom: 1rem;
        }

        .bundle-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 1px solid #d5e0ec;
            margin-bottom: 1.15rem;
            padding-bottom: .95rem;
        }

        .bundle-header-main {
            min-width: 0;
        }

        .bundle-title {
            margin: 0 0 .25rem;
            font-size: 1.46rem;
            font-weight: 720;
            line-height: 1.25;
            color: var(--pub-text);
        }

        .bundle-meta {
            color: var(--pub-muted);
            font-size: .94rem;
        }

        .bundle-meta span + span::before {
            content: ' / ';
            color: #94a3b8;
        }

        .identity-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: .65rem 1.2rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #fff;
            color: #0b63b6;
            font-size: 1rem;
            font-weight: 650;
            line-height: 1.2;
            text-decoration: none;
            white-space: nowrap;
        }

        .identity-edit-btn:hover,
        .identity-edit-btn:focus {
            border-color: #0b63b6;
            background: #f3f8fd;
            color: #0b63b6;
            text-decoration: none;
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
            display: grid;
            gap: .8rem;
        }

        .instrument-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 1rem 1.1rem;
            border: 1px solid var(--pub-border-soft);
            border-radius: var(--pub-radius);
            padding: 1.05rem 1.1rem;
            background: #ffffff;
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }

        .instrument-item:hover {
            border-color: #aac4df;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.055);
            transform: translateY(-1px);
        }

        .instrument-no {
            width: 3.05rem;
            height: 2.1rem;
            border-radius: 999px;
            background: #edf5fc;
            color: var(--pub-blue);
            font-weight: 700;
            font-size: .92rem;
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
            font-size: 1rem;
            font-weight: 620;
            line-height: 1.5;
            margin-bottom: .25rem;
            color: #0f172a;
        }

        .instrument-meta {
            font-size: .9rem;
            color: var(--pub-muted);
            line-height: 1.35;
        }

        .pub-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--pub-blue);
            background: #0b6fc8;
            color: #fff;
            min-height: 44px;
            padding: .58rem 1.25rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: .95rem;
            font-weight: 650;
            text-decoration: none;
            flex-shrink: 0;
            line-height: 1.2;
        }

        .pub-btn,
        .pub-btn:hover,
        .pub-btn:focus,
        .pub-btn:active,
        .pub-btn:visited {
            text-decoration: none !important;
        }

        .pub-btn:hover {
            background: #095fae;
            border-color: #095fae;
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

        .identity-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: .6rem;
            margin-top: .85rem;
            flex-wrap: wrap;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .86rem;
            font-weight: 650;
            padding: .26rem .65rem;
            border-radius: 999px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-belum  { background: #f1f5f9; color: #475569; }
        .status-proses { background: #fef9c3; color: #854d0e; }
        .status-selesai { background: #dcfce7; color: #166534; }

        .progress-summary {
            font-size: .96rem;
            color: #475569;
            font-weight: 500;
            text-align: right;
        }

        .instrument-list-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin: 1.2rem 0 .75rem;
        }

        .instrument-list-title {
            margin: 0;
            color: var(--pub-text);
            font-size: 1.08rem;
            font-weight: 720;
            line-height: 1.35;
        }

        .profile-box {
            margin-bottom: .95rem;
        }

        .profile-title {
            margin: 0 0 .55rem;
            color: var(--pub-text);
            font-size: 1rem;
            font-weight: 720;
            line-height: 1.35;
        }

        .profile-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--pub-border-soft);
            background: #fff;
            font-size: .92rem;
        }

        .profile-table th,
        .profile-table td {
            border: 1px solid var(--pub-border-soft);
            padding: .46rem .65rem;
            vertical-align: top;
            line-height: 1.42;
        }

        .profile-table th {
            width: 190px;
            background: #f1f5f9;
            color: #1f2a3d;
            font-weight: 680;
            text-align: left;
        }

        .profile-table td {
            color: #0f172a;
        }

        .profile-muted {
            color: var(--pub-muted);
        }

        .profile-table .pub-btn {
            min-height: 36px;
            padding: .42rem .9rem;
            font-size: .88rem;
        }

        .public-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, .46);
        }

        .public-modal-backdrop.show {
            display: flex;
        }

        .public-modal {
            width: min(940px, 100%);
            height: min(86vh, 780px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .24);
        }

        .public-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--pub-border-soft);
        }

        .public-modal-title {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 720;
        }

        .public-modal-close {
            width: 38px;
            height: 38px;
            padding: 0;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            background: #94a3b8;
            color: #ffffff;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .public-modal-close .icon {
            width: 18px;
            height: 18px;
            stroke-width: 2.3;
        }

        .public-modal-close:hover,
        .public-modal-close:focus {
            outline: none;
            border-color: #64748b;
            background: #64748b;
            color: #ffffff;
            box-shadow: 0 0 0 2px rgba(100, 116, 139, .22);
        }

        .public-modal-body {
            flex: 1;
            min-height: 0;
            background: #eef2f6;
        }

        .pdf-frame {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            background: #eef2f6;
        }

        .identity-title {
            margin: .2rem 0 .45rem;
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--pub-text);
        }

        @media (max-width: 720px) {
            .public-shell {
                width: min(100% - 20px, 946px);
                margin-top: 18px;
            }

            .public-card {
                padding: 1.15rem 1rem;
            }

            .bundle-title {
                font-size: 1.28rem;
            }

            .bundle-meta span {
                display: block;
            }

            .bundle-meta span + span::before {
                content: '';
            }

            .bundle-header {
                flex-direction: column;
            }

            .instrument-item {
                grid-template-columns: auto minmax(0, 1fr);
                align-items: flex-start;
            }

            .status-badge {
                grid-column: 2;
                justify-self: start;
            }

            .instrument-item .pub-btn {
                grid-column: 1 / -1;
                width: 100%;
            }

            .instrument-list-head {
                flex-direction: column;
                gap: .25rem;
            }

            .progress-summary {
                text-align: left;
            }

            .profile-table,
            .profile-table tbody,
            .profile-table tr,
            .profile-table th,
            .profile-table td {
                display: block;
                width: 100%;
            }

            .profile-table th {
                border-bottom: 0;
            }

            .public-modal-backdrop {
                padding: 10px;
            }

            .identity-actions,
            .identity-actions .pub-btn,
            .identity-actions .identity-edit-btn {
                width: 100%;
            }
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
$profile = isset($profile) && is_array($profile) ? $profile : [];
$profilePdf = trim((string) ($profile['ringkasan_penelitian_pdf'] ?? ''));
$profilePdfUrl = $profilePdf;
$profilePdfViewerUrl = sivalid_pdf_viewer_url($profilePdf);
$hasValidatorSession = is_array($validatorSession) && !empty($validatorSession);
$identityButtonLabel = $hasValidatorSession ? 'Simpan Identitas' : 'Mulai Validasi';
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
            <div class="bundle-header-main">
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
            <?php if ($state !== 'identity' && $hasValidatorSession): ?>
                <a href="<?= base_url('paket/' . esc($token) . '?identitas=edit') ?>" class="identity-edit-btn">Ubah Identitas</a>
            <?php endif; ?>
        </div>

        <?php if ($state === 'identity'): ?>
            <!-- STATE: IDENTITY FORM -->
            <?php if (trim((string) ($bundle['deskripsi'] ?? '')) !== ''): ?>
                <div class="bundle-description">
                    <?= render_rich_text_content((string) $bundle['deskripsi']) ?>
                </div>
            <?php endif; ?>

            <h3 class="identity-title"><?= $hasValidatorSession ? 'Ubah Identitas Validator' : 'Identitas Validator' ?></h3>
            <?php if ($hasValidatorSession): ?>
                <p class="identity-intro">
                    Perbarui identitas Bapak/Ibu bila ada data yang perlu disesuaikan. Progres penilaian yang sudah tersimpan tidak akan berubah.
                </p>
            <?php else: ?>
                <p class="identity-intro">
                    Silakan lengkapi identitas Bapak/Ibu terlebih dahulu. Setelah itu, Bapak/Ibu dapat mulai
                    memvalidasi <strong><?= count($instruments) ?> instrumen</strong> yang tersedia.
                </p>
            <?php endif; ?>

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
                               value="<?= esc(old('nama', (string) ($validatorSession['validator_nama'] ?? ''))) ?>"
                               placeholder="Nama Bapak/Ibu"
                               required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?= esc(old('email', (string) ($validatorSession['validator_email'] ?? ''))) ?>"
                               placeholder="email@contoh.com"
                               maxlength="150">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="instansi">Instansi</label>
                        <input type="text" id="instansi" name="instansi"
                               value="<?= esc(old('instansi', (string) ($validatorSession['validator_instansi'] ?? ''))) ?>"
                               placeholder="Nama instansi"
                               maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="bidang_keahlian">Bidang Keahlian</label>
                        <input type="text" id="bidang_keahlian" name="bidang_keahlian"
                               value="<?= esc(old('bidang_keahlian', (string) ($validatorSession['validator_bidang_keahlian'] ?? ''))) ?>"
                               placeholder="Misal: Pendidikan Matematika"
                               maxlength="150">
                    </div>
                </div>

                <div class="identity-actions">
                    <?php if ($hasValidatorSession): ?>
                        <a href="<?= base_url('paket/' . esc($token)) ?>" class="identity-edit-btn">
                            Kembali ke Daftar Instrumen
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="pub-btn" style="padding: .65rem 1.5rem; font-size: 1rem;">
                        <?= esc($identityButtonLabel) ?>
                    </button>
                </div>
            </form>

        <?php else: ?>
            <!-- STATE: PROGRESS VIEW -->
            <div class="profile-box">
                <h2 class="profile-title">Profil Peneliti</h2>
                <table class="profile-table">
                    <tbody>
                    <tr>
                        <th>Nama Peneliti</th>
                        <td><?= esc(trim((string) ($profile['nama_peneliti'] ?? '')) ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td><?= esc(trim((string) ($profile['nim'] ?? '')) ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td><?= esc(trim((string) ($profile['program_studi'] ?? '')) ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Perguruan Tinggi</th>
                        <td><?= esc(trim((string) ($profile['institusi'] ?? '')) ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Judul Penelitian</th>
                        <td><?= esc(trim((string) ($profile['nama_penelitian'] ?? '')) ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Ringkasan Penelitian</th>
                        <td>
                            <?php if ($profilePdfUrl !== ''): ?>
                                <button type="button" class="pub-btn" data-open-modal="research-summary-modal">
                                    Lihat Ringkasan Penelitian
                                </button>
                            <?php else: ?>
                                <span class="profile-muted">Belum tersedia.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <?php
            $selesaiCount = 0;
            foreach ($instruments as $instr) {
                $prog = $progressMap[(int) $instr['instrument_id']] ?? null;
                if ($prog && $prog['status'] === 'selesai') {
                    $selesaiCount++;
                }
            }
            ?>
            <div class="instrument-list-head">
                <h2 class="instrument-list-title">Daftar Instrumen Siap Divalidasi</h2>
                <div class="progress-summary">
                    Progress: <strong><?= $selesaiCount ?>/<?= count($instruments) ?></strong> instrumen selesai.
                </div>
            </div>

            <ul class="instrument-list">
                <?php foreach ($instruments as $i => $instr): ?>
                    <?php
                    $pos  = $i + 1;
                    $prog = $progressMap[(int) $instr['instrument_id']] ?? null;
                    $st   = $prog['status'] ?? 'belum';

                    if ($st === 'selesai') {
                        $badgeClass = 'status-selesai';
                        $badgeText  = 'Selesai';
                        $btnText    = 'Buka Penilaian';
                    } elseif ($st === 'proses') {
                        $badgeClass = 'status-proses';
                        $badgeText  = 'Dalam Proses';
                        $btnText    = 'Lanjutkan';
                    } else {
                        $badgeClass = 'status-belum';
                        $badgeText  = 'Belum Diisi';
                        $btnText    = 'Mulai';
                    }
                    ?>
                    <li class="instrument-item">
                        <div class="instrument-no"><?= esc(sprintf('%03d', $pos)) ?></div>
                        <div class="instrument-info">
                            <div class="instrument-name"><?= esc($instr['judul']) ?></div>
                            <div class="instrument-meta">
                                <?= esc($instr['kode']) ?>
                                <?php if (!empty($instr['jenis'])): ?>
                                    &nbsp;&middot;&nbsp; <?= esc(title_case_label((string) ($instr['jenis'] ?? '-'))) ?>
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
        <?php endif; ?>
    </div>

</div>

<?php if ($profilePdfUrl !== ''): ?>
<div id="research-summary-modal" class="public-modal-backdrop" aria-hidden="true">
    <div class="public-modal" role="dialog" aria-modal="true" aria-labelledby="research-summary-modal-title">
        <div class="public-modal-head">
            <h2 id="research-summary-modal-title" class="public-modal-title">Ringkasan Penelitian</h2>
            <button type="button" class="btn btn-icon btn-ghost-secondary public-modal-close" data-close-modal aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="public-modal-body">
            <iframe class="pdf-frame" src="<?= esc($profilePdfViewerUrl) ?>" title="Ringkasan Penelitian"></iframe>
        </div>
    </div>
</div>
<?php endif; ?>

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

        var activeModal = null;

        function openModal(modal) {
            if (!modal) return;
            activeModal = modal;
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            var closeButton = modal.querySelector('[data-close-modal]');
            if (closeButton) {
                closeButton.focus();
            }
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (activeModal === modal) {
                activeModal = null;
            }
        }

        document.querySelectorAll('[data-open-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                openModal(document.getElementById(button.getAttribute('data-open-modal')));
            });
        });

        document.querySelectorAll('.public-modal-backdrop').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                var closeTrigger = event.target && event.target.closest
                    ? event.target.closest('[data-close-modal]')
                    : null;

                if (event.target === modal || closeTrigger) {
                    closeModal(modal);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && activeModal) {
                closeModal(activeModal);
            }
        });
    });
</script>

</body>
</html>
