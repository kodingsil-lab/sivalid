<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;
use App\Models\InstrumentItemModel;

class InstrumentItems extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentAspectModel $aspectModel;
    protected InstrumentIndicatorModel $indicatorModel;
    protected InstrumentItemModel $itemModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->aspectModel     = new InstrumentAspectModel();
        $this->indicatorModel  = new InstrumentIndicatorModel();
        $this->itemModel       = new InstrumentItemModel();
    }

    public function index()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;

        $data = [
            'title'        => 'Butir Pernyataan Instrumen',
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'items'        => $this->itemModel->getWithRelations($instrumentId),
        ];

        return view('admin/items/index', $data);
    }

    public function new()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;

        $aspects = [];
        $indicators = [];

        if ($instrumentId !== null) {
            $aspects = $this->aspectModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();

            $indicators = $this->indicatorModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();
        }

        $nextNumber = 1;

        if ($instrumentId !== null) {
            $lastItem = $this->itemModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('nomor', 'DESC')
                ->first();

            if ($lastItem) {
                $nextNumber = ((int) $lastItem['nomor']) + 1;
            }
        }

        $data = [
            'title'        => 'Tambah Butir Pernyataan',
            'item'         => null,
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'aspects'      => $aspects,
            'indicators'   => $indicators,
            'nextNumber'   => $nextNumber,
            'action'       => base_url('admin/instrument-items'),
            'method'       => 'post',
        ];

        return view('admin/items/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'nomor'         => 'required|integer',
            'pernyataan'    => 'required|min_length[5]',
            'tipe_butir'    => 'required',
            'urutan'        => 'required|integer',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');
        $indicatorId  = $this->request->getPost('indicator_id');

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

        if (!empty($indicatorId)) {
            $indicator = $this->indicatorModel->where([
                'id'            => (int) $indicatorId,
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
            ])->first();

            if (!$indicator) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Indikator tidak sesuai dengan instrumen dan aspek yang dipilih.');
            }
        }

        $this->itemModel->insert([
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indicator_id'  => !empty($indicatorId) ? (int) $indicatorId : null,
            'nomor'         => (int) $this->request->getPost('nomor'),
            'pernyataan'    => trim((string) $this->request->getPost('pernyataan')),
            'tipe_butir'    => trim((string) $this->request->getPost('tipe_butir')),
            'wajib'         => (int) $this->request->getPost('wajib'),
            'urutan'        => (int) $this->request->getPost('urutan'),
            'status'        => trim((string) $this->request->getPost('status')),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', 'Butir pernyataan berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $item = $this->itemModel->find($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $aspects = $this->aspectModel
            ->where('instrument_id', $item['instrument_id'])
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $indicators = $this->indicatorModel
            ->where('instrument_id', $item['instrument_id'])
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $data = [
            'title'        => 'Edit Butir Pernyataan',
            'item'         => $item,
            'instrumentId' => $item['instrument_id'],
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'aspects'      => $aspects,
            'indicators'   => $indicators,
            'nextNumber'   => $item['nomor'],
            'action'       => base_url('admin/instrument-items/' . $id),
            'method'       => 'put',
        ];

        return view('admin/items/form', $data);
    }

    public function update($id = null)
    {
        $item = $this->itemModel->find($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'nomor'         => 'required|integer',
            'pernyataan'    => 'required|min_length[5]',
            'tipe_butir'    => 'required',
            'urutan'        => 'required|integer',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');
        $indicatorId  = $this->request->getPost('indicator_id');

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

        if (!empty($indicatorId)) {
            $indicator = $this->indicatorModel->where([
                'id'            => (int) $indicatorId,
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
            ])->first();

            if (!$indicator) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Indikator tidak sesuai dengan instrumen dan aspek yang dipilih.');
            }
        }

        $this->itemModel->update($id, [
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indicator_id'  => !empty($indicatorId) ? (int) $indicatorId : null,
            'nomor'         => (int) $this->request->getPost('nomor'),
            'pernyataan'    => trim((string) $this->request->getPost('pernyataan')),
            'tipe_butir'    => trim((string) $this->request->getPost('tipe_butir')),
            'wajib'         => (int) $this->request->getPost('wajib'),
            'urutan'        => (int) $this->request->getPost('urutan'),
            'status'        => trim((string) $this->request->getPost('status')),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', 'Butir pernyataan berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $item = $this->itemModel->find($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $instrumentId = $item['instrument_id'];

        $this->itemModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', 'Butir pernyataan berhasil dihapus.');
    }
}