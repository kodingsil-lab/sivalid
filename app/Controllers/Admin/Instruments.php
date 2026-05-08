<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\SettingModel;

class Instruments extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));

        $query = $this->instrumentModel;

        if ($keyword !== '') {
            $query = $query
                ->groupStart()
                ->like('kode', $keyword)
                ->orLike('judul', $keyword)
                ->orLike('jenis', $keyword)
                ->orLike('sasaran', $keyword)
                ->orLike('status', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'       => 'Master Instrumen',
            'keyword'     => $keyword,
            'instruments' => $query->orderBy('id', 'DESC')->findAll(),
        ];

        return view('admin/instruments/index', $data);
    }

    public function new()
    {
        $data = [
            'title'      => 'Tambah Instrumen',
            'instrument' => null,
            'jenisOptions' => $this->getJenisOptions(),
            'action'     => base_url('admin/instruments'),
            'method'     => 'post',
        ];

        return view('admin/instruments/form', $data);
    }

    public function create()
    {
        $rules = [
            'kode'      => 'required|min_length[2]|max_length[50]|is_unique[instruments.kode]',
            'judul'     => 'required|min_length[5]|max_length[255]',
            'jenis'     => 'required',
            'skala_min' => 'required|integer',
            'skala_max' => 'required|integer',
            'status'    => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $skalaMin = (int) $this->request->getPost('skala_min');
        $skalaMax = (int) $this->request->getPost('skala_max');

        if ($skalaMin >= $skalaMax) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Skala minimal harus lebih kecil dari skala maksimal.');
        }

        $this->instrumentModel->insert([
            'kode'      => trim((string) $this->request->getPost('kode')),
            'judul'     => trim((string) $this->request->getPost('judul')),
            'jenis'     => trim((string) $this->request->getPost('jenis')),
            'sasaran'   => trim((string) $this->request->getPost('sasaran')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')),
            'pengantar' => trim((string) $this->request->getPost('pengantar')),
            'petunjuk'  => trim((string) $this->request->getPost('petunjuk')),
            'skala_min' => $skalaMin,
            'skala_max' => $skalaMax,
            'status'    => trim((string) $this->request->getPost('status')),
        ]);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil ditambahkan.');
    }

    public function show($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $data = [
            'title'      => 'Detail Instrumen',
            'instrument' => $instrument,
        ];

        return view('admin/instruments/show', $data);
    }

    public function edit($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Instrumen',
            'instrument' => $instrument,
            'jenisOptions' => $this->getJenisOptions(),
            'action'     => base_url('admin/instruments/' . $id),
            'method'     => 'put',
        ];

        return view('admin/instruments/form', $data);
    }

    public function update($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $rules = [
            'kode'      => 'required|min_length[2]|max_length[50]|is_unique[instruments.kode,id,' . $id . ']',
            'judul'     => 'required|min_length[5]|max_length[255]',
            'jenis'     => 'required',
            'skala_min' => 'required|integer',
            'skala_max' => 'required|integer',
            'status'    => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $skalaMin = (int) $this->request->getPost('skala_min');
        $skalaMax = (int) $this->request->getPost('skala_max');

        if ($skalaMin >= $skalaMax) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Skala minimal harus lebih kecil dari skala maksimal.');
        }

        $this->instrumentModel->update($id, [
            'kode'      => trim((string) $this->request->getPost('kode')),
            'judul'     => trim((string) $this->request->getPost('judul')),
            'jenis'     => trim((string) $this->request->getPost('jenis')),
            'sasaran'   => trim((string) $this->request->getPost('sasaran')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')),
            'pengantar' => trim((string) $this->request->getPost('pengantar')),
            'petunjuk'  => trim((string) $this->request->getPost('petunjuk')),
            'skala_min' => $skalaMin,
            'skala_max' => $skalaMax,
            'status'    => trim((string) $this->request->getPost('status')),
        ]);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $this->instrumentModel->delete($id);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil dihapus.');
    }

    private function getJenisOptions(): array
    {
        return $this->settingModel->getInstrumentTypes([
            'Validasi Instrumen',
            'Validasi Produk',
            'Angket Respon',
            'Observasi',
            'FGD',
            'Tes Kinerja',
        ]);
    }
}