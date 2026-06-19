<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$currentInstrument = isset($instrument) && is_array($instrument) ? $instrument : [];
$instrumentId = (int) ($currentInstrument['id'] ?? 0);
$status = (string) ($currentInstrument['status'] ?? '');
$statusLabel = status_display_label($status);
$statusClass = status_badge_class($status);

$renderDetailText = static function (?string $value): string {
    $value = trim((string) $value);

    if ($value === '') {
        return '<span class="text-muted">-</span>';
    }

    if (preg_match('/<[a-z][\s\S]*>/i', $value) === 1) {
        $allowedTags = '<p><br><strong><b><em><i><u><s><ol><ul><li><h1><h2><h3><blockquote>';
        $html = strip_tags($value, $allowedTags);
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/\s+(href|src)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;

        return '<div class="ql-editor instrument-detail-rich">' . $html . '</div>';
    }

    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $lines = array_values(array_filter(array_map('trim', explode("\n", $value)), static fn(string $line): bool => $line !== ''));
    $isNumberedList = $lines !== [] && count($lines) === count(array_filter($lines, static fn(string $line): bool => preg_match('/^\d+\.\s+/', $line) === 1));

    if ($isNumberedList) {
        $items = array_map(static function (string $line): string {
            $line = preg_replace('/^\d+\.\s+/', '', $line) ?? $line;
            return '<li>' . esc($line) . '</li>';
        }, $lines);

        return '<ol class="instrument-detail-list">' . implode('', $items) . '</ol>';
    }

    $paragraphs = preg_split('/\n{2,}/', $value) ?: [];
    $html = [];

    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);

        if ($paragraph !== '') {
            $html[] = '<p>' . nl2br(esc($paragraph)) . '</p>';
        }
    }

    return '<div class="instrument-detail-prose">' . implode('', $html) . '</div>';
};
?>

<div class="instrument-detail-shell">
<div class="page-header d-print-none mb-3 instrument-detail-page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Detail Instrumen</h2>
            <div class="text-muted mt-1">Informasi lengkap instrumen untuk proses validasi penelitian.</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="d-flex flex-wrap justify-content-end gap-2 instrument-header-actions">
                <a href="<?= base_url('admin/instruments/' . $instrumentId . '/edit') ?>" class="btn btn-warning">Edit</a>
                <a href="<?= base_url('admin/instrument-aspects?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                    Kelola Kisi-Kisi
                </a>
                <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                    Kelola Butir
                </a>
                <?php if (($currentInstrument['status'] ?? '') === 'Valid'): ?>
                    <a href="<?= base_url('admin/instrumen-valid') ?>" class="btn btn-light">
                        Lihat Instrumen Valid
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('admin/instruments') ?>" class="btn btn-light">Kembali</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 instrument-detail-card">
    <div class="card-body">
        <h3 class="card-title mb-3">Identitas Instrumen</h3>

        <div class="table-responsive">
            <table class="table table-vcenter table-sm instrument-meta-table">
                <tbody>
                    <tr>
                        <th style="width: 240px;">Kode Instrumen</th>
                        <td class="fw-semibold"><?= esc((string) ($currentInstrument['kode'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Judul Instrumen</th>
                        <td><?= esc((string) ($currentInstrument['judul'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Instrumen</th>
                        <td><?= esc(title_case_label((string) ($currentInstrument['jenis'] ?? '-'))) ?></td>
                    </tr>
                    <tr>
                        <th>Sasaran</th>
                        <td><?= esc((string) (!empty($currentInstrument['sasaran']) ? $currentInstrument['sasaran'] : '-')) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="<?= esc($statusClass) ?>">
                                <?= esc($statusLabel) ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-3 instrument-detail-card">
    <div class="card-body">
        <h3 class="card-title mb-3">Pengantar dan Petunjuk</h3>

        <div class="instrument-document-section">
            <h4>1. Pengantar</h4>
            <?= $renderDetailText($currentInstrument['pengantar'] ?? '') ?>
        </div>

        <div class="instrument-document-section">
            <h4>2. Petunjuk</h4>
            <?= $renderDetailText($currentInstrument['petunjuk'] ?? '') ?>
        </div>

        <div class="instrument-document-section mb-0">
            <h4>Skala Penilaian</h4>
            <div class="table-responsive">
                <table class="table table-sm instrument-scale-table">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (sivalid_scale_options($currentInstrument) as $option): ?>
                            <tr>
                                <td><?= esc((string) ($option['score'] ?? '-')) ?></td>
                                <td><?= esc((string) ($option['label'] ?? '-')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="instrument-note">Bagian ini menampilkan naskah instrumen sebagaimana disiapkan peneliti sebelum digunakan pada proses validasi atau pengumpulan data.</div>
    </div>
</div>

</div>

<style>
    .instrument-detail-shell {
        width: 100%;
        max-width: 1180px;
        margin-left: auto;
        margin-right: auto;
        font-size: 14px;
    }

    .instrument-detail-page-header {
        width: 100%;
    }

    .instrument-detail-shell table,
    .instrument-detail-shell th,
    .instrument-detail-shell td,
    .instrument-detail-shell p,
    .instrument-detail-shell li,
    .instrument-detail-shell .badge,
    .instrument-detail-shell .btn {
        font-size: 14px !important;
    }

    .instrument-detail-card > .card-body {
        padding: 1.25rem 1.35rem;
    }

    .instrument-detail-card .card-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 700;
    }

    .instrument-detail-card {
        font-size: 14px;
    }

    .instrument-meta-table {
        font-size: 14px;
    }

    .instrument-meta-table th {
        width: 230px;
        color: #334155;
        font-weight: 700;
        vertical-align: top;
        background: #f8fafc;
    }

    .instrument-meta-table td {
        color: #0f172a;
        line-height: 1.55;
    }

    .instrument-meta-table th,
    .instrument-meta-table td {
        padding: .82rem .9rem !important;
    }

    .instrument-document-section {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.35rem;
        padding-bottom: 1.35rem;
    }

    .instrument-document-section h4 {
        margin: 0 0 0.85rem;
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
    }

    .instrument-scale-table {
        max-width: 460px;
        margin-bottom: 0;
        font-size: 14px;
        border: 1px solid #e2e8f0;
    }

    .instrument-scale-table th,
    .instrument-scale-table td {
        text-align: center;
    }

    .instrument-detail-prose {
        max-width: 1040px;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.8;
    }

    .instrument-detail-rich {
        max-width: 1040px;
        padding: 0;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.8;
        white-space: normal;
    }

    .instrument-detail-rich .ql-align-center {
        text-align: center;
    }

    .instrument-detail-rich .ql-align-right {
        text-align: right;
    }

    .instrument-detail-rich .ql-align-justify {
        text-align: justify;
        text-justify: inter-word;
    }

    .instrument-detail-rich p,
    .instrument-detail-rich ol,
    .instrument-detail-rich ul,
    .instrument-detail-rich blockquote {
        margin-bottom: 0.75rem;
    }

    .instrument-detail-rich p:last-child,
    .instrument-detail-rich ol:last-child,
    .instrument-detail-rich ul:last-child,
    .instrument-detail-rich blockquote:last-child {
        margin-bottom: 0;
    }

    .instrument-detail-rich ol,
    .instrument-detail-rich ul {
        padding-left: 1.75rem;
    }

    .instrument-detail-rich ol {
        counter-reset: instrument-list-0 instrument-list-1 instrument-list-2;
    }

    .instrument-detail-rich ol > li {
        list-style: none;
        position: relative;
        counter-increment: instrument-list-0;
        padding-left: 1.65rem;
    }

    .instrument-detail-rich ol > li::before {
        content: counter(instrument-list-0, decimal) ".";
        position: absolute;
        left: 0;
        color: #0f172a;
    }

    .instrument-detail-rich ol > li.ql-indent-1 {
        counter-increment: instrument-list-1;
        margin-left: 1.75rem;
    }

    .instrument-detail-rich ol > li.ql-indent-1::before {
        content: counter(instrument-list-1, lower-alpha) ".";
    }

    .instrument-detail-rich ol > li.ql-indent-2 {
        counter-increment: instrument-list-2;
        margin-left: 3.5rem;
    }

    .instrument-detail-rich ol > li.ql-indent-2::before {
        content: counter(instrument-list-2, lower-roman) ".";
    }

    .instrument-detail-rich ul > li.ql-indent-1 {
        margin-left: 1.75rem;
    }

    .instrument-detail-rich ul > li.ql-indent-2 {
        margin-left: 3.5rem;
    }

    .instrument-detail-rich li {
        padding-left: 0.35rem;
        margin-bottom: 0.45rem;
    }

    .instrument-detail-rich li:last-child {
        margin-bottom: 0;
    }

    .instrument-detail-prose p {
        margin: 0 0 0.9rem;
    }

    .instrument-detail-prose p:last-child {
        margin-bottom: 0;
    }

    .instrument-detail-list {
        max-width: 1040px;
        margin: 0;
        padding-left: 1.75rem;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.8;
    }

    .instrument-detail-list li {
        padding-left: 0.35rem;
        margin-bottom: 0.45rem;
    }

    .instrument-detail-list li:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .instrument-meta-table th,
        .instrument-meta-table td {
            display: block;
            width: 100% !important;
        }

        .instrument-meta-table th {
            padding-bottom: 0.25rem;
            border-bottom: 0;
        }

        .instrument-meta-table td {
            padding-top: 0.25rem;
        }
    }

    .instrument-note {
        border-top: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.55;
        margin-top: 1.35rem;
        padding-top: .95rem;
    }

    .instrument-header-actions {
        max-width: 760px;
    }
</style>

<?= $this->endSection() ?>
