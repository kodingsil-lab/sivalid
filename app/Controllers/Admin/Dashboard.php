<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ManualValidInstrumentModel;
use App\Models\ResponseModel;

class Dashboard extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentLinkModel $linkModel;
    protected ManualValidInstrumentModel $manualValidInstrumentModel;
    protected ResponseModel $responseModel;

    public function __construct()
    {
        $this->instrumentModel      = new InstrumentModel();
        $this->linkModel            = new InstrumentLinkModel();
        $this->manualValidInstrumentModel = new ManualValidInstrumentModel();
        $this->responseModel        = new ResponseModel();
    }

    public function index()
    {
        $totalInstrumen = $this->instrumentModel->countAllResults();

        $instrumenValid = $this->manualValidInstrumentModel->countAllResults();

        $linkAktif = $this->linkModel
            ->where('status', 'Aktif')
            ->countAllResults();

        $totalRespon = $this->responseModel->countAllResults();

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

        $data = [
            'title'           => 'Dashboard',
            'totalInstrumen'  => $totalInstrumen,
            'instrumenValid'  => $instrumenValid,
            'linkAktif'       => $linkAktif,
            'totalRespon'     => $totalRespon,
            'responByMode'    => $responByMode,
            'latestResponses' => $latestResponses,
        ];

        return view('admin/dashboard', $data);
    }
}
