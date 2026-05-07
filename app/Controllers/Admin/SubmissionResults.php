<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class SubmissionResults extends BaseController
{
    protected ResponseModel $responseModel;
    protected ResponseAnswerModel $answerModel;

    protected array $allowedModes = [
        'validasi_instrumen',
        'validasi_produk',
        'respon_mahasiswa',
        'observasi',
        'fgd',
        'tes_kinerja',
    ];

    public function __construct()
    {
        $this->responseModel = new ResponseModel();
        $this->answerModel   = new ResponseAnswerModel();
    }

    public function index()
    {
        $mode = $this->request->getGet('mode');

        if (!in_array($mode, $this->allowedModes, true)) {
            $mode = null;
        }

        $data = [
            'title'        => 'Hasil Pengisian',
            'mode'         => $mode,
            'allowedModes' => $this->allowedModes,
            'responses'    => $this->getResponses($mode),
        ];

        return view('admin/submissions/index', $data);
    }

    public function show($id = null)
    {
        $responseId = (int) $id;
        $response = $this->getResponseDetail($responseId);

        if (!$response) {
            return redirect()
                ->to(base_url('admin/submissions'))
                ->with('error', 'Data pengisian tidak ditemukan.');
        }

        $answers = $this->answerModel
            ->select(
                'response_answers.*,
                 instrument_items.nomor,
                 instrument_items.pernyataan,
                 instrument_items.tipe_butir,
                 instrument_items.wajib,
                 instrument_aspects.nama_aspek'
            )
            ->join('instrument_items', 'instrument_items.id = response_answers.instrument_item_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id', 'left')
            ->where('response_answers.response_id', $responseId)
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();

        $data = [
            'title'    => 'Detail Hasil Pengisian',
            'response' => $response,
            'answers'  => $answers,
        ];

        return view('admin/submissions/show', $data);
    }

    public function delete($id = null)
    {
        $responseId = (int) $id;
        $response = $this->responseModel->find($responseId);

        if (!$response) {
            return redirect()
                ->to(base_url('admin/submissions'))
                ->with('error', 'Data pengisian tidak ditemukan.');
        }

        $this->answerModel
            ->where('response_id', $responseId)
            ->delete();

        $this->responseModel->delete($responseId);

        return redirect()
            ->to(base_url('admin/submissions?mode=' . $response['mode']))
            ->with('success', 'Data pengisian berhasil dihapus.');
    }

    private function getResponses(?string $mode = null): array
    {
        $builder = $this->responseModel
            ->select(
                'responses.*,
                 respondents.nama,
                 respondents.email,
                 respondents.jenis_responden,
                 respondents.nim,
                 respondents.program_studi,
                 respondents.kelas,
                 respondents.semester,
                 respondents.instansi,
                 respondents.bidang_keahlian,
                 instrument_links.judul_link,
                 instruments.kode,
                 instruments.judul,
                 research_products.nama_produk'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left');

        if ($mode !== null) {
            $builder->where('responses.mode', $mode);
        }

        return $builder
            ->orderBy('responses.id', 'DESC')
            ->findAll();
    }

    private function getResponseDetail(int $responseId): ?array
    {
        return $this->responseModel
            ->select(
                'responses.*,
                 respondents.nama,
                 respondents.email,
                 respondents.jenis_responden,
                 respondents.nim,
                 respondents.program_studi,
                 respondents.kelas,
                 respondents.semester,
                 respondents.instansi,
                 respondents.bidang_keahlian,
                 instrument_links.judul_link,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 research_products.nama_produk,
                 research_products.kode AS product_kode,
                 research_products.jenis_produk'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left')
            ->where('responses.id', $responseId)
            ->first();
    }
}
