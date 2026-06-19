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

if (! function_exists('sivalid_scale_templates')) {
    function sivalid_scale_templates(): array
    {
        return [
            'relevance_4' => [
                'label' => 'Skala Relevansi 1-4',
                'min' => 1,
                'max' => 4,
                'labels' => [
                    1 => 'Tidak Relevan',
                    2 => 'Kurang Relevan',
                    3 => 'Cukup Relevan',
                    4 => 'Sangat Relevan',
                ],
            ],
            'agreement_2' => [
                'label' => 'Skala Setuju/Tidak Setuju',
                'min' => 1,
                'max' => 2,
                'labels' => [
                    1 => 'Tidak Setuju (TS)',
                    2 => 'Setuju (S)',
                ],
            ],
        ];
    }
}

if (! function_exists('sivalid_scale_labels')) {
    function sivalid_scale_labels(array $row): array
    {
        $raw = trim((string) ($row['skala_labels'] ?? ''));

        if ($raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $labels = [];
                foreach ($decoded as $score => $label) {
                    $score = (int) $score;
                    $label = trim((string) $label);
                    if ($score > 0 && $label !== '') {
                        $labels[$score] = $label;
                    }
                }

                if ($labels !== []) {
                    ksort($labels);
                    return $labels;
                }
            }
        }

        $min = isset($row['skala_min']) ? (int) $row['skala_min'] : 1;
        $max = isset($row['skala_max']) ? (int) $row['skala_max'] : 4;

        if ($min === 1 && $max === 2) {
            return sivalid_scale_templates()['agreement_2']['labels'];
        }

        if ($min === 1 && $max === 4) {
            return sivalid_scale_templates()['relevance_4']['labels'];
        }

        $labels = [];
        foreach (range($min, max($min, $max)) as $score) {
            $labels[(int) $score] = 'Skor ' . $score;
        }

        return $labels;
    }
}

if (! function_exists('sivalid_scale_options')) {
    function sivalid_scale_options(array $row): array
    {
        $min = isset($row['skala_min']) ? (int) $row['skala_min'] : 1;
        $max = isset($row['skala_max']) ? (int) $row['skala_max'] : 4;

        if ($min <= 0) {
            $min = 1;
        }

        if ($max < $min) {
            $max = $min;
        }

        $labels = sivalid_scale_labels(['skala_min' => $min, 'skala_max' => $max] + $row);
        $options = [];

        foreach (range($min, $max) as $score) {
            $options[] = [
                'score' => (int) $score,
                'label' => $labels[(int) $score] ?? ('Skor ' . $score),
            ];
        }

        return $options;
    }
}
