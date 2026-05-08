<?php
$currentUri = trim(service('uri')->getPath(), '/');

if (! function_exists('sivalid_sidebar_normalize_uri')) {
    function sivalid_sidebar_normalize_uri(string $uri): string
    {
        $uri = trim($uri, '/');
        $adminPosition = strpos($uri, 'admin/');

        return $adminPosition !== false ? substr($uri, $adminPosition) : $uri;
    }
}

if (! function_exists('is_active_menu')) {
    function is_active_menu(string $path, string $currentUri): string
    {
        $path = trim($path, '/');
        $currentUri = sivalid_sidebar_normalize_uri($currentUri);

        return ($currentUri === $path || str_starts_with($currentUri, $path . '/')) ? 'active' : '';
    }
}

if (! function_exists('sivalid_sidebar_group_active')) {
    function sivalid_sidebar_group_active(array $items, string $currentUri): bool
    {
        foreach ($items as $item) {
            if (is_active_menu($item['path'], $currentUri) === 'active') {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('sivalid_sidebar_icon')) {
    function sivalid_sidebar_icon(string $name): string
    {
        $icons = [
            'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
            'file' => '<path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>',
            'table' => '<rect x="3" y="5" width="18" height="14" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="10" x2="10" y2="19"/>',
            'list' => '<line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="5" cy="6" r="1" fill="currentColor"/><circle cx="5" cy="12" r="1" fill="currentColor"/><circle cx="5" cy="18" r="1" fill="currentColor"/>',
            'check' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
            'edit' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
            'circle-check' => '<circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/>',
            'box' => '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/><line x1="12" y1="12" x2="20" y2="7.5"/><line x1="12" y1="12" x2="12" y2="21"/><line x1="12" y1="12" x2="4" y2="7.5"/>',
            'link' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
            'chart' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        ];

        return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">' . ($icons[$name] ?? $icons['file']) . '</svg>';
    }
}

if (! function_exists('sivalid_sidebar_link')) {
    function sivalid_sidebar_link(array $item, string $currentUri): void
    {
        ?>
        <li class="nav-item">
            <a class="nav-link <?= is_active_menu((string) $item['path'], $currentUri) ?>"
               href="<?= base_url((string) $item['path']) ?>">
                <span class="nav-link-icon">
                    <?= sivalid_sidebar_icon((string) $item['icon']) ?>
                </span>
                <span class="nav-link-title"><?= esc((string) $item['label']) ?></span>
            </a>
        </li>
        <?php
    }
}

$instrumentMenus = [
    ['path' => 'admin/instruments', 'label' => 'Master Instrumen', 'icon' => 'file'],
    ['path' => 'admin/instrument-aspects', 'label' => 'Kisi-Kisi Instrumen', 'icon' => 'table'],
    ['path' => 'admin/instrument-items', 'label' => 'Butir Instrumen', 'icon' => 'list'],
    ['path' => 'admin/validasi-instrumen', 'label' => 'Validasi Instrumen', 'icon' => 'check'],
    ['path' => 'admin/instrument-revisions', 'label' => 'Revisi Butir', 'icon' => 'edit'],
    ['path' => 'admin/instrumen-valid', 'label' => 'Instrumen Valid', 'icon' => 'circle-check'],
];

$productMenus = [
    ['path' => 'admin/products', 'label' => 'Produk Penelitian', 'icon' => 'box'],
    ['path' => 'admin/validasi-produk', 'label' => 'Validasi Produk', 'icon' => 'check'],
];

$fillingMenus = [
    ['path' => 'admin/respondent-links', 'label' => 'Link Responden', 'icon' => 'link'],
    ['path' => 'admin/submissions', 'label' => 'Hasil Pengisian', 'icon' => 'table'],
    ['path' => 'admin/reports', 'label' => 'Laporan', 'icon' => 'chart'],
];

$instrumentGroup = ['label' => 'Instrumen', 'icon' => 'file', 'items' => $instrumentMenus];
$directGroups = [
    ['label' => 'Produk', 'items' => $productMenus],
    ['label' => 'Pengisian', 'items' => $fillingMenus],
    ['label' => 'Sistem', 'items' => [
        ['path' => 'admin/settings', 'label' => 'Pengaturan', 'icon' => 'settings'],
    ]],
];
?>

<aside class="navbar navbar-vertical navbar-expand-lg navbar-light">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a href="<?= base_url('admin/dashboard') ?>" class="navbar-brand">
            <span class="brand-name">SIVALID</span>
            <span class="brand-sub">Sistem Validasi Instrumen</span>
        </a>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3 pb-3">
                <?php
                sivalid_sidebar_link([
                    'path' => 'admin/dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'dashboard',
                ], $currentUri);
                ?>

                <?php $isInstrumentActive = sivalid_sidebar_group_active($instrumentGroup['items'], $currentUri); ?>
                <li class="nav-item nav-group">
                    <details class="nav-group-details <?= $isInstrumentActive ? 'is-active' : '' ?>" <?= $isInstrumentActive ? 'open' : '' ?>>
                        <summary class="nav-group-toggle">
                            <span class="nav-link-icon">
                                <?= sivalid_sidebar_icon($instrumentGroup['icon']) ?>
                            </span>
                            <span class="nav-link-title"><?= esc($instrumentGroup['label']) ?></span>
                            <span class="nav-group-chevron" aria-hidden="true"></span>
                        </summary>

                        <ul class="nav-group-menu">
                            <?php foreach ($instrumentGroup['items'] as $item): ?>
                                <?php sivalid_sidebar_link($item, $currentUri); ?>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                </li>

                <?php foreach ($directGroups as $group): ?>
                    <li class="nav-item mt-1">
                        <span class="nav-section-label">
                            <?= esc($group['label']) ?>
                        </span>
                    </li>

                    <?php foreach ($group['items'] as $item): ?>
                        <?php sivalid_sidebar_link($item, $currentUri); ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</aside>
