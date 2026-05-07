<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnalysisAspectModel;
use App\Models\AnalysisItemModel;
use App\Models\AnalysisResultModel;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentRevisionModel;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class Reports extends BaseController
{
    protected AnalysisResultModel $analysisResultModel;
    protected AnalysisAspectModel $analysisAspectModel;
    protected AnalysisItemModel $analysisItemModel;
    protected InstrumentLinkModel $linkModel;
    protected ResponseModel $responseModel;
    protected ResponseAnswerModel $answerModel;
    protected InstrumentRevisionModel $revisionModel;

    public function __construct()
    {
        $this->analysisResultModel = new AnalysisResultModel();
        $this->analysisAspectModel = new AnalysisAspectModel();
        $this->analysisItemModel   = new AnalysisItemModel();
        $this->linkModel           = new InstrumentLinkModel();
        $this->responseModel       = new ResponseModel();
        $this->answerModel         = new ResponseAnswerModel();
        $this->revisionModel       = new InstrumentRevisionModel();
    }

    public function index()
    {
        $analyses = $this->analysisResultModel
            ->select(
                'analysis_results.*,
                 instruments.kode,
                 instruments.judul,
                 instrument_links.judul_link,
                 research_products.nama_produk,
                 research_products.kode AS product_kode'
            )
            ->join('instruments', 'instruments.id = analysis_results.instrument_id')
            ->join('instrument_links', 'instrument_links.id = analysis_results.instrument_link_id')
            ->join('research_products', 'research_products.id = analysis_results.product_id', 'left')
            ->orderBy('analysis_results.id', 'DESC')
            ->findAll();

        $links = $this->linkModel
            ->select(
                'instrument_links.*,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 instruments.status AS instrument_status'
            )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->whereIn('instrument_links.mode', [
                'respon_mahasiswa',
                'observasi',
                'fgd',
                'tes_kinerja',
            ])
            ->orderBy('instrument_links.id', 'DESC')
            ->findAll();

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
        }

        unset($link);

        $data = [
            'title'    => 'Laporan',
            'analyses' => $analyses,
            'links'    => $links,
        ];

        return view('admin/reports/index', $data);
    }

    public function validasiInstrumen($analysisResultId = null)
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_instrumen') {
            return redirect()
                ->to(base_url('admin/reports'))
                ->with('error', 'Laporan validasi instrumen tidak ditemukan.');
        }

        $link = $this->getLinkDetail((int) $analysis['instrument_link_id']);
        $responses = $this->responseModel->getWithRespondentByLink((int) $analysis['instrument_link_id']);
        $aspectAnalysis = $this->analysisAspectModel->getByAnalysis((int) $analysisResultId);
        $itemAnalysis = $this->analysisItemModel->getByAnalysis((int) $analysisResultId);
        $comments = $this->getItemComments((int) $analysis['instrument_link_id']);
        $revisions = $this->revisionModel->getWithItem((int) $analysis['instrument_id']);

        $data = [
            'title'          => 'Laporan Validasi Instrumen',
            'analysis'       => $analysis,
            'link'           => $link,
            'responses'      => $responses,
            'aspectAnalysis' => $aspectAnalysis,
            'itemAnalysis'   => $itemAnalysis,
            'comments'       => $comments,
            'revisions'      => $revisions,
        ];

        return view('admin/reports/validasi_instrumen', $data);
    }

    public function validasiProduk($analysisResultId = null)
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/reports'))
                ->with('error', 'Laporan validasi produk tidak ditemukan.');
        }

        $link = $this->getLinkDetail((int) $analysis['instrument_link_id']);
        $responses = $this->responseModel->getWithRespondentByLink((int) $analysis['instrument_link_id']);
        $aspectAnalysis = $this->analysisAspectModel->getByAnalysis((int) $analysisResultId);
        $itemAnalysis = $this->analysisItemModel->getByAnalysis((int) $analysisResultId);
        $comments = $this->getItemComments((int) $analysis['instrument_link_id']);

        $data = [
            'title'          => 'Laporan Validasi Produk',
            'analysis'       => $analysis,
            'link'           => $link,
            'responses'      => $responses,
            'aspectAnalysis' => $aspectAnalysis,
            'itemAnalysis'   => $itemAnalysis,
            'comments'       => $comments,
        ];

        return view('admin/reports/validasi_produk', $data);
    }

    public function printValidasiInstrumen($analysisResultId = null)
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_instrumen') {
            return redirect()
                ->to(base_url('admin/reports'))
                ->with('error', 'Laporan cetak validasi instrumen tidak ditemukan.');
        }

        $link = $this->getLinkDetail((int) $analysis['instrument_link_id']);
        $responses = $this->responseModel->getWithRespondentByLink((int) $analysis['instrument_link_id']);
        $aspectAnalysis = $this->analysisAspectModel->getByAnalysis((int) $analysisResultId);
        $itemAnalysis = $this->analysisItemModel->getByAnalysis((int) $analysisResultId);
        $comments = $this->getItemComments((int) $analysis['instrument_link_id']);
        $revisions = $this->revisionModel->getWithItem((int) $analysis['instrument_id']);

        $data = [
            'title'          => 'Cetak Laporan Validasi Instrumen',
            'analysis'       => $analysis,
            'link'           => $link,
            'responses'      => $responses,
            'aspectAnalysis' => $aspectAnalysis,
            'itemAnalysis'   => $itemAnalysis,
            'comments'       => $comments,
            'revisions'      => $revisions,
        ];

        return view('admin/reports/print_validasi_instrumen', $data);
    }

    public function printValidasiProduk($analysisResultId = null)
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/reports'))
                ->with('error', 'Laporan cetak validasi produk tidak ditemukan.');
        }

        $link = $this->getLinkDetail((int) $analysis['instrument_link_id']);
        $responses = $this->responseModel->getWithRespondentByLink((int) $analysis['instrument_link_id']);
        $aspectAnalysis = $this->analysisAspectModel->getByAnalysis((int) $analysisResultId);
        $itemAnalysis = $this->analysisItemModel->getByAnalysis((int) $analysisResultId);
        $comments = $this->getItemComments((int) $analysis['instrument_link_id']);

        $data = [
            'title'          => 'Cetak Laporan Validasi Produk',
            'analysis'       => $analysis,
            'link'           => $link,
            'responses'      => $responses,
            'aspectAnalysis' => $aspectAnalysis,
            'itemAnalysis'   => $itemAnalysis,
            'comments'       => $comments,
        ];

        return view('admin/reports/print_validasi_produk', $data);
    }

    public function revisiButir()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;

        $revisions = $this->revisionModel->getWithItem($instrumentId);

        $data = [
            'title'       => 'Laporan Revisi Butir Instrumen',
            'instrumentId'=> $instrumentId,
            'revisions'   => $revisions,
        ];

        return view('admin/reports/revisi_butir', $data);
    }

    public function responMahasiswa($linkId = null)
    {
        return $this->genericPengisianReport(
            (int) $linkId,
            'respon_mahasiswa',
            'Laporan Respon Mahasiswa',
            'admin/reports/respon_mahasiswa'
        );
    }

    public function observasi($linkId = null)
    {
        return $this->genericPengisianReport(
            (int) $linkId,
            'observasi',
            'Laporan Observasi',
            'admin/reports/observasi'
        );
    }

    public function fgd($linkId = null)
    {
        return $this->genericPengisianReport(
            (int) $linkId,
            'fgd',
            'Laporan FGD',
            'admin/reports/fgd'
        );
    }

    private function genericPengisianReport(int $linkId, string $mode, string $title, string $view)
    {
        $linkRow = $this->linkModel->find($linkId);

        if (!$linkRow || $linkRow['mode'] !== $mode) {
            return redirect()
                ->to(base_url('admin/reports'))
                ->with('error', $title . ' tidak ditemukan.');
        }

        $link = $this->getLinkDetail($linkId);
        $responses = $this->responseModel->getWithRespondentByLink($linkId);
        $summary = $this->getGenericSummary($linkId);
        $items = $this->getGenericItemSummary($linkId);
        $comments = $this->getItemComments($linkId);

        $data = [
            'title'     => $title,
            'link'      => $link,
            'responses' => $responses,
            'summary'   => $summary,
            'items'     => $items,
            'comments'  => $comments,
        ];

        return view($view, $data);
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

    private function getGenericSummary(int $instrumentLinkId): array
    {
        $responses = $this->responseModel
            ->where('instrument_link_id', $instrumentLinkId)
            ->findAll();

        $responseIds = array_map(static function ($response) {
            return (int) $response['id'];
        }, $responses);

        if (empty($responseIds)) {
            return [
                'jumlah_responden' => 0,
                'total_skor'       => 0,
                'jumlah_jawaban'   => 0,
                'rata_rata'        => 0,
            ];
        }

        $answers = $this->answerModel
            ->whereIn('response_id', $responseIds)
            ->findAll();

        $totalSkor = 0;

        foreach ($answers as $answer) {
            $totalSkor += (int) $answer['skor'];
        }

        $jumlahJawaban = count($answers);
        $rataRata = $jumlahJawaban > 0 ? $totalSkor / $jumlahJawaban : 0;

        return [
            'jumlah_responden' => count($responses),
            'total_skor'       => $totalSkor,
            'jumlah_jawaban'   => $jumlahJawaban,
            'rata_rata'        => round($rataRata, 2),
        ];
    }

    private function getGenericItemSummary(int $instrumentLinkId): array
    {
        return $this->answerModel
            ->select(
                'instrument_items.nomor,
                 instrument_items.pernyataan,
                 instrument_aspects.nama_aspek,
                 COUNT(response_answers.id) AS jumlah_jawaban,
                 SUM(response_answers.skor) AS total_skor,
                 AVG(response_answers.skor) AS rata_rata'
            )
            ->join('responses', 'responses.id = response_answers.response_id')
            ->join('instrument_items', 'instrument_items.id = response_answers.instrument_item_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id')
            ->where('responses.instrument_link_id', $instrumentLinkId)
            ->groupBy('instrument_items.id')
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }
}
