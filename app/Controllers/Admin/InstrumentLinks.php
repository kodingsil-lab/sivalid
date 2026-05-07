<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WorkflowStatusService;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ResponseModel;

class InstrumentLinks extends BaseController
{
    protected InstrumentLinkModel $linkModel;
    protected InstrumentModel $instrumentModel;
    protected ResponseModel $responseModel;
    protected WorkflowStatusService $workflowStatusService;

    public function __construct()
    {
        $this->linkModel       = new InstrumentLinkModel();
        $this->instrumentModel = new InstrumentModel();
        $this->responseModel   = new ResponseModel();
        $this->workflowStatusService = new WorkflowStatusService();
    }

    public function index()
    {
        $links = $this->linkModel->getWithInstrument('validasi_instrumen');

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
        }

        unset($link);

        $data = [
            'title' => 'Link Validasi Instrumen',
            'links' => $links,
        ];

        return view('admin/links/index', $data);
    }

    public function new()
    {
        $data = [
            'title'       => 'Buat Link Validasi Instrumen',
            'link'        => [
                'instrument_id' => $this->request->getGet('instrument_id'),
            ],
            'instruments' => $this->instrumentModel
                ->orderBy('judul', 'ASC')
                ->findAll(),
            'action'      => base_url('admin/instrument-links'),
            'method'      => 'post',
        ];

        return view('admin/links/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_id'   => 'required|integer',
            'judul_link'      => 'required|min_length[3]|max_length[255]',
            'sasaran'         => 'permit_empty|max_length[150]',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'status'          => 'required',
            'maksimal_respon' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        $token = $this->generateUniqueToken();

        $this->linkModel->insert([
            'instrument_id'   => $instrumentId,
            'product_id'      => null,
            'token'           => $token,
            'mode'            => 'validasi_instrumen',
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        $this->workflowStatusService->markInstrumentInValidation($instrumentId);

        return redirect()
            ->to(base_url('admin/instrument-links'))
            ->with('success', 'Link validasi instrumen berhasil dibuat. Status instrumen diperbarui menjadi Dalam Validasi Instrumen.');
    }

    public function edit($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/instrument-links'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        $data = [
            'title'       => 'Edit Link Validasi Instrumen',
            'link'        => $link,
            'instruments' => $this->instrumentModel
                ->orderBy('judul', 'ASC')
                ->findAll(),
            'action'      => base_url('admin/instrument-links/' . $id),
            'method'      => 'put',
        ];

        return view('admin/links/form', $data);
    }

    public function update($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/instrument-links'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        $rules = [
            'instrument_id'   => 'required|integer',
            'judul_link'      => 'required|min_length[3]|max_length[255]',
            'sasaran'         => 'permit_empty|max_length[150]',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'status'          => 'required',
            'maksimal_respon' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        $this->linkModel->update($id, [
            'instrument_id'   => $instrumentId,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-links'))
            ->with('success', 'Link validasi instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/instrument-links'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        $this->linkModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-links'))
            ->with('success', 'Link validasi instrumen berhasil dihapus.');
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
            $exists = $this->linkModel->where('token', $token)->first();
        } while ($exists);

        return $token;
    }

    private function emptyToNull($value)
    {
        return $value === '' || $value === null ? null : $value;
    }
}
