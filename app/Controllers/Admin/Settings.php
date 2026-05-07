<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Pengaturan',
            'profile'  => $this->settingModel->getGroupValues('profile'),
            'category' => $this->settingModel->getGroupValues('category'),
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
                ->back()
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
            ->to(base_url('admin/settings'))
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
            ->to(base_url('admin/settings'))
            ->with('success', 'Pengaturan kategori kelayakan berhasil disimpan.');
    }
}
