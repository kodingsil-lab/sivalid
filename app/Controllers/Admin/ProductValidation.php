<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WorkflowStatusService;
use App\Models\AnalysisAspectModel;
use App\Models\AnalysisItemModel;
use App\Models\AnalysisResultModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentItemModel;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ProductInstrumentModel;
use App\Models\ResearchProductModel;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class ProductValidation extends BaseController
{
    protected ResearchProductModel $productModel;
    protected InstrumentModel $instrumentModel;
    protected ProductInstrumentModel $productInstrumentModel;
    protected InstrumentLinkModel $linkModel;
    protected ResponseModel $responseModel;
    protected ResponseAnswerModel $answerModel;
    protected InstrumentItemModel $itemModel;
    protected InstrumentAspectModel $aspectModel;
    protected AnalysisResultModel $analysisResultModel;
    protected AnalysisAspectModel $analysisAspectModel;
    protected AnalysisItemModel $analysisItemModel;
    protected WorkflowStatusService $workflowStatusService;

    public function __construct()
    {
        $this->productModel           = new ResearchProductModel();
        $this->instrumentModel        = new InstrumentModel();
        $this->productInstrumentModel = new ProductInstrumentModel();
        $this->linkModel              = new InstrumentLinkModel();
        $this->responseModel          = new ResponseModel();
        $this->answerModel            = new ResponseAnswerModel();
        $this->itemModel              = new InstrumentItemModel();
        $this->aspectModel            = new InstrumentAspectModel();
        $this->analysisResultModel    = new AnalysisResultModel();
        $this->analysisAspectModel    = new AnalysisAspectModel();
        $this->analysisItemModel      = new AnalysisItemModel();
        $this->workflowStatusService  = new WorkflowStatusService();
    }

    public function index()
    {
        $links = $this->linkModel->getWithInstrument('validasi_produk');

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
            $link['analysis'] = $this->analysisResultModel->getLatestByLink((int) $link['id']);
        }

        unset($link);

        $data = [
            'title' => 'Analisis Validasi Produk',
            'links' => $links,
        ];

        return view('admin/validations/product_result', $data);
    }

    public function new()
    {
        $productId = $this->request->getGet('product_id');

        $data = [
            'title'       => 'Buat Link Validasi Produk',
            'link'        => [
                'product_id' => $productId,
                'mode'       => 'validasi_produk',
            ],
            'products'    => $this->productModel
                ->orderBy('nama_produk', 'ASC')
                ->findAll(),
            'instruments' => $this->getValidInstruments((int) ($productId ?: 0)),
            'action'      => base_url('admin/validasi-produk'),
            'method'      => 'post',
        ];

        return view('admin/links/form', $data);
    }

    public function create()
    {
        $rules = [
            'product_id'      => 'required|integer',
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

        $productId    = (int) $this->request->getPost('product_id');
        $instrumentId = (int) $this->request->getPost('instrument_id');

        $product = $this->productModel->find($productId);

        if (!$product) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Produk penelitian tidak ditemukan.');
        }

        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        if ($instrument['status'] !== 'Valid') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen yang digunakan untuk validasi produk harus berstatus Valid.');
        }

        $isLinked = $this->productInstrumentModel
            ->where('product_id', $productId)
            ->where('instrument_id', $instrumentId)
            ->first();

        if (!$isLinked) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen belum dihubungkan dengan produk ini.');
        }

        $token = $this->generateUniqueToken();

        $this->linkModel->insert([
            'instrument_id'   => $instrumentId,
            'product_id'      => $productId,
            'token'           => $token,
            'mode'            => 'validasi_produk',
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        $this->workflowStatusService->markProductInValidation($productId);

        return redirect()
            ->to(base_url('admin/validasi-produk'))
            ->with('success', 'Link validasi produk berhasil dibuat. Status produk diperbarui menjadi Dalam Validasi Produk.');
    }

    public function edit($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || $link['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Link validasi produk tidak ditemukan.');
        }

        $data = [
            'title'       => 'Edit Link Validasi Produk',
            'link'        => $link,
            'products'    => $this->productModel
                ->orderBy('nama_produk', 'ASC')
                ->findAll(),
            'instruments' => $this->getValidInstruments((int) $link['product_id']),
            'action'      => base_url('admin/validasi-produk/' . $id),
            'method'      => 'put',
        ];

        return view('admin/links/form', $data);
    }

    public function update($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || $link['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Link validasi produk tidak ditemukan.');
        }

        $rules = [
            'product_id'      => 'required|integer',
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

        $productId    = (int) $this->request->getPost('product_id');
        $instrumentId = (int) $this->request->getPost('instrument_id');

        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument || $instrument['status'] !== 'Valid') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen harus berstatus Valid.');
        }

        $isLinked = $this->productInstrumentModel
            ->where('product_id', $productId)
            ->where('instrument_id', $instrumentId)
            ->first();

        if (!$isLinked) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen belum dihubungkan dengan produk ini.');
        }

        $this->linkModel->update($id, [
            'product_id'      => $productId,
            'instrument_id'   => $instrumentId,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        return redirect()
            ->to(base_url('admin/validasi-produk'))
            ->with('success', 'Link validasi produk berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || $link['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Link validasi produk tidak ditemukan.');
        }

        $this->linkModel->delete($id);

        return redirect()
            ->to(base_url('admin/validasi-produk'))
            ->with('success', 'Link validasi produk berhasil dihapus.');
    }

    public function process($linkId = null)
    {
        $link = $this->linkModel->find($linkId);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Link validasi produk tidak ditemukan.');
        }

        if ($link['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Mode link bukan validasi produk.');
        }

        if (empty($link['product_id'])) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Link validasi produk belum memiliki produk.');
        }

        $responses = $this->responseModel
            ->where('instrument_link_id', $link['id'])
            ->where('mode', 'validasi_produk')
            ->findAll();

        if (empty($responses)) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Belum ada hasil pengisian validator produk untuk link ini.');
        }

        $instrumentId = (int) $link['instrument_id'];
        $productId    = (int) $link['product_id'];

        $responseIds = array_map(static function ($response) {
            return (int) $response['id'];
        }, $responses);

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->orderBy('urutan', 'ASC')
            ->orderBy('nomor', 'ASC')
            ->findAll();

        if (empty($items)) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Butir yang dapat digunakan pada instrumen ini belum tersedia.');
        }

        $scaleItems = array_values(array_filter($items, static function ($item) {
            return ($item['tipe_butir'] ?? 'skala') === 'skala';
        }));

        if (empty($scaleItems)) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Butir skala pada instrumen ini belum tersedia, sehingga analisis skor belum dapat diproses.');
        }

        $aspects = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $answers = $this->getAnswersByResponses($responseIds);

        $instrument = $this->instrumentModel->find($instrumentId);

        $skalaMax = isset($instrument['skala_max']) ? (int) $instrument['skala_max'] : 4;

        if ($skalaMax <= 0) {
            $skalaMax = 4;
        }

        $jumlahValidator = count($responses);
        $jumlahButir     = count($scaleItems);
        $skorTertinggi   = $skalaMax;

        $scaleItemIds = array_map(static function ($item) {
            return (int) $item['id'];
        }, $scaleItems);

        $totalSkor = 0;

        foreach ($answers as $answer) {
            if (
                in_array((int) $answer['instrument_item_id'], $scaleItemIds, true)
                && $answer['skor'] !== null
                && $answer['skor'] !== ''
            ) {
                $totalSkor += (int) $answer['skor'];
            }
        }

        $skorMaksimal = $jumlahButir * $skorTertinggi * $jumlahValidator;

        $rataRata = $jumlahButir > 0 && $jumlahValidator > 0
            ? $totalSkor / ($jumlahButir * $jumlahValidator)
            : 0;

        $persentase = $skorMaksimal > 0
            ? ($totalSkor / $skorMaksimal) * 100
            : 0;

        $kategori = $this->kategoriValidasiProduk($persentase);

        $db = db_connect();
        $db->transStart();

        $oldAnalyses = $this->analysisResultModel
            ->where('instrument_link_id', (int) $link['id'])
            ->where('mode', 'validasi_produk')
            ->findAll();

        foreach ($oldAnalyses as $oldAnalysis) {
            $this->analysisResultModel->delete((int) $oldAnalysis['id']);
        }

        $analysisResultId = $this->analysisResultModel->insert([
            'instrument_id'      => $instrumentId,
            'instrument_link_id' => (int) $link['id'],
            'product_id'         => $productId,
            'mode'               => 'validasi_produk',
            'jumlah_responden'   => $jumlahValidator,
            'jumlah_butir'       => $jumlahButir,
            'total_skor'         => $totalSkor,
            'skor_maksimal'      => $skorMaksimal,
            'rata_rata'          => round($rataRata, 2),
            'persentase'         => round($persentase, 2),
            'kategori'           => $kategori,
            'catatan'            => 'Analisis otomatis validasi produk.',
        ], true);

        foreach ($aspects as $aspect) {
            $aspectItems = array_values(array_filter($scaleItems, static function ($item) use ($aspect) {
                return (int) $item['aspect_id'] === (int) $aspect['id'];
            }));

            if (empty($aspectItems)) {
                continue;
            }

            $aspectItemIds = array_map(static function ($item) {
                return (int) $item['id'];
            }, $aspectItems);

            $aspectTotalSkor = 0;

            foreach ($answers as $answer) {
                if (in_array((int) $answer['instrument_item_id'], $aspectItemIds, true)) {
                    $aspectTotalSkor += (int) $answer['skor'];
                }
            }

            $aspectSkorMaksimal = count($aspectItems) * $skorTertinggi * $jumlahValidator;

            $aspectRataRata = count($aspectItems) > 0 && $jumlahValidator > 0
                ? $aspectTotalSkor / (count($aspectItems) * $jumlahValidator)
                : 0;

            $aspectPersentase = $aspectSkorMaksimal > 0
                ? ($aspectTotalSkor / $aspectSkorMaksimal) * 100
                : 0;

            $this->analysisAspectModel->insert([
                'analysis_result_id' => (int) $analysisResultId,
                'aspect_id'          => (int) $aspect['id'],
                'total_skor'         => $aspectTotalSkor,
                'skor_maksimal'      => $aspectSkorMaksimal,
                'rata_rata'          => round($aspectRataRata, 2),
                'persentase'         => round($aspectPersentase, 2),
                'kategori'           => $this->kategoriValidasiProduk($aspectPersentase),
            ]);
        }

        foreach ($scaleItems as $item) {
            $itemTotalSkor = 0;

            foreach ($answers as $answer) {
                if (
                    (int) $answer['instrument_item_id'] === (int) $item['id']
                    && $answer['skor'] !== null
                    && $answer['skor'] !== ''
                ) {
                    $itemTotalSkor += (int) $answer['skor'];
                }
            }

            $itemRataRata = $jumlahValidator > 0
                ? $itemTotalSkor / $jumlahValidator
                : 0;

            $this->analysisItemModel->insert([
                'analysis_result_id' => (int) $analysisResultId,
                'instrument_item_id' => (int) $item['id'],
                'total_skor'         => $itemTotalSkor,
                'rata_rata'          => round($itemRataRata, 2),
                'kategori'           => $this->kategoriButirProduk($itemRataRata, $skorTertinggi),
                'rekomendasi'        => $this->rekomendasiButirProduk($itemRataRata, $skorTertinggi),
            ]);
        }

        $this->workflowStatusService->markProductValidated($kategori, $productId);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Analisis validasi produk gagal diproses.');
        }

        return redirect()
            ->to(base_url('admin/validasi-produk/analisis/' . $analysisResultId))
            ->with('success', 'Analisis validasi produk berhasil diproses.');
    }

    public function analysis($analysisResultId = null)
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis || $analysis['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Data analisis validasi produk tidak ditemukan.');
        }

        $linkRow = $this->linkModel->find((int) $analysis['instrument_link_id']);

        if (!$linkRow) {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Link validasi produk tidak ditemukan.');
        }

        $link = $this->linkModel->findByToken($linkRow['token']);

        $responses = $this->responseModel
            ->getWithRespondentByLink((int) $analysis['instrument_link_id']);

        $aspectAnalysis = $this->analysisAspectModel
            ->getByAnalysis((int) $analysisResultId);

        $itemAnalysis = $this->analysisItemModel
            ->getByAnalysis((int) $analysisResultId);

        $comments = $this->getItemComments((int) $analysis['instrument_link_id']);

        $data = [
            'title'          => 'Hasil Analisis Validasi Produk',
            'analysis'       => $analysis,
            'link'           => $link,
            'responses'      => $responses,
            'aspectAnalysis' => $aspectAnalysis,
            'itemAnalysis'   => $itemAnalysis,
            'comments'       => $comments,
        ];

        return view('admin/validations/product_analysis', $data);
    }

    public function show($linkId = null)
    {
        $analysis = $this->analysisResultModel->getLatestByLink((int) $linkId);

        if (!$analysis || $analysis['mode'] !== 'validasi_produk') {
            return redirect()
                ->to(base_url('admin/validasi-produk'))
                ->with('error', 'Analisis validasi produk belum tersedia. Silakan proses analisis terlebih dahulu.');
        }

        return redirect()->to(base_url('admin/validasi-produk/analisis/' . $analysis['id']));
    }

    private function getValidInstruments(int $productId = 0): array
    {
        $builder = $this->instrumentModel
            ->select('instruments.*')
            ->join('product_instruments', 'product_instruments.instrument_id = instruments.id')
            ->where('instruments.status', 'Valid');

        if ($productId > 0) {
            $builder->where('product_instruments.product_id', $productId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->findAll();
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = bin2hex(random_bytes(8));
            $exists = $this->linkModel->where('token', $token)->first();
        } while ($exists);

        return $token;
    }

    private function emptyToNull($value)
    {
        return $value === '' || $value === null ? null : $value;
    }

    private function getAnswersByResponses(array $responseIds): array
    {
        if (empty($responseIds)) {
            return [];
        }

        return $this->answerModel
            ->whereIn('response_id', $responseIds)
            ->findAll();
    }

    private function getItemComments(int $instrumentLinkId): array
    {
        return $this->answerModel
            ->select(
                'response_answers.*,
                 instrument_items.nomor,
                 instrument_items.pernyataan,
                 respondents.nama'
            )
            ->join('responses', 'responses.id = response_answers.response_id')
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_items', 'instrument_items.id = response_answers.instrument_item_id')
            ->where('responses.instrument_link_id', $instrumentLinkId)
            ->where('response_answers.komentar !=', '')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }

    private function kategoriValidasiProduk(float $persentase): string
    {
        if ($persentase >= 85) {
            return 'Sangat Layak';
        }

        if ($persentase >= 70) {
            return 'Layak';
        }

        if ($persentase >= 55) {
            return 'Kurang Layak';
        }

        return 'Tidak Layak';
    }

    private function kategoriButirProduk(float $rataRata, int $skalaMax = 4): string
    {
        if ($skalaMax <= 0) {
            $skalaMax = 4;
        }

        $persentase = ($rataRata / $skalaMax) * 100;

        if ($persentase >= 85) {
            return 'Sangat Sesuai';
        }

        if ($persentase >= 70) {
            return 'Sesuai';
        }

        if ($persentase >= 55) {
            return 'Kurang Sesuai';
        }

        return 'Tidak Sesuai';
    }

    private function rekomendasiButirProduk(float $rataRata, int $skalaMax = 4): string
    {
        if ($skalaMax <= 0) {
            $skalaMax = 4;
        }

        $persentase = ($rataRata / $skalaMax) * 100;

        if ($persentase >= 85) {
            return 'Dipertahankan';
        }

        if ($persentase >= 70) {
            return 'Revisi kecil';
        }

        if ($persentase >= 55) {
            return 'Revisi besar';
        }

        return 'Ganti atau hapus';
    }

}
