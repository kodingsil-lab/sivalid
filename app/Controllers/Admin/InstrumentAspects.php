<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;

class InstrumentAspects extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentAspectModel $aspectModel;
    protected InstrumentIndicatorModel $indicatorModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->aspectModel     = new InstrumentAspectModel();
        $this->indicatorModel  = new InstrumentIndicatorModel();
    }

    public function index()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;

        $data = [
            'title'        => 'Kisi-Kisi Instrumen',
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'aspects'      => $this->aspectModel->getWithInstrument($instrumentId),
            'indicators'   => $this->indicatorModel->getWithRelations($instrumentId),
        ];

        return view('admin/aspects/index', $data);
    }

    public function new()
    {
        $instrumentId = $this->request->getGet('instrument_id');

        $data = [
            'title'        => 'Tambah Aspek Instrumen',
            'aspect'       => null,
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'action'       => base_url('admin/instrument-aspects'),
            'method'       => 'post',
        ];

        return view('admin/aspects/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_id' => 'required|integer',
            'nama_aspek'    => 'required|min_length[2]|max_length[200]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->aspectModel->insert([
            'instrument_id' => (int) $this->request->getPost('instrument_id'),
            'nama_aspek'    => trim((string) $this->request->getPost('nama_aspek')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $this->request->getPost('instrument_id')))
            ->with('success', 'Aspek instrumen berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $aspect = $this->aspectModel->find($id);

        if (!$aspect) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data aspek tidak ditemukan.');
        }

        $data = [
            'title'        => 'Edit Aspek Instrumen',
            'aspect'       => $aspect,
            'instrumentId' => $aspect['instrument_id'],
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'action'       => base_url('admin/instrument-aspects/' . $id),
            'method'       => 'put',
        ];

        return view('admin/aspects/form', $data);
    }

    public function update($id = null)
    {
        $aspect = $this->aspectModel->find($id);

        if (!$aspect) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data aspek tidak ditemukan.');
        }

        $rules = [
            'instrument_id' => 'required|integer',
            'nama_aspek'    => 'required|min_length[2]|max_length[200]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->aspectModel->update($id, [
            'instrument_id' => (int) $this->request->getPost('instrument_id'),
            'nama_aspek'    => trim((string) $this->request->getPost('nama_aspek')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $this->request->getPost('instrument_id')))
            ->with('success', 'Aspek instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $aspect = $this->aspectModel->find($id);

        if (!$aspect) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data aspek tidak ditemukan.');
        }

        $instrumentId = $aspect['instrument_id'];

        $this->aspectModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Aspek instrumen berhasil dihapus.');
    }
}