<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()
                ->to(base_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $allowedRoles = array_map(
            static fn($role): string => strtolower(trim((string) $role)),
            (array) ($arguments ?? [])
        );

        if ($allowedRoles === []) {
            return null;
        }

        $role = strtolower(trim((string) (session()->get('user_role') ?? 'admin')));

        if (! in_array($role, $allowedRoles, true)) {
            return redirect()
                ->to(base_url('admin/dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan.
    }
}
