<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use CodeIgniter\HTTP\ResponseInterface;
use ZipArchive;

class Backup extends BaseController
{
    protected AuditLogService $auditLog;

    public function __construct()
    {
        $this->auditLog = new AuditLogService();
    }

    public function index()
    {
        $data = [
            'title' => 'Backup & Restore',
        ];

        return view('admin/backup/index', $data);
    }

    /**
     * Export database ke file SQL menggunakan query dump manual
     * (tanpa dependensi mysqldump CLI agar portabel di shared hosting).
     */
    public function exportDatabase()
    {
        $db     = db_connect();
        $tables = $db->listTables();
        $sql    = '';

        foreach ($tables as $table) {
            // DROP IF EXISTS
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            // CREATE TABLE
            $createRow = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            $createStmt = array_values($createRow)[1];
            $sql .= $createStmt . ";\n\n";

            // INSERT rows
            $rows = $db->table($table)->get()->getResultArray();

            if (!empty($rows)) {
                $cols = '`' . implode('`, `', array_keys($rows[0])) . '`';
                $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES\n";

                $valueLines = [];

                foreach ($rows as $row) {
                    $escaped = array_map(function ($v) use ($db) {
                        if ($v === null) {
                            return 'NULL';
                        }
                        return "'" . $db->escapeStr((string) $v) . "'";
                    }, $row);

                    $valueLines[] = '(' . implode(', ', $escaped) . ')';
                }

                $sql .= implode(",\n", $valueLines) . ";\n\n";
            }
        }

        $filename = 'sivalid-db-' . date('Ymd-His') . '.sql';

        $this->auditLog->log('export_database', null, null, 'Export database: ' . $filename);

        return $this->response
            ->setHeader('Content-Type', 'application/sql')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($sql);
    }

    /**
     * Export semua file produk sebagai ZIP.
     * Membutuhkan ekstensi PHP ZipArchive.
     */
    public function exportFiles()
    {
        if (!class_exists('ZipArchive')) {
            return redirect()
                ->to(base_url('admin/backup'))
                ->with('error', 'Ekstensi ZipArchive tidak tersedia di server ini. Lakukan backup file secara manual via FTP/cPanel.');
        }

        $uploadDir = WRITEPATH . 'uploads/products/';

        if (!is_dir($uploadDir)) {
            return redirect()
                ->to(base_url('admin/backup'))
                ->with('error', 'Direktori upload produk tidak ditemukan.');
        }

        $zipFilename = 'sivalid-files-' . date('Ymd-His') . '.zip';
        $zipPath     = WRITEPATH . 'cache/' . $zipFilename;

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()
                ->to(base_url('admin/backup'))
                ->with('error', 'Gagal membuat file ZIP.');
        }

        $files = glob($uploadDir . '*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $zip->addFile($file, 'products/' . basename($file));
            }
        }

        $zip->close();

        $this->auditLog->log('export_files', null, null, 'Export file produk: ' . $zipFilename);

        $content = file_get_contents($zipPath);
        @unlink($zipPath);

        return $this->response
            ->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $zipFilename . '"')
            ->setBody($content);
    }
}
