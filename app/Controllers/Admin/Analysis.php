<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnalysisResultModel;

class Analysis extends BaseController
{
    protected AnalysisResultModel $analysisResultModel;

    public function __construct()
    {
        $this->analysisResultModel = new AnalysisResultModel();
    }

    public function index()
    {
        $analyses = $this->analysisResultModel
            ->select(
                'analysis_results.*,
                 instruments.kode,
                 instruments.judul,
                 instrument_links.judul_link'
            )
            ->join('instruments', 'instruments.id = analysis_results.instrument_id')
            ->join('instrument_links', 'instrument_links.id = analysis_results.instrument_link_id')
            ->orderBy('analysis_results.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'Analisis & Laporan',
            'analyses' => $analyses,
        ];

        return view('admin/validations/instrument_result', [
            'title' => 'Analisis & Laporan',
            'links' => [],
            'analyses' => $analyses,
        ]);
    }
}