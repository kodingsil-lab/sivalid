<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WorkflowStatusService;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ResponseModel;

class RespondentLinks extends BaseController
{
    protected InstrumentLinkModel $linkModel;
    protected InstrumentModel $instrumentModel;
    protected ResponseModel $responseModel;
    protected WorkflowStatusService $workflowStatusService;

    protected array $allowedModes = [
        'respon_mahasiswa',
        'observasi',
        'fgd',
        'tes_kinerja',
    ];

    public function __construct()
    {
        $this->linkModel       = new InstrumentLinkModel();
        $this->instrumentModel = new InstrumentModel();
        $this->responseModel   = new ResponseModel();
        $this->workflowStatusService = new WorkflowStatusService();
    }

    public function index()
    {
        $mode = $this->request->getGet('mode');
        $perPage = config('Pager')->perPage;

        if (!in_array($mode, $this->allowedModes, true)) {
            $mode = null;
        }

        $links = $this->linkModel->paginateWithInstrument($mode, $perPage, 'respondent_links');

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
        }

        unset($link);

        $data = [
            'title'        => 'Link Instrumen Responden',
            'mode'         => $mode,
            'links'        => $links,
            'allowedModes' => $this->allowedModes,
            'pager'        => $this->linkModel->pager,
            'pagerGroup'   => 'respondent_links',
        ];

        return view('admin/respondent_links/index', $data);
    }

    public function new()
    {
        $mode = $this->request->getGet('mode');

        if (!in_array($mode, $this->allowedModes, true)) {
            $mode = 'respon_mahasiswa';
        }

        $data = [
            'title'       => 'Buat Link Instrumen Responden',
            'link'        => [
                'mode' => $mode,
            ],
            'instruments' => $this->getValidInstruments(),
            'allowedModes' => $this->allowedModes,
            'action'      => base_url('admin/respondent-links'),
            'method'      => 'post',
        ];

        return view('admin/respondent_links/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_id'   => 'required|integer',
            'mode'            => 'required',
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

        $mode = trim((string) $this->request->getPost('mode'));

        if (!in_array($mode, $this->allowedModes, true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Mode pengisian tidak valid.');
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        if (!in_array($instrument['status'], ['Valid', 'Siap Disebar'], true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen yang dibagikan kepada responden harus berstatus Valid atau Siap Disebar.');
        }

        $token = $this->generateUniqueToken();

        $this->linkModel->insert([
            'instrument_id'   => $instrumentId,
            'product_id'      => null,
            'token'           => $token,
            'mode'            => $mode,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        $this->workflowStatusService->markInstrumentReadyToShare($instrumentId);

        $statusLabel = status_display_label('Siap Disebar');

        return redirect()
            ->to(base_url('admin/respondent-links?mode=' . $mode))
            ->with('success', 'Link instrumen responden berhasil dibuat. Status instrumen diperbarui menjadi ' . $statusLabel . '.');
    }

    public function edit($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || !in_array($link['mode'], $this->allowedModes, true)) {
            return redirect()
                ->to(base_url('admin/respondent-links'))
                ->with('error', 'Link instrumen responden tidak ditemukan.');
        }

        $data = [
            'title'        => 'Edit Link Instrumen Responden',
            'link'         => $link,
            'instruments'  => $this->getValidInstruments(),
            'allowedModes' => $this->allowedModes,
            'action'       => base_url('admin/respondent-links/' . $id),
            'method'       => 'put',
        ];

        return view('admin/respondent_links/form', $data);
    }

    public function update($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || !in_array($link['mode'], $this->allowedModes, true)) {
            return redirect()
                ->to(base_url('admin/respondent-links'))
                ->with('error', 'Link instrumen responden tidak ditemukan.');
        }

        $rules = [
            'instrument_id'   => 'required|integer',
            'mode'            => 'required',
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

        $mode = trim((string) $this->request->getPost('mode'));

        if (!in_array($mode, $this->allowedModes, true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Mode pengisian tidak valid.');
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument || !in_array($instrument['status'], ['Valid', 'Siap Disebar'], true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen harus berstatus Valid atau Siap Disebar.');
        }

        $this->linkModel->update($id, [
            'instrument_id'   => $instrumentId,
            'mode'            => $mode,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        return redirect()
            ->to(base_url('admin/respondent-links?mode=' . $mode))
            ->with('success', 'Link instrumen responden berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || !in_array($link['mode'], $this->allowedModes, true)) {
            return redirect()
                ->to(base_url('admin/respondent-links'))
                ->with('error', 'Link instrumen responden tidak ditemukan.');
        }

        $mode = $link['mode'];

        $this->linkModel->delete($id);

        return redirect()
            ->to(base_url('admin/respondent-links?mode=' . $mode))
            ->with('success', 'Link instrumen responden berhasil dihapus.');
    }

    private function getValidInstruments(): array
    {
        return $this->instrumentModel
            ->whereIn('status', ['Valid', 'Siap Disebar'])
            ->orderBy('judul', 'ASC')
            ->findAll();
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
