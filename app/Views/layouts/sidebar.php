<?php
$currentUri = service('uri')->getPath();

function is_active_menu(string $path, string $currentUri): string
{
    return str_starts_with($currentUri, $path) ? 'active' : '';
}
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>SIVALID</h2>
        <small>Sistem Informasi Validasi Instrumen Penelitian</small>
    </div>

    <nav class="sidebar-menu">
        <a href="<?= base_url('admin/dashboard') ?>" class="<?= is_active_menu('admin/dashboard', $currentUri) ?>">
            Dashboard
        </a>

        <a href="<?= base_url('admin/instruments') ?>" class="<?= is_active_menu('admin/instruments', $currentUri) ?>">
            Master Instrumen
        </a>

        <a href="<?= base_url('admin/instrument-aspects') ?>" class="<?= is_active_menu('admin/instrument-aspects', $currentUri) ?>">
            Kisi-Kisi Instrumen
        </a>

        <a href="<?= base_url('admin/instrument-items') ?>" class="<?= is_active_menu('admin/instrument-items', $currentUri) ?>">
            Butir Instrumen
        </a>

        <a href="<?= base_url('admin/instrument-revisions') ?>" class="<?= is_active_menu('admin/instrument-revisions', $currentUri) ?>">
            Revisi Butir
        </a>

        <a href="<?= base_url('admin/instrumen-valid') ?>" class="<?= is_active_menu('admin/instrumen-valid', $currentUri) ?>">
            Instrumen Valid
        </a>

        <a href="<?= base_url('admin/instrument-links') ?>" class="<?= is_active_menu('admin/instrument-links', $currentUri) ?>">
            Validasi Instrumen
        </a>

        <a href="<?= base_url('admin/products') ?>" class="<?= is_active_menu('admin/products', $currentUri) ?>">
            Produk Penelitian
        </a>

        <a href="<?= base_url('admin/validasi-produk') ?>" class="<?= is_active_menu('admin/validasi-produk', $currentUri) ?>">
            Validasi Produk
        </a>

        <a href="<?= base_url('admin/respondent-links') ?>" class="<?= is_active_menu('admin/respondent-links', $currentUri) ?>">
            Link Responden
        </a>

        <a href="<?= base_url('admin/submissions') ?>" class="<?= is_active_menu('admin/submissions', $currentUri) ?>">
            Hasil Pengisian
        </a>

        <a href="<?= base_url('admin/reports') ?>" class="<?= is_active_menu('admin/reports', $currentUri) ?>">
            Laporan
        </a>

        <a href="<?= base_url('admin/settings') ?>" class="<?= is_active_menu('admin/settings', $currentUri) ?>">
            Pengaturan
        </a>
    </nav>
</aside>
