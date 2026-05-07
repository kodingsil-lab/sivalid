<header class="topbar">
    <div class="topbar-title">
        <?= esc($title ?? 'Dashboard') ?>
    </div>

    <div class="topbar-user">
        <?= esc(session()->get('user_name') ?? 'Admin') ?>
        <a href="<?= base_url('logout') ?>" onclick="return confirm('Yakin ingin logout?')">
            Logout
        </a>
    </div>
</header>