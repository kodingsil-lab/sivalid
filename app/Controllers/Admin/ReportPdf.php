<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\PdfService;
use App\Models\AnalysisAspectModel;
use App\Models\AnalysisItemModel;
use App\Models\AnalysisResultModel;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentRevisionModel;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class ReportPdf extends BaseController
{
    protected AnalysisResultModel $analysisResultModel;
    protected AnalysisAspectModel $analysisAspectModel;
    protected AnalysisItemModel $analysisItemModel;
    protected InstrumentLinkModel $linkModel;
    protected ResponseModel $responseModel;
    protected ResponseAnswerModel $answerModel;
    protected InstrumentRevisionModel $revisionModel;
    protected PdfService $pdfService;

    public function __construct()
    {
        $this->analysisResultModel = new AnalysisResultModel();
        $this->analysisAspectModel = new AnalysisAspectModel();
        $this->analysisItemModel   = new AnalysisItemModel();
        $this->linkModel           = new InstrumentLinkModel();
        $this->responseModel       = new ResponseModel();
        $this->answerModel         = new ResponseAnswerModel();
        $this->revisionModel       = new InstrumentRevisionModel();
        $this->pdfService          = new PdfService();
    }

    public function validasiInstrumen($analysisResultId = null)
    {
        $data = $this->getValidasiInstrumenData((int) $analysisResultId, 'Laporan Validasi Instrumen');

        if (isset($data['redirect'])) {
            return $data['redirect'];
        }

        $html = view('admin/reports/print_validasi_instrumen', $data);
        $filename = 'laporan-validasi-instrumen-' . $data['analysis']['id'] . '.pdf';

        return $this->pdfService->render($html, $filename);
    }

    public function previewValidasiInstrumen($analysisResultId = null)
    {
        $data = $this->getValidasiInstrumenData((int) $analysisResultId, 'Preview PDF Laporan Validasi Instrumen');

        if (isset($data['redirect'])) {
            return $data['redirect'];
        }

        $html = view('admin/reports/print_validasi_instrumen', $data);
        $filename = 'preview-validasi-instrumen-' . $data['analysis']['id'] . '.pdf';

        return $this->pdfService->preview($html, $filename);
    }

    public function validasiProduk($analysisResultId = null)
    {
        $data = $this->getValidasiProdukData((int) $analysisResultId, 'Laporan Validasi Produk');

        if (isset($data['redirect'])) {
            return $data['redirect'];
        }

        $html = view('admin/reports/print_validasi_produk', $data);
        $filename = 'laporan-validasi-produk-' . $data['analysis']['id'] . '.pdf';

        return $this->pdfService->render($html, $filename);
    }

    public function previewValidasiProduk($analysisResultId = null)
    {
        $data = $this->getValidasiProdukData((int) $analysisResultId, 'Preview PDF Laporan Validasi Produk');

        if (isset($data['redirect'])) {
            return $data['redirect'];
        }

        $html = view('admin/reports/print_validasi_produk', $data);
        $filename = 'preview-validasi-produk-' . $data['analysis']['id'] . '.pdf';

        return $this->pdfService->preview($html, $filename);
    }

    private function getValidasiInstrumenData(int $analysisResultId, string $title): array
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_instrumen') {
            return [
                'redirect' => redirect()
                    ->to(base_url('admin/reports'))
                    ->with('error', 'Data laporan validasi instrumen tidak ditemukan.'),
            ];
        }

        return [
            'title'          => $title,
            'analysis'       => $analysis,
            'link'           => $this->getLinkDetail((int) $analysis['instrument_link_id']),
            'responses'      => $this->responseModel->getWithRespondentByLink((int) $analysis['instrument_link_id']),
            'aspectAnalysis' => $this->analysisAspectModel->getByAnalysis($analysisResultId),
            'itemAnalysis'   => $this->analysisItemModel->getByAnalysis($analysisResultId),
            'comments'       => $this->getItemComments((int) $analysis['instrument_link_id']),
            'revisions'      => $this->revisionModel->getWithItem((int) $analysis['instrument_id']),
            'isPdf'          => true,
        ];
    }

    private function getValidasiProdukData(int $analysisResultId, string $title): array
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_produk') {
            return [
                'redirect' => redirect()
                    ->to(base_url('admin/reports'))
                    ->with('error', 'Data laporan validasi produk tidak ditemukan.'),
            ];
        }

        return [
            'title'          => $title,
            'analysis'       => $analysis,
            'link'           => $this->getLinkDetail((int) $analysis['instrument_link_id']),
            'responses'      => $this->responseModel->getWithRespondentByLink((int) $analysis['instrument_link_id']),
            'aspectAnalysis' => $this->analysisAspectModel->getByAnalysis($analysisResultId),
            'itemAnalysis'   => $this->analysisItemModel->getByAnalysis($analysisResultId),
            'comments'       => $this->getItemComments((int) $analysis['instrument_link_id']),
            'isPdf'          => true,
        ];
    }

    private function getLinkDetail(int $linkId): ?array
    {
        $linkRow = $this->linkModel->find($linkId);

        if (!$linkRow) {
            return null;
        }

        return $this->linkModel->findByToken($linkRow['token']);
    }

    private function getItemComments(int $instrumentLinkId): array
    {
        return $this->answerModel
            ->select(
                'response_answers.*,
                 instrument_items.nomor,
                 instrument_items.pernyataan,
                 respondents.nama,
                 respondents.jenis_responden'
            )
            ->join('responses', 'responses.id = response_answers.response_id')
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_items', 'instrument_items.id = response_answers.instrument_item_id')
            ->where('responses.instrument_link_id', $instrumentLinkId)
            ->where('response_answers.komentar !=', '')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }
}
