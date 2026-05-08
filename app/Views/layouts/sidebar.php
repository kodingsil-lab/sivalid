<?php
$currentUri = service('uri')->getPath();

function is_active_menu(string $path, string $currentUri): string
{
    return str_starts_with($currentUri, $path) ? 'active' : '';
}
?>

<aside class="navbar navbar-vertical navbar-expand-lg navbar-light">
    <div class="container-fluid">

        <!-- Toggle mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Brand -->
        <a href="<?= base_url('admin/dashboard') ?>" class="navbar-brand">
            <span class="brand-name">SIVALID</span>
            <span class="brand-sub">Sistem Validasi Instrumen</span>
        </a>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3 pb-3">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/dashboard', $currentUri) ?>"
                       href="<?= base_url('admin/dashboard') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <span class="nav-section-label">
                        Instrumen
                    </span>
                </li>

                <!-- Master Instrumen -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/instruments', $currentUri) ?>"
                       href="<?= base_url('admin/instruments') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/>
                                <line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Master Instrumen</span>
                    </a>
                </li>

                <!-- Kisi-Kisi -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/instrument-aspects', $currentUri) ?>"
                       href="<?= base_url('admin/instrument-aspects') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="10" x2="10" y2="19"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Kisi-Kisi Instrumen</span>
                    </a>
                </li>

                <!-- Butir Instrumen -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/instrument-items', $currentUri) ?>"
                       href="<?= base_url('admin/instrument-items') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/>
                                <line x1="9" y1="18" x2="20" y2="18"/>
                                <circle cx="5" cy="6" r="1" fill="currentColor"/><circle cx="5" cy="12" r="1" fill="currentColor"/>
                                <circle cx="5" cy="18" r="1" fill="currentColor"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Butir Instrumen</span>
                    </a>
                </li>

                <!-- Validasi Instrumen -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/validasi-instrumen', $currentUri) ?>"
                       href="<?= base_url('admin/validasi-instrumen') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Validasi Instrumen</span>
                    </a>
                </li>

                <!-- Revisi Butir -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/instrument-revisions', $currentUri) ?>"
                       href="<?= base_url('admin/instrument-revisions') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Revisi Butir</span>
                    </a>
                </li>

                <!-- Instrumen Valid -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/instrumen-valid', $currentUri) ?>"
                       href="<?= base_url('admin/instrumen-valid') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Instrumen Valid</span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <span class="nav-section-label">
                        Produk
                    </span>
                </li>

                <!-- Produk Penelitian -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/products', $currentUri) ?>"
                       href="<?= base_url('admin/products') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/>
                                <line x1="12" y1="12" x2="20" y2="7.5"/><line x1="12" y1="12" x2="12" y2="21"/>
                                <line x1="12" y1="12" x2="4" y2="7.5"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Produk Penelitian</span>
                    </a>
                </li>

                <!-- Validasi Produk -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/validasi-produk', $currentUri) ?>"
                       href="<?= base_url('admin/validasi-produk') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Validasi Produk</span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <span class="nav-section-label">
                        Pengisian
                    </span>
                </li>

                <!-- Link Responden -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/respondent-links', $currentUri) ?>"
                       href="<?= base_url('admin/respondent-links') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Link Responden</span>
                    </a>
                </li>

                <!-- Hasil Pengisian -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/submissions', $currentUri) ?>"
                       href="<?= base_url('admin/submissions') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Hasil Pengisian</span>
                    </a>
                </li>

                <!-- Laporan -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/reports', $currentUri) ?>"
                       href="<?= base_url('admin/reports') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Laporan</span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <span class="nav-section-label">
                        Sistem
                    </span>
                </li>

                <!-- Pengaturan -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_menu('admin/settings', $currentUri) ?>"
                       href="<?= base_url('admin/settings') ?>">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </span>
                        <span class="nav-link-title">Pengaturan</span>
                    </a>
                </li>

            </ul>
        </div>

    </div>
</aside>
