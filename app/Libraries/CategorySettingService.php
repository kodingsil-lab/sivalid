<?php

namespace App\Libraries;

use App\Models\SettingModel;

class CategorySettingService
{
    protected SettingModel $settingModel;
    protected ?array $thresholds = null;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function thresholds(): array
    {
        if ($this->thresholds !== null) {
            return $this->thresholds;
        }

        $settings = $this->settingModel->getGroupValues('category');

        $sangat = $this->readPercent($settings['kategori_sangat_layak_min'] ?? null, 85);
        $layak  = $this->readPercent($settings['kategori_layak_min'] ?? null, 70);
        $kurang = $this->readPercent($settings['kategori_kurang_layak_min'] ?? null, 55);
        $tidak  = $this->readPercent($settings['kategori_tidak_layak_min'] ?? null, 0);

        if ($kurang < $tidak) {
            $kurang = $tidak;
        }

        if ($layak < $kurang) {
            $layak = $kurang;
        }

        if ($sangat < $layak) {
            $sangat = $layak;
        }

        $this->thresholds = [
            'sangat_layak_min' => $sangat,
            'layak_min'        => $layak,
            'kurang_layak_min' => $kurang,
            'tidak_layak_min'  => $tidak,
        ];

        return $this->thresholds;
    }

    public function classify(float $percentage, array $labels): string
    {
        $thresholds = $this->thresholds();

        if ($percentage >= $thresholds['sangat_layak_min']) {
            return $labels['sangat'];
        }

        if ($percentage >= $thresholds['layak_min']) {
            return $labels['layak'];
        }

        if ($percentage >= $thresholds['kurang_layak_min']) {
            return $labels['kurang'];
        }

        return $labels['tidak'];
    }

    private function readPercent($value, int $default): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $percent = (int) $value;

        if ($percent < 0) {
            return 0;
        }

        if ($percent > 100) {
            return 100;
        }

        return $percent;
    }
}
