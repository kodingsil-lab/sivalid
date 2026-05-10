<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;

class InstrumentIndicators extends BaseController
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
        $perPage = config('Pager')->perPage;

        $data = [
            'title'        => 'Indikator Kisi-Kisi Instrumen',
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'indicators'   => $this->indicatorModel->paginateWithRelations($instrumentId, $perPage, 'instrument_indicators'),
            'pager'        => $this->indicatorModel->pager,
            'pagerGroup'   => 'instrument_indicators',
        ];

        return view('admin/indicators/index', $data);
    }

    public function new()
    {
        $instrumentId = (int) ($this->request->getGet('instrument_id') ?? 0);

        return redirect()
            ->to(base_url('admin/instrument-aspects' . ($instrumentId > 0 ? '?instrument_id=' . $instrumentId : '')))
            ->with('info', 'Form lama sudah dinonaktifkan. Gunakan popup pada halaman Kisi-Kisi Instrumen.');
    }

    public function create()
    {
        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'indikator'     => 'required|min_length[3]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');

        $aspect = $this->aspectModel->where([
            'id'            => $aspectId,
            'instrument_id' => $instrumentId,
        ])->first();

        if (!$aspect) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Aspek tidak sesuai dengan instrumen yang dipilih.');
        }

        $this->indicatorModel->insert([
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indikator'     => trim((string) $this->request->getPost('indikator')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $indicator = $this->indicatorModel->find($id);

        if (!$indicator) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data indikator tidak ditemukan.');
        }

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $indicator['instrument_id']))
            ->with('info', 'Edit dilakukan melalui popup pada halaman Kisi-Kisi Instrumen.');
    }

    public function update($id = null)
    {
        $indicator = $this->indicatorModel->find($id);

        if (!$indicator) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data indikator tidak ditemukan.');
        }

        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'indikator'     => 'required|min_length[3]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');

        $aspect = $this->aspectModel->where([
            'id'            => $aspectId,
            'instrument_id' => $instrumentId,
        ])->first();

        if (!$aspect) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Aspek tidak sesuai dengan instrumen yang dipilih.');
        }

        $this->indicatorModel->update($id, [
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indikator'     => trim((string) $this->request->getPost('indikator')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $indicator = $this->indicatorModel->find($id);

        if (!$indicator) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data indikator tidak ditemukan.');
        }

        $instrumentId = $indicator['instrument_id'];

        $this->indicatorModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Indikator berhasil dihapus.');
    }
}