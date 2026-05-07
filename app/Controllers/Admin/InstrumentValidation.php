<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WorkflowStatusService;
use App\Models\AnalysisAspectModel;
use App\Models\AnalysisItemModel;
use App\Models\AnalysisResultModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentModel;
use App\Models\InstrumentItemModel;
use App\Models\InstrumentLinkModel;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class InstrumentValidation extends BaseController
{
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
        $this->linkModel           = new InstrumentLinkModel();
        $this->responseModel       = new ResponseModel();
        $this->answerModel         = new ResponseAnswerModel();
        $this->itemModel           = new InstrumentItemModel();
        $this->aspectModel         = new InstrumentAspectModel();
        $this->analysisResultModel = new AnalysisResultModel();
        $this->analysisAspectModel = new AnalysisAspectModel();
        $this->analysisItemModel   = new AnalysisItemModel();
        $this->workflowStatusService = new WorkflowStatusService();
    }

    public function index()
    {
        $links = $this->linkModel->getWithInstrument('validasi_instrumen');

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
            $link['analysis'] = $this->analysisResultModel->getLatestByLink((int) $link['id']);
        }

        unset($link);

        $data = [
            'title' => 'Analisis Validasi Instrumen',
            'links' => $links,
        ];

        return view('admin/validations/instrument_result', $data);
    }

    public function valid()
    {
        $instrumentModel = new InstrumentModel();

        $instruments = $instrumentModel
            ->whereIn('status', ['Valid', 'Siap Disebar'])
            ->orderBy('judul', 'ASC')
            ->findAll();

        $data = [
            'title'       => 'Instrumen Valid',
            'instruments' => $instruments,
        ];

        return view('admin/validations/valid_instruments', $data);
    }

    public function process($linkId = null)
    {
        $link = $this->linkModel->find($linkId);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        if ($link['mode'] !== 'validasi_instrumen') {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Mode link bukan validasi instrumen.');
        }

        $responses = $this->responseModel
            ->where('instrument_link_id', $link['id'])
            ->where('mode', 'validasi_instrumen')
            ->findAll();

        if (empty($responses)) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Belum ada hasil pengisian validator untuk link ini.');
        }

        $instrumentId = (int) $link['instrument_id'];
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
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Butir yang dapat digunakan pada instrumen ini belum tersedia.');
        }

        $scaleItems = array_values(array_filter($items, static function ($item) {
            return ($item['tipe_butir'] ?? 'skala') === 'skala';
        }));

        if (empty($scaleItems)) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Butir skala pada instrumen ini belum tersedia, sehingga analisis skor belum dapat diproses.');
        }

        $aspects = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $answers = $this->getAnswersByResponses($responseIds);

        $instrumentModel = new InstrumentModel();
        $instrument = $instrumentModel->find($instrumentId);

        $skalaMax = isset($instrument['skala_max']) ? (int) $instrument['skala_max'] : 4;

        if ($skalaMax <= 0) {
            $skalaMax = 4;
        }

        $jumlahResponden = count($responses);
        $jumlahButir = count($scaleItems);
        $skorTertinggi = $skalaMax;

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

        $skorMaksimal = $jumlahButir * $skorTertinggi * $jumlahResponden;
        $rataRata = $jumlahButir > 0 && $jumlahResponden > 0
            ? $totalSkor / ($jumlahButir * $jumlahResponden)
            : 0;
        $persentase = $skorMaksimal > 0
            ? ($totalSkor / $skorMaksimal) * 100
            : 0;

        $kategori = $this->kategoriValidasiInstrumen($persentase);

        $db = db_connect();
        $db->transStart();

        /*
         * Hapus analisis lama untuk link ini agar hasil terbaru tidak dobel.
         */
        $oldAnalyses = $this->analysisResultModel
            ->where('instrument_link_id', $link['id'])
            ->findAll();

        foreach ($oldAnalyses as $oldAnalysis) {
            $this->analysisResultModel->delete((int) $oldAnalysis['id']);
        }

        $analysisResultId = $this->analysisResultModel->insert([
            'instrument_id'      => $instrumentId,
            'instrument_link_id' => (int) $link['id'],
            'product_id'         => null,
            'mode'               => 'validasi_instrumen',
            'jumlah_responden'   => $jumlahResponden,
            'jumlah_butir'       => $jumlahButir,
            'total_skor'         => $totalSkor,
            'skor_maksimal'      => $skorMaksimal,
            'rata_rata'          => round($rataRata, 2),
            'persentase'         => round($persentase, 2),
            'kategori'           => $kategori,
            'catatan'            => 'Analisis otomatis validasi instrumen.',
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

            $aspectSkorMaksimal = count($aspectItems) * $skorTertinggi * $jumlahResponden;
            $aspectRataRata = count($aspectItems) > 0 && $jumlahResponden > 0
                ? $aspectTotalSkor / (count($aspectItems) * $jumlahResponden)
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
                'kategori'           => $this->kategoriValidasiInstrumen($aspectPersentase),
            ]);
        }

        $hasRevisionRecommendation = false;

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

            $itemRataRata = $jumlahResponden > 0
                ? $itemTotalSkor / $jumlahResponden
                : 0;

            $itemCategory = $this->kategoriButir($itemRataRata, $skorTertinggi);
            $itemRecommendation = $this->rekomendasiButir($itemRataRata, $skorTertinggi);

            if (in_array($itemRecommendation, ['Revisi kecil', 'Revisi besar', 'Ganti atau hapus'], true)) {
                $hasRevisionRecommendation = true;
            }

            $this->analysisItemModel->insert([
                'analysis_result_id' => (int) $analysisResultId,
                'instrument_item_id' => (int) $item['id'],
                'total_skor'         => $itemTotalSkor,
                'rata_rata'          => round($itemRataRata, 2),
                'kategori'           => $itemCategory,
                'rekomendasi'        => $itemRecommendation,
            ]);
        }

        if ($hasRevisionRecommendation) {
            $this->workflowStatusService->markInstrumentNeedRevision($instrumentId);
        } else {
            $this->workflowStatusService->markInstrumentReadyToSetValid($instrumentId);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Analisis gagal diproses.');
        }

        return redirect()
            ->to(base_url('admin/validasi-instrumen/analisis/' . $analysisResultId))
            ->with('success', 'Analisis validasi instrumen berhasil diproses.');
    }

    public function analysis($analysisResultId = null)
    {
        $analysis = $this->analysisResultModel->find($analysisResultId);

        if (!$analysis) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Data analisis tidak ditemukan.');
        }

        $link = $this->linkModel->findByToken(
            $this->linkModel->find((int) $analysis['instrument_link_id'])['token']
        );

        $responses = $this->responseModel
            ->getWithRespondentByLink((int) $analysis['instrument_link_id']);

        $aspectAnalysis = $this->analysisAspectModel
            ->getByAnalysis((int) $analysisResultId);

        $itemAnalysis = $this->analysisItemModel
            ->getByAnalysis((int) $analysisResultId);

        $comments = $this->getItemComments((int) $analysis['instrument_link_id']);

        $data = [
            'title'          => 'Hasil Analisis Validasi Instrumen',
            'analysis'       => $analysis,
            'link'           => $link,
            'responses'      => $responses,
            'aspectAnalysis' => $aspectAnalysis,
            'itemAnalysis'   => $itemAnalysis,
            'comments'       => $comments,
        ];

        return view('admin/validations/instrument_analysis', $data);
    }

    public function show($linkId = null)
    {
        $analysis = $this->analysisResultModel->getLatestByLink((int) $linkId);

        if (!$analysis) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Analisis belum tersedia. Silakan proses analisis terlebih dahulu.');
        }

        return redirect()->to(base_url('admin/validasi-instrumen/analisis/' . $analysis['id']));
    }

    public function setValid($linkId = null)
    {
        $link = $this->linkModel->find($linkId);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        if ($link['mode'] !== 'validasi_instrumen') {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Mode link bukan validasi instrumen.');
        }

        $analysis = $this->analysisResultModel->getLatestByLink((int) $link['id']);

        if (!$analysis) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Instrumen belum memiliki hasil analisis. Silakan proses analisis terlebih dahulu.');
        }

        $instrumentId = (int) $link['instrument_id'];

        $itemAnalyses = $this->analysisItemModel
            ->where('analysis_result_id', (int) $analysis['id'])
            ->findAll();

        $itemsNeedRevision = array_filter($itemAnalyses, static function ($item) {
            return in_array($item['rekomendasi'], [
                'Revisi kecil',
                'Revisi besar',
                'Ganti atau hapus',
            ], true);
        });

        /*
         * Sistem tetap mengizinkan penetapan valid walaupun ada rekomendasi revisi,
         * karena bisa saja revisi sudah dilakukan setelah analisis.
         */
        $instrumentModel = new InstrumentModel();

        $instrument = $instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/validasi-instrumen'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $this->workflowStatusService->markInstrumentValid($instrumentId);

        $this->linkModel->update((int) $link['id'], [
            'status' => 'Ditutup',
        ]);

        $message = 'Instrumen berhasil ditetapkan sebagai Valid. Link validasi instrumen ditutup agar tidak ada pengisian tambahan.';

        if (!empty($itemsNeedRevision)) {
            $message .= ' Catatan: masih ada butir yang pada analisis terakhir direkomendasikan untuk revisi. Pastikan revisi sudah dilakukan sebelum instrumen digunakan.';
        }

        return redirect()
            ->to(base_url('admin/validasi-instrumen/analisis/' . $analysis['id']))
            ->with('success', $message);
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

    private function kategoriValidasiInstrumen(float $persentase): string
    {
        if ($persentase >= 85) {
            return 'Layak digunakan tanpa revisi';
        }

        if ($persentase >= 70) {
            return 'Layak digunakan dengan revisi kecil';
        }

        if ($persentase >= 55) {
            return 'Perlu revisi besar sebelum digunakan';
        }

        return 'Tidak layak digunakan';
    }

    private function kategoriButir(float $rataRata, int $skalaMax = 4): string
    {
        if ($skalaMax <= 0) {
            $skalaMax = 4;
        }

        $persentase = ($rataRata / $skalaMax) * 100;

        if ($persentase >= 85) {
            return 'Sangat Relevan';
        }

        if ($persentase >= 70) {
            return 'Cukup Relevan';
        }

        if ($persentase >= 55) {
            return 'Kurang Relevan';
        }

        return 'Tidak Relevan';
    }

    private function rekomendasiButir(float $rataRata, int $skalaMax = 4): string
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
