<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\ResearchProductModel;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected SettingModel $settingModel;
    protected InstrumentModel $instrumentModel;
    protected ResearchProductModel $productModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->instrumentModel = new InstrumentModel();
        $this->productModel = new ResearchProductModel();
    }

    public function index()
    {
        $activeTab = trim((string) $this->request->getGet('tab'));
        $allowedTabs = ['profile', 'category', 'instrument-types', 'product-types', 'system'];

        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'profile';
        }

        $instrumentTypes = $this->settingModel->getGroupRows('instrument_type');
        if ($instrumentTypes === []) {
            $this->seedDefaultInstrumentTypes();
            $instrumentTypes = $this->settingModel->getGroupRows('instrument_type');
        }

        $productTypes = $this->settingModel->getGroupRows('product_type');
        if ($productTypes === []) {
            $this->seedDefaultProductTypes();
            $productTypes = $this->settingModel->getGroupRows('product_type');
        }

        $instrumentTypeUsage = [];
        foreach ($this->instrumentModel->select('jenis, COUNT(*) as total')->groupBy('jenis')->findAll() as $row) {
            $instrumentTypeUsage[(string) ($row['jenis'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        $productTypeUsage = [];
        foreach ($this->productModel->select('jenis_produk, COUNT(*) as total')->groupBy('jenis_produk')->findAll() as $row) {
            $productTypeUsage[(string) ($row['jenis_produk'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        $data = [
            'title'    => 'Pengaturan',
            'activeTab' => $activeTab,
            'profile'  => $this->settingModel->getGroupValues('profile'),
            'category' => $this->settingModel->getGroupValues('category'),
            'instrumentTypes' => $instrumentTypes,
            'productTypes' => $productTypes,
            'instrumentTypeUsage' => $instrumentTypeUsage,
            'productTypeUsage' => $productTypeUsage,
        ];

        return view('admin/settings/index', $data);
    }

    public function saveProfile()
    {
        $rules = [
            'nama_penelitian'  => 'required|max_length[255]',
            'nama_peneliti'    => 'permit_empty|max_length[150]',
            'institusi'        => 'permit_empty|max_length[150]',
            'program_studi'    => 'permit_empty|max_length[150]',
            'tahun_penelitian' => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/settings?tab=profile'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fields = [
            'nama_penelitian',
            'nama_peneliti',
            'institusi',
            'program_studi',
            'tahun_penelitian',
        ];

        foreach ($fields as $field) {
            $this->settingModel->setValue(
                $field,
                trim((string) $this->request->getPost($field)),
                'profile'
            );
        }

        return redirect()
            ->to(base_url('admin/settings?tab=profile'))
            ->with('success', 'Profil penelitian berhasil disimpan.');
    }

    public function saveCategory()
    {
        $fields = [
            'kategori_sangat_layak_min',
            'kategori_layak_min',
            'kategori_kurang_layak_min',
            'kategori_tidak_layak_min',
        ];

        foreach ($fields as $field) {
            $this->settingModel->setValue(
                $field,
                trim((string) $this->request->getPost($field)),
                'category'
            );
        }

        return redirect()
            ->to(base_url('admin/settings?tab=category'))
            ->with('success', 'Pengaturan kategori kelayakan berhasil disimpan.');
    }

    private function seedDefaultInstrumentTypes(): void
    {
        $defaults = [
            'Validasi Instrumen',
            'Validasi Produk',
            'Angket Respon',
            'Observasi',
            'FGD',
            'Tes Kinerja',
        ];

        foreach ($defaults as $index => $label) {
            $this->settingModel->insert([
                'setting_key' => 'instrument_type_default_' . ($index + 1),
                'setting_value' => $label,
                'setting_group' => 'instrument_type',
            ]);
        }
    }

    private function seedDefaultProductTypes(): void
    {
        $defaults = [
            'Buku Model',
            'Buku Ajar',
            'Materi Ajar',
            'Panduan Pembelajaran',
            'E-Learning',
            'Rubrik',
            'Template Artikel',
            'Produk Lainnya',
        ];

        foreach ($defaults as $index => $label) {
            $this->settingModel->insert([
                'setting_key' => 'product_type_default_' . ($index + 1),
                'setting_value' => $label,
                'setting_group' => 'product_type',
            ]);
        }
    }
}
