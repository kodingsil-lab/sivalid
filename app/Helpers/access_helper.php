<?php

if (! function_exists('current_user_id')) {
    function current_user_id(): int
    {
        return (int) (session()->get('user_id') ?? 0);
    }
}

if (! function_exists('current_user_role')) {
    function current_user_role(): string
    {
        return strtolower(trim((string) (session()->get('user_role') ?? 'admin')));
    }
}

if (! function_exists('is_superadmin')) {
    function is_superadmin(): bool
    {
        return current_user_role() === 'superadmin';
    }
}

if (! function_exists('is_admin_role')) {
    function is_admin_role(?string $role = null): bool
    {
        $role = strtolower(trim((string) ($role ?? current_user_role())));

        return in_array($role, ['superadmin', 'admin'], true);
    }
}

if (! function_exists('apply_owner_scope')) {
    function apply_owner_scope($builder, string $column = 'user_id')
    {
        if (is_superadmin()) {
            return $builder;
        }

        $userId = current_user_id();

        if ($userId <= 0) {
            return $builder->where($column, -1);
        }

        return $builder->where($column, $userId);
    }
}

if (! function_exists('owned_create_data')) {
    function owned_create_data(array $data, string $key = 'user_id'): array
    {
        if (! array_key_exists($key, $data) || (int) ($data[$key] ?? 0) <= 0) {
            $data[$key] = current_user_id() ?: null;
        }

        return $data;
    }
}

if (! function_exists('user_owns_row')) {
    function user_owns_row(?array $row, string $key = 'user_id'): bool
    {
        if (! $row) {
            return false;
        }

        if (is_superadmin()) {
            return true;
        }

        return (int) ($row[$key] ?? 0) === current_user_id();
    }
}

if (! function_exists('ownership_denied_redirect')) {
    function ownership_denied_redirect(string $to = 'admin/dashboard', string $message = 'Data tidak ditemukan atau bukan milik akun Anda.')
    {
        return redirect()
            ->to(base_url($to))
            ->with('error', $message);
    }
}

if (! function_exists('sivalid_global_data_labels')) {
    function sivalid_global_data_labels(): array
    {
        return [
            'app_branding' => 'Logo, favicon, dan nama aplikasi',
            'instrument_types' => 'Jenis instrumen',
            'product_types' => 'Jenis produk',
        ];
    }
}
