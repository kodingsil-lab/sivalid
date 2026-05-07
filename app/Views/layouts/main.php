<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'SIVALID') ?> - SIVALID</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: #1f2937;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 18px 16px;
            border-bottom: 1px solid #374151;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .sidebar-header small {
            display: block;
            margin-top: 4px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.4;
        }

        .sidebar-menu {
            padding: 12px 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 11px 16px;
            color: #e5e7eb;
            text-decoration: none;
            font-size: 14px;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover {
            background: #374151;
            border-left-color: #93c5fd;
        }

        .sidebar-menu a.active {
            background: #111827;
            border-left-color: #60a5fa;
            font-weight: bold;
        }

        .main-area {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 22px;
            box-sizing: border-box;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        .topbar-user {
            font-size: 14px;
            color: #555;
        }

        .topbar-user a {
            color: #9f1c1c;
            text-decoration: none;
            margin-left: 12px;
        }

        .content {
            padding: 24px;
        }

        .page-title {
            margin: 0 0 18px;
            font-size: 24px;
        }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            padding: 18px;
            margin-bottom: 18px;
            box-sizing: border-box;
        }

        .card h3 {
            margin-top: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #ddd;
            padding: 16px;
            box-sizing: border-box;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .stat-card .label {
            font-size: 13px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 9px;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #f1f5f9;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            border: 1px solid #ccc;
            background: #f8fafc;
        }

        .badge-valid {
            background: #eaf7ea;
            border-color: #b9e3b9;
            color: #236b23;
        }

        .badge-warning {
            background: #fff7e6;
            border-color: #f4d38a;
            color: #92400e;
        }

        .badge-danger {
            background: #fdecea;
            border-color: #f5c2c0;
            color: #9f1c1c;
        }

        .badge-status-draft {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #374151;
        }

        .badge-status-process {
            background: #e0f2fe;
            border-color: #7dd3fc;
            color: #075985;
        }

        .badge-status-warning {
            background: #fff7ed;
            border-color: #fdba74;
            color: #9a3412;
        }

        .badge-status-success {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .badge-status-danger {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            align-items: center;
        }

        .search-form {
            display: flex;
            gap: 8px;
        }

        .search-form input {
            padding: 8px;
            border: 1px solid #bbb;
            min-width: 260px;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #bbb;
            background: #fff;
            color: #222;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary {
            background: #1f4e79;
            border-color: #1f4e79;
            color: #fff;
        }

        .btn-warning {
            background: #f59e0b;
            border-color: #f59e0b;
            color: #fff;
        }

        .btn-danger {
            background: #b91c1c;
            border-color: #b91c1c;
            color: #fff;
        }

        .btn-light {
            background: #f8fafc;
            color: #222;
        }

        .form-row {
            margin-bottom: 14px;
        }

        .form-row label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 9px;
            border: 1px solid #bbb;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea.form-control {
            min-height: 90px;
            resize: vertical;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 14px;
            font-size: 14px;
            border: 1px solid transparent;
        }

        .alert-error {
            background: #fdecea;
            color: #9f1c1c;
            border-color: #f5c2c0;
        }

        .alert-success {
            background: #eaf7ea;
            color: #236b23;
            border-color: #b9e3b9;
        }

        .action-inline {
            display: inline;
        }

        .empty-state {
            padding: 24px;
            text-align: center;
            color: #666;
            background: #fff;
            border: 1px solid #ddd;
        }

        @media (max-width: 900px) {
            .app-wrapper {
                display: block;
            }

            .sidebar {
                width: 100%;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .toolbar {
                display: block;
            }

            .search-form {
                margin-top: 10px;
            }
        }

        @media (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                height: auto;
                padding: 12px;
                display: block;
            }

            .topbar-user {
                margin-top: 6px;
            }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <?= $this->include('layouts/sidebar') ?>

    <div class="main-area">
        <?= $this->include('layouts/topbar') ?>

        <main class="content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

</body>
</html>
