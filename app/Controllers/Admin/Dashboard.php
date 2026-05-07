<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnalysisResultModel;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ResearchProductModel;
use App\Models\ResponseModel;

class Dashboard extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentLinkModel $linkModel;
    protected ResponseModel $responseModel;
    protected AnalysisResultModel $analysisResultModel;
    protected ResearchProductModel $productModel;

    public function __construct()
    {
        $this->instrumentModel      = new InstrumentModel();
        $this->linkModel            = new InstrumentLinkModel();
        $this->responseModel        = new ResponseModel();
        $this->analysisResultModel  = new AnalysisResultModel();
        $this->productModel         = new ResearchProductModel();
    }

    public function index()
    {
        $totalInstrumen = $this->instrumentModel->countAllResults();

        $instrumenValid = $this->instrumentModel
            ->where('status', 'Valid')
            ->countAllResults();

        $totalProduk = $this->productModel->countAllResults();

        $linkAktif = $this->linkModel
            ->where('status', 'Aktif')
            ->countAllResults();

        $totalRespon = $this->responseModel->countAllResults();

        $totalLaporan = $this->analysisResultModel->countAllResults();

        $responByMode = $this->responseModel
            ->select('mode, COUNT(id) AS total')
            ->groupBy('mode')
            ->orderBy('mode', 'ASC')
            ->findAll();

        $latestResponses = $this->responseModel
            ->select(
                'responses.*,
                 respondents.nama,
                 respondents.jenis_responden,
                 instrument_links.judul_link,
                 instruments.kode,
                 instruments.judul'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->orderBy('responses.id', 'DESC')
            ->limit(10)
            ->findAll();

        $latestAnalyses = $this->analysisResultModel
            ->select(
                'analysis_results.*,
                 instruments.kode,
                 instruments.judul,
                 instrument_links.judul_link,
                 research_products.nama_produk'
            )
            ->join('instruments', 'instruments.id = analysis_results.instrument_id')
            ->join('instrument_links', 'instrument_links.id = analysis_results.instrument_link_id')
            ->join('research_products', 'research_products.id = analysis_results.product_id', 'left')
            ->orderBy('analysis_results.id', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title'           => 'Dashboard',
            'totalInstrumen'  => $totalInstrumen,
            'instrumenValid'  => $instrumenValid,
            'totalProduk'     => $totalProduk,
            'linkAktif'       => $linkAktif,
            'totalRespon'     => $totalRespon,
            'totalLaporan'    => $totalLaporan,
            'responByMode'    => $responByMode,
            'latestResponses' => $latestResponses,
            'latestAnalyses'  => $latestAnalyses,
        ];

        return view('admin/dashboard', $data);
    }
}
