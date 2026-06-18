<?php
$adminName = (string) (session()->get('user_name') ?? 'Admin SIVALID');
$adminEmail = (string) (session()->get('user_email') ?? 'Administrator');
$nameParts = preg_split('/\s+/', trim($adminName));
$avatarInitials = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? 'S', 0, 1));
?>

<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">

        <!-- Judul halaman -->
        <div class="navbar-brand pe-0 pe-md-3">
            <span class="fw-semibold text-dark" style="font-size:15px;">
                <?= esc($title ?? 'Dashboard') ?>
            </span>
        </div>

        <div class="navbar-nav flex-row ms-auto align-items-center">

            <div class="nav-item dropdown">
                <a
                    href="#"
                    class="nav-link d-flex lh-1 text-reset p-0 topbar-user-toggle"
                    data-bs-toggle="dropdown"
                    aria-label="Buka menu profil"
                    aria-expanded="false"
                >
                    <span class="avatar avatar-sm topbar-avatar">
                        <?= esc($avatarInitials) ?>
                    </span>
                    <div class="d-none d-xl-block ps-2 text-start">
                        <div class="topbar-user-name"><?= esc($adminName) ?></div>
                        <div class="topbar-user-role">Administrator</div>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow topbar-user-menu">
                    <div class="dropdown-header">
                        <div class="fw-semibold"><?= esc($adminName) ?></div>
                        <div class="small text-muted"><?= esc($adminEmail) ?></div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form action="<?= base_url('logout') ?>" method="post" class="m-0" onsubmit="return confirm('Yakin ingin logout?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="dropdown-item text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 8v-2a2 2 0 0 0-2-2h-7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-2"/>
                                <path d="M9 12h12l-3-3"/>
                                <path d="M18 15l3-3"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
