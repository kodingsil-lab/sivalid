<header class="navbar navbar-expand-md navbar-light d-print-none" style="border-bottom:1px solid #e2e8f0; background:#fff;">
    <div class="container-xl">

        <!-- Judul halaman -->
        <div class="navbar-brand pe-0 pe-md-3">
            <span class="fw-semibold text-dark" style="font-size:15px;">
                <?= esc($title ?? 'Dashboard') ?>
            </span>
        </div>

        <div class="navbar-nav flex-row ms-auto align-items-center gap-3">

            <!-- Nama admin -->
            <span class="d-none d-md-inline text-muted" style="font-size:13.5px;">
                <?= esc(session()->get('user_name') ?? 'Admin') ?>
            </span>

            <!-- Tombol logout -->
            <a href="<?= base_url('logout') ?>"
               class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Yakin ingin logout?')"
               style="font-size:13px; padding:5px 12px;">
                Logout
            </a>

        </div>
    </div>
</header>
