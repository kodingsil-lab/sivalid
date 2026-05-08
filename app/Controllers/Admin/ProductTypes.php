<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResearchProductModel;
use App\Models\SettingModel;

class ProductTypes extends BaseController
{
    protected SettingModel $settingModel;
    protected ResearchProductModel $productModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->productModel = new ResearchProductModel();
    }

    public function index()
    {
        $types = $this->settingModel->getGroupRows('product_type');

        if ($types === []) {
            $this->seedDefaultTypes();
            $types = $this->settingModel->getGroupRows('product_type');
        }

        $usage = [];
        foreach ($this->productModel->select('jenis_produk, COUNT(*) as total')->groupBy('jenis_produk')->findAll() as $row) {
            $usage[(string) ($row['jenis_produk'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        return view('admin/product_types/index', [
            'title' => 'Jenis Produk',
            'types' => $types,
            'usage' => $usage,
        ]);
    }

    public function create()
    {
        $rules = [
            'jenis' => 'required|min_length[2]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/settings?tab=product-types'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $label = trim((string) $this->request->getPost('jenis'));

        $existing = $this->settingModel->getProductTypes();
        foreach ($existing as $item) {
            if (mb_strtolower($item) === mb_strtolower($label)) {
                return redirect()
                    ->to(base_url('admin/settings?tab=product-types'))
                    ->with('error', 'Jenis produk sudah ada.');
            }
        }

        $this->settingModel->insert([
            'setting_key' => 'product_type_' . time() . '_' . random_int(100, 999),
            'setting_value' => $label,
            'setting_group' => 'product_type',
        ]);

        return redirect()
            ->to(base_url('admin/settings?tab=product-types'))
            ->with('success', 'Jenis produk berhasil ditambahkan.');
    }

    public function delete($id = null)
    {
        $type = $this->settingModel
            ->where('id', (int) $id)
            ->where('setting_group', 'product_type')
            ->first();

        if (! $type) {
            return redirect()
                ->to(base_url('admin/settings?tab=product-types'))
                ->with('error', 'Jenis produk tidak ditemukan.');
        }

        $label = trim((string) ($type['setting_value'] ?? ''));
        $used = $this->productModel->where('jenis_produk', $label)->countAllResults();

        if ($used > 0) {
            return redirect()
                ->to(base_url('admin/settings?tab=product-types'))
                ->with('error', 'Jenis produk tidak bisa dihapus karena masih dipakai pada data produk.');
        }

        $this->settingModel->delete((int) $type['id']);

        return redirect()
            ->to(base_url('admin/settings?tab=product-types'))
            ->with('success', 'Jenis produk berhasil dihapus.');
    }

    private function seedDefaultTypes(): void
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
