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
        $totalInstrumen = $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->countAllResults();

        $instrumenValid = $this->manualValidInstrumentModel
            ->scopeOwned('manual_valid_instruments.user_id')
            ->countAllResults();

        $linkAktif = $this->linkModel
            ->scopeOwned('instrument_links.user_id')
            ->where('status', 'Aktif')
            ->countAllResults();

        $totalRespon = $this->responseModel
            ->scopeOwned('responses.user_id')
            ->countAllResults();

        $responByType = $this->responseModel
            ->scopeOwned('responses.user_id')
            ->select('respondents.jenis_responden, COUNT(responses.id) AS total')
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->groupBy('respondents.jenis_responden')
            ->orderBy('respondents.jenis_responden', 'ASC')
            ->findAll();

        $latestResponseQuery = $this->responseModel
            ->select(
                'responses.*,
                 respondents.nama,
                 respondents.jenis_responden,
                 instrument_links.identity_template,
                 instrument_links.identity_fields,
                 instrument_links.judul_link,
                 instruments.kode,
                 instruments.judul'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id');

        $this->applyOwnerScope($latestResponseQuery, 'responses.user_id');

        $latestResponses = $latestResponseQuery
            ->orderBy('responses.id', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title'           => 'Dashboard',
            'totalInstrumen'  => $totalInstrumen,
            'instrumenValid'  => $instrumenValid,
            'linkAktif'       => $linkAktif,
            'totalRespon'     => $totalRespon,
            'responByType'    => $responByType,
            'latestResponses' => $latestResponses,
        ];

        return view('admin/dashboard', $data);
    }
}
