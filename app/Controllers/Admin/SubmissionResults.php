<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use Config\Pager;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ResearchProductModel;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class SubmissionResults extends BaseController
{
    protected ResponseModel $responseModel;
    protected ResponseAnswerModel $answerModel;
    protected InstrumentModel $instrumentModel;
    protected InstrumentLinkModel $linkModel;
    protected ResearchProductModel $productModel;
    protected AuditLogService $auditLog;

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
        $this->responseModel   = new ResponseModel();
        $this->answerModel     = new ResponseAnswerModel();
        $this->instrumentModel = new InstrumentModel();
        $this->linkModel       = new InstrumentLinkModel();
        $this->productModel    = new ResearchProductModel();
        $this->auditLog        = new AuditLogService();
    }

    public function index()
    {
        $filters = $this->getFilters();
        $perPage = config(Pager::class)->perPage;
        $currentPage = max(1, (int) ($this->request->getGet('page_submissions') ?? 1));

        $builder = $this->getResponsesQuery();
        $this->applyResponseFilters($builder, $filters);

        $data = [
            'title'        => 'Hasil Pengisian',
            'mode'         => $filters['mode'],
            'filters'      => $filters,
            'allowedModes' => $this->allowedModes,
            'responses'    => $builder
                ->orderBy('responses.id', 'DESC')
                ->paginate($perPage, 'submissions'),
            'pager'        => $this->responseModel->pager,
            'offset'       => ($currentPage - 1) * $perPage,
            'instruments'  => $this->getInstrumentOptions(),
            'links'        => $this->getLinkOptions(),
            'products'     => $this->getProductOptions(),
        ];

        return view('admin/submissions/index', $data);
    }

    public function export()
    {
        $filters = $this->getFilters();
        $rows = $this->getExportRows($filters);

        $filename = 'hasil-pengisian-' . date('Ymd-His') . '.csv';
        $csv = $this->buildCsv($rows);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
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

        $this->auditLog->log(
            AuditLogService::ACTION_DELETE_SUBMISSION,
            AuditLogService::ENTITY_RESPONSE,
            $responseId,
            'Hapus data pengisian mode=' . $response['mode']
        );

        return redirect()
            ->to(base_url('admin/submissions?mode=' . $response['mode']))
            ->with('success', 'Data pengisian berhasil dihapus.');
    }

    private function getFilters(): array
    {
        $mode = trim((string) $this->request->getGet('mode'));

        if (!in_array($mode, $this->allowedModes, true)) {
            $mode = '';
        }

        return [
            'mode'               => $mode,
            'instrument_id'      => $this->getPositiveIntFilter('instrument_id'),
            'instrument_link_id' => $this->getPositiveIntFilter('instrument_link_id'),
            'product_id'         => $this->getPositiveIntFilter('product_id'),
            'date_from'          => $this->getDateFilter('date_from'),
            'date_to'            => $this->getDateFilter('date_to'),
        ];
    }

    private function getPositiveIntFilter(string $key): string
    {
        $value = $this->request->getGet($key);

        if ($value === null || $value === '') {
            return '';
        }

        $intValue = (int) $value;

        return $intValue > 0 ? (string) $intValue : '';
    }

    private function getDateFilter(string $key): string
    {
        $value = trim((string) $this->request->getGet($key));

        if ($value === '') {
            return '';
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1 ? $value : '';
    }

    private function getResponsesQuery()
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
                 research_products.nama_produk'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left');
    }

    private function applyResponseFilters($builder, array $filters): void
    {
        if ($filters['mode'] !== '') {
            $builder->where('responses.mode', $filters['mode']);
        }

        if ($filters['instrument_id'] !== '') {
            $builder->where('responses.instrument_id', (int) $filters['instrument_id']);
        }

        if ($filters['instrument_link_id'] !== '') {
            $builder->where('responses.instrument_link_id', (int) $filters['instrument_link_id']);
        }

        if ($filters['product_id'] !== '') {
            $builder->where('responses.product_id', (int) $filters['product_id']);
        }

        if ($filters['date_from'] !== '') {
            $builder->where('responses.submitted_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if ($filters['date_to'] !== '') {
            $builder->where('responses.submitted_at <=', $filters['date_to'] . ' 23:59:59');
        }
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

    private function getInstrumentOptions(): array
    {
        return $this->instrumentModel
            ->select('id, kode, judul')
            ->orderBy('judul', 'ASC')
            ->findAll();
    }

    private function getLinkOptions(): array
    {
        return $this->linkModel
            ->select(
                'instrument_links.id,
                 instrument_links.judul_link,
                 instrument_links.mode,
                 instruments.kode'
            )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->orderBy('instrument_links.id', 'DESC')
            ->findAll();
    }

    private function getProductOptions(): array
    {
        return $this->productModel
            ->select('id, kode, nama_produk')
            ->orderBy('nama_produk', 'ASC')
            ->findAll();
    }

    private function getExportRows(array $filters): array
    {
        $builder = $this->answerModel
            ->select(
                'responses.id AS response_id,
                 responses.mode,
                 responses.status,
                 responses.komentar_umum,
                 responses.kesimpulan,
                 responses.submitted_at,
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
                 instruments.kode AS instrument_kode,
                 instruments.judul AS instrument_judul,
                 research_products.kode AS product_kode,
                 research_products.nama_produk,
                 instrument_aspects.nama_aspek,
                 instrument_items.nomor,
                 instrument_items.tipe_butir,
                 instrument_items.wajib,
                 instrument_items.pernyataan,
                 response_answers.skor,
                 response_answers.jawaban_teks,
                 response_answers.komentar'
            )
            ->join('responses', 'responses.id = response_answers.response_id')
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left')
            ->join('instrument_items', 'instrument_items.id = response_answers.instrument_item_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id', 'left');

        $this->applyResponseFilters($builder, $filters);

        return $builder
            ->orderBy('responses.id', 'DESC')
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }

    private function buildCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            'response_id',
            'mode',
            'status',
            'submitted_at',
            'nama',
            'email',
            'jenis_responden',
            'nim',
            'program_studi',
            'kelas',
            'semester',
            'instansi',
            'bidang_keahlian',
            'judul_link',
            'instrument_kode',
            'instrument_judul',
            'product_kode',
            'nama_produk',
            'nama_aspek',
            'nomor_butir',
            'tipe_butir',
            'wajib',
            'pernyataan',
            'skor',
            'jawaban_teks',
            'komentar',
            'komentar_umum',
            'kesimpulan',
        ]);

        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['response_id'],
                $row['mode'],
                $row['status'],
                $row['submitted_at'],
                $row['nama'],
                $row['email'],
                $row['jenis_responden'],
                $row['nim'],
                $row['program_studi'],
                $row['kelas'],
                $row['semester'],
                $row['instansi'],
                $row['bidang_keahlian'],
                $row['judul_link'],
                $row['instrument_kode'],
                $row['instrument_judul'],
                $row['product_kode'],
                $row['nama_produk'],
                $row['nama_aspek'],
                $row['nomor'],
                $row['tipe_butir'],
                $row['wajib'],
                $row['pernyataan'],
                $row['skor'],
                $row['jawaban_teks'],
                $row['komentar'],
                $row['komentar_umum'],
                $row['kesimpulan'],
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return "\xEF\xBB\xBF" . $csv;
    }
}
