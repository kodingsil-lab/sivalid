<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('sivalid_setting_value')) {
    function sivalid_setting_value(string $key, ?string $default = null): ?string
    {
        static $cache = [];

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $settingModel = new \App\Models\SettingModel();
            $cache[$key] = $settingModel->getValue($key, $default);
        } catch (\Throwable $e) {
            $cache[$key] = $default;
        }

        return $cache[$key];
    }
}

if (! function_exists('sivalid_asset_url')) {
    function sivalid_asset_url(?string $path, string $defaultPath): string
    {
        $path = trim((string) ($path ?: $defaultPath));

        if ($path === '') {
            $path = $defaultPath;
        }

        if (preg_match('/^https?:\/\//i', $path) === 1) {
            return $path;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');
        $url = base_url($normalizedPath);
        $fullPath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath);

        if (is_file($fullPath)) {
            $url .= '?v=' . filemtime($fullPath);
        }

        return $url;
    }
}

if (! function_exists('sivalid_logo_url')) {
    function sivalid_logo_url(): string
    {
        return sivalid_asset_url(
            sivalid_setting_value('app_logo', 'assets/sivalid copy.png'),
            'assets/sivalid copy.png'
        );
    }
}

if (! function_exists('sivalid_favicon_url')) {
    function sivalid_favicon_url(): string
    {
        return sivalid_asset_url(
            sivalid_setting_value('app_favicon', 'assets/sivalid copy.png'),
            'assets/sivalid copy.png'
        );
    }
}
