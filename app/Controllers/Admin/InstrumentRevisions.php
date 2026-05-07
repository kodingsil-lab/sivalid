<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WorkflowStatusService;
use App\Models\AnalysisItemModel;
use App\Models\AnalysisResultModel;
use App\Models\InstrumentItemModel;
use App\Models\InstrumentModel;
use App\Models\InstrumentRevisionModel;
use App\Models\ResponseAnswerModel;

class InstrumentRevisions extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentItemModel $itemModel;
    protected InstrumentRevisionModel $revisionModel;
    protected AnalysisResultModel $analysisResultModel;
    protected AnalysisItemModel $analysisItemModel;
    protected ResponseAnswerModel $answerModel;
    protected WorkflowStatusService $workflowStatusService;

    public function __construct()
    {
        $this->instrumentModel      = new InstrumentModel();
        $this->itemModel            = new InstrumentItemModel();
        $this->revisionModel        = new InstrumentRevisionModel();
        $this->analysisResultModel  = new AnalysisResultModel();
        $this->analysisItemModel    = new AnalysisItemModel();
        $this->answerModel          = new ResponseAnswerModel();
        $this->workflowStatusService = new WorkflowStatusService();
    }

    public function index()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;

        $data = [
            'title'        => 'Revisi Butir Instrumen',
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->orderBy('judul', 'ASC')->findAll(),
            'revisions'    => $this->revisionModel->getWithItem($instrumentId),
            'revisionCandidates' => $this->getRevisionCandidates($instrumentId),
        ];

        return view('admin/revisions/index', $data);
    }

    public function new()
    {
        $itemId = $this->request->getGet('item_id');
        $analysisResultId = $this->request->getGet('analysis_result_id');

        if (empty($itemId)) {
            return redirect()
                ->to(base_url('admin/instrument-revisions'))
                ->with('error', 'Butir instrumen belum dipilih.');
        }

        $item = $this->getItemDetail((int) $itemId);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-revisions'))
                ->with('error', 'Butir instrumen tidak ditemukan.');
        }

        $analysisItem = null;

        if (!empty($analysisResultId)) {
            $analysisItem = $this->analysisItemModel
                ->where('analysis_result_id', (int) $analysisResultId)
                ->where('instrument_item_id', (int) $itemId)
                ->first();
        }

        $comments = $this->getCommentsByItem((int) $itemId, !empty($analysisResultId) ? (int) $analysisResultId : null);

        $data = [
            'title'            => 'Revisi Butir Instrumen',
            'item'             => $item,
            'analysisItem'     => $analysisItem,
            'analysisResultId' => $analysisResultId,
            'comments'         => $comments,
            'revisions'        => $this->revisionModel->getByItem((int) $itemId),
            'action'           => base_url('admin/instrument-revisions'),
            'method'           => 'post',
        ];

        return view('admin/revisions/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_item_id' => 'required|integer',
            'pernyataan_baru'   => 'required|min_length[5]',
            'alasan_revisi'     => 'permit_empty',
            'sumber_revisi'     => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $itemId = (int) $this->request->getPost('instrument_item_id');
        $analysisResultId = $this->request->getPost('analysis_result_id');

        $item = $this->itemModel->find($itemId);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-revisions'))
                ->with('error', 'Butir instrumen tidak ditemukan.');
        }

        $pernyataanLama = $item['pernyataan'];
        $pernyataanBaru = trim((string) $this->request->getPost('pernyataan_baru'));

        $db = db_connect();
        $db->transStart();

        $this->revisionModel->insert([
            'instrument_item_id' => $itemId,
            'analysis_result_id' => !empty($analysisResultId) ? (int) $analysisResultId : null,
            'pernyataan_lama'    => $pernyataanLama,
            'pernyataan_baru'    => $pernyataanBaru,
            'alasan_revisi'      => trim((string) $this->request->getPost('alasan_revisi')),
            'sumber_revisi'      => trim((string) $this->request->getPost('sumber_revisi')),
            'tanggal_revisi'     => date('Y-m-d H:i:s'),
        ]);

        $this->itemModel->update($itemId, [
            'pernyataan' => $pernyataanBaru,
            'status'     => 'Direvisi',
        ]);

        $this->workflowStatusService->markInstrumentRevised((int) $item['instrument_id']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Revisi butir gagal disimpan.');
        }

        return redirect()
            ->to(base_url('admin/instrument-revisions?instrument_id=' . $item['instrument_id']))
            ->with('success', 'Revisi butir berhasil disimpan. Status instrumen diperbarui menjadi Direvisi.');
    }

    public function edit($id = null)
    {
        return redirect()
            ->to(base_url('admin/instrument-revisions'))
            ->with('error', 'Riwayat revisi tidak diedit. Buat revisi baru jika ada perubahan lanjutan.');
    }

    public function update($id = null)
    {
        return redirect()
            ->to(base_url('admin/instrument-revisions'))
            ->with('error', 'Riwayat revisi tidak diperbarui agar jejak perubahan tetap aman.');
    }

    public function delete($id = null)
    {
        $revision = $this->revisionModel->find($id);

        if (!$revision) {
            return redirect()
                ->to(base_url('admin/instrument-revisions'))
                ->with('error', 'Riwayat revisi tidak ditemukan.');
        }

        /*
         * Yang dihapus hanya riwayat revisi, bukan mengembalikan redaksi butir.
         */
        $this->revisionModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-revisions'))
            ->with('success', 'Riwayat revisi berhasil dihapus.');
    }

    private function getRevisionCandidates(?int $instrumentId = null): array
    {
        $builder = $this->analysisItemModel->select(
            'analysis_items.*,
             analysis_results.instrument_id,
             analysis_results.instrument_link_id,
             instruments.kode,
             instruments.judul,
             instrument_items.nomor,
             instrument_items.pernyataan,
             instrument_items.status AS item_status,
             instrument_aspects.nama_aspek'
        )
            ->join('analysis_results', 'analysis_results.id = analysis_items.analysis_result_id')
            ->join('instruments', 'instruments.id = analysis_results.instrument_id')
            ->join('instrument_items', 'instrument_items.id = analysis_items.instrument_item_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id')
            ->whereIn('analysis_items.rekomendasi', [
                'Revisi kecil',
                'Revisi besar',
                'Ganti atau hapus',
            ]);

        if ($instrumentId !== null) {
            $builder->where('analysis_results.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('analysis_items.rata_rata', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }

    private function getItemDetail(int $itemId): ?array
    {
        return $this->itemModel->select(
            'instrument_items.*,
             instruments.kode,
             instruments.judul,
             instrument_aspects.nama_aspek,
             instrument_indicators.indikator'
        )
            ->join('instruments', 'instruments.id = instrument_items.instrument_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id')
            ->join('instrument_indicators', 'instrument_indicators.id = instrument_items.indicator_id', 'left')
            ->where('instrument_items.id', $itemId)
            ->first();
    }

    private function getCommentsByItem(int $itemId, ?int $analysisResultId = null): array
    {
        $builder = $this->answerModel->select(
            'response_answers.*,
             responses.instrument_link_id,
             respondents.nama,
             respondents.bidang_keahlian,
             respondents.instansi'
        )
            ->join('responses', 'responses.id = response_answers.response_id')
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->where('response_answers.instrument_item_id', $itemId)
            ->where('response_answers.komentar !=', '');

        if ($analysisResultId !== null) {
            $analysis = $this->analysisResultModel->find($analysisResultId);

            if ($analysis) {
                $builder->where('responses.instrument_link_id', (int) $analysis['instrument_link_id']);
            }
        }

        return $builder
            ->orderBy('response_answers.id', 'DESC')
            ->findAll();
    }
}
