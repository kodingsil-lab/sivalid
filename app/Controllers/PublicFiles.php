<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PublicFiles extends BaseController
{
    private array $allowedPrefixes = [
        'uploads/settings/',
        'uploads/instrument-attachments/',
    ];

    public function uploaded(string $encodedPath = '')
    {
        $path = $this->decodePath($encodedPath);

        if ($path === null || !$this->isAllowedPath($path)) {
            return $this->fileNotFound();
        }

        $fullPath = $this->resolveUploadedPath($path);

        if ($fullPath === null) {
            log_message('warning', 'Uploaded file not found: {path}', ['path' => $path]);

            return $this->fileNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(file_get_contents($fullPath));
    }

    private function decodePath(string $encodedPath): ?string
    {
        if ($encodedPath === '') {
            return null;
        }

        $padding = strlen($encodedPath) % 4;
        if ($padding > 0) {
            $encodedPath .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($encodedPath, '-_', '+/'), true);

        if (!is_string($decoded) || $decoded === '') {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $decoded), '/');

        if (str_contains($path, '../') || str_contains($path, '..\\')) {
            return null;
        }

        return $path;
    }

    private function isAllowedPath(string $path): bool
    {
        foreach ($this->allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function resolveUploadedPath(string $path): ?string
    {
        $relative = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $candidates = [
            FCPATH . $relative,
            ROOTPATH . 'public' . DIRECTORY_SEPARATOR . $relative,
            ROOTPATH . $relative,
        ];

        foreach ($candidates as $candidate) {
            $realPath = realpath($candidate);

            if ($realPath !== false && is_file($realPath) && strtolower(pathinfo($realPath, PATHINFO_EXTENSION)) === 'pdf') {
                return $realPath;
            }
        }

        return null;
    }

    private function fileNotFound()
    {
        return $this->response
            ->setStatusCode(404)
            ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setBody('File PDF tidak ditemukan di penyimpanan server.');
    }
}
