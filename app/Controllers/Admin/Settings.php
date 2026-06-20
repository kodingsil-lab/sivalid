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
        $allowedTabs = ['category', 'instrument-types', 'product-types', 'application', 'system'];

        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'category';
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
            'category' => $this->settingModel->getGroupValues('category'),
            'application' => $this->settingModel->getGroupValues('application'),
            'instrumentTypes' => $instrumentTypes,
            'productTypes' => $productTypes,
            'instrumentTypeUsage' => $instrumentTypeUsage,
            'productTypeUsage' => $productTypeUsage,
        ];

        return view('admin/settings/index', $data);
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

    public function saveApplication()
    {
        $rules = [];
        $logo = $this->request->getFile('app_logo');
        $favicon = $this->request->getFile('app_favicon');

        if ($logo && $logo->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['app_logo'] = 'uploaded[app_logo]|max_size[app_logo,2048]|ext_in[app_logo,png,jpg,jpeg,gif,webp,svg]';
        }

        if ($favicon && $favicon->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['app_favicon'] = 'uploaded[app_favicon]|max_size[app_favicon,1024]|ext_in[app_favicon,ico,png,jpg,jpeg,gif,webp,svg]';
        }

        if ($rules !== [] && ! $this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/settings?tab=application'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            $this->saveBrandingFile($logo, 'app_logo', 'logo', 'application');
        }

        if ($favicon && $favicon->isValid() && ! $favicon->hasMoved()) {
            $this->saveBrandingFile($favicon, 'app_favicon', 'favicon', 'application');
        }

        if ((! $logo || $logo->getError() === UPLOAD_ERR_NO_FILE) && (! $favicon || $favicon->getError() === UPLOAD_ERR_NO_FILE)) {
            $this->settingModel->setValue('app_logo', 'assets/sivalid copy.png', 'application');
            $this->settingModel->setValue('app_favicon', 'assets/sivalid copy.png', 'application');
        }

        return redirect()
            ->to(base_url('admin/settings?tab=application'))
            ->with('success', 'Pengaturan aplikasi berhasil disimpan.');
    }

    private function saveBrandingFile(\CodeIgniter\HTTP\Files\UploadedFile $file, string $settingKey, string $prefix, string $group): void
    {
        $targetDir = FCPATH . 'uploads/settings';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $oldPath = (string) ($this->settingModel->getValue($settingKey) ?? '');
        $extension = $file->getClientExtension() ?: $file->guessExtension();
        $fileName = $prefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.' . strtolower($extension);
        $file->move($targetDir, $fileName);

        $newPath = 'uploads/settings/' . $fileName;
        $this->settingModel->setValue($settingKey, $newPath, $group);

        if ($oldPath !== '' && str_starts_with($oldPath, 'uploads/settings/')) {
            $oldFullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $oldPath);
            if (is_file($oldFullPath)) {
                unlink($oldFullPath);
            }
        }
    }

    private function seedDefaultInstrumentTypes(): void
    {
        $defaults = [
            'Angket',
            'Wawancara',
            'Observasi',
            'FGD',
            'Tes Kinerja',
            'Rubrik Penilaian',
            'Dokumentasi',
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
