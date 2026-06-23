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
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        helper('instrument_layout');

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

        $summaryBuilder = $this->getSubmissionSummaryQuery();
        $this->applyResponseFilters($summaryBuilder, $filters);

        $data = [
            'title'        => 'Hasil Pengisian',
            'mode'         => $filters['mode'],
            'filters'      => $filters,
            'allowedModes' => $this->allowedModes,
            'summaries'    => $summaryBuilder
                ->orderBy('last_submitted_at', 'DESC')
                ->findAll(),
            'pager'        => null,
            'offset'       => ($currentPage - 1) * $perPage,
            'instruments'  => $this->getInstrumentOptions(),
            'instrumentTypes' => $this->getInstrumentTypeOptions(),
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

    public function exportExcel()
    {
        $filters = $this->getFilters();
        $matrix = $this->getExcelScoreMatrix($filters);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Skor');

        $sheet->fromArray($matrix['headers'], null, 'A1');
        $rowNumber = 2;

        foreach ($matrix['rows'] as $row) {
            $sheet->fromArray($row, null, 'A' . $rowNumber);
            $rowNumber++;
        }

        $highestColumn = $sheet->getHighestColumn();
        $highestRow = max(1, $sheet->getHighestRow());

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E2EC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($columnIndex = 1; $columnIndex <= $highestColumnIndex; $columnIndex++) {
            $column = Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $highestColumn . '1');
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(24);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(24);
        $sheet->getColumnDimension('L')->setWidth(28);
        $sheet->getColumnDimension('N')->setWidth(34);
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        $filename = 'rekap-hasil-pengisian-' . date('Ymd-His') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($this->spreadsheetToString($spreadsheet));
    }

    public function exportWord()
    {
        $matrix = $this->getExportMatrix($this->getFilters());
        $html = $this->buildLandscapeExportHtml($matrix, 'Rekap Hasil Pengisian');
        $filename = 'rekap-hasil-pengisian-' . date('Ymd-His') . '.doc';

        return $this->response
            ->setHeader('Content-Type', 'application/msword; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($html);
    }

    public function exportPdf()
    {
        $matrix = $this->getExportMatrix($this->getFilters());
        $html = $this->buildLandscapeExportHtml($matrix, 'Rekap Hasil Pengisian');
        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'rekap-hasil-pengisian-' . date('Ymd-His') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function exportReport()
    {
        $filters = $this->getFilters();
        $types = $this->getReportInstrumentTypes($filters);

        if ($types === []) {
            return redirect()
                ->to(base_url('admin/submissions?' . http_build_query(array_filter($filters))))
                ->with('error', 'Belum ada hasil pengisian untuk diekspor.');
        }

        if (count($types) > 1) {
            return redirect()
                ->to(base_url('admin/submissions?' . http_build_query(array_filter($filters))))
                ->with('error', 'Filter dulu satu instrumen atau satu link pengisian agar format laporan tidak bercampur.');
        }

        $jenis = array_values($types)[0];
        $format = $this->reportFormatForInstrumentType($jenis);

        if ($format === 'word') {
            return $this->exportTypedWordReport($filters, $jenis);
        }

        return $this->exportTypedExcelReport($filters, $jenis);
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
                 instrument_items.sumber_dokumen,
                 instrument_items.skor_1_deskripsi,
                 instrument_items.skor_2_deskripsi,
                 instrument_items.skor_3_deskripsi,
                 instrument_items.skor_4_deskripsi,
                 instrument_items.skor_5_deskripsi,
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
        $response = $this->findOwnedResponse($responseId);

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
            ->to(base_url('admin/submissions'))
            ->with('success', 'Data pengisian berhasil dihapus.');
    }

    public function deleteSummary()
    {
        $linkId = (int) $this->request->getPost('instrument_link_id');

        if ($linkId <= 0) {
            return redirect()
                ->to(base_url('admin/submissions'))
                ->with('error', 'Rekap hasil pengisian tidak valid.');
        }

        $responses = $this->responseModel
            ->scopeOwned('responses.user_id')
            ->select('id')
            ->where('instrument_link_id', $linkId)
            ->findAll();

        if (empty($responses)) {
            return redirect()
                ->to(base_url('admin/submissions'))
                ->with('error', 'Data pengisian pada rekap ini tidak ditemukan.');
        }

        $responseIds = array_map(static fn (array $row): int => (int) $row['id'], $responses);
        $db = db_connect();
        $db->transBegin();

        $this->answerModel
            ->whereIn('response_id', $responseIds)
            ->delete();

        $this->responseModel
            ->whereIn('id', $responseIds)
            ->delete();

        if ($db->transStatus() === false) {
            $db->transRollback();

            return redirect()
                ->to(base_url('admin/submissions'))
                ->with('error', 'Data pengisian gagal dihapus. Silakan coba lagi.');
        }

        $db->transCommit();

        $this->auditLog->log(
            AuditLogService::ACTION_DELETE_SUBMISSION,
            AuditLogService::ENTITY_RESPONSE,
            $linkId,
            'Hapus rekap hasil pengisian link_id=' . $linkId . ', total=' . count($responseIds)
        );

        return redirect()
            ->to(base_url('admin/submissions'))
            ->with('success', count($responseIds) . ' data pengisian berhasil dihapus.');
    }

    private function getFilters(): array
    {
        return [
            'mode'               => '',
            'jenis'              => trim((string) $this->request->getGet('jenis')),
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
                 respondents.identity_data,
                 instrument_links.judul_link,
                 instrument_links.identity_template,
                 instrument_links.identity_fields,
                 instrument_links.justification_config,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 research_products.nama_produk'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left');

        $this->applyOwnerScope($builder, 'responses.user_id');

        return $builder;
    }

    private function getSubmissionSummaryQuery()
    {
        $builder = $this->responseModel
            ->select(
                'responses.instrument_id,
                 responses.instrument_link_id,
                 responses.product_id,
                 COUNT(responses.id) AS total_responses,
                 MAX(responses.submitted_at) AS last_submitted_at,
                 instrument_links.judul_link,
                 instrument_links.identity_template,
                 instrument_links.identity_fields,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 research_products.nama_produk'
            )
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left')
            ->groupBy(
                'responses.instrument_id,
                 responses.instrument_link_id,
                 responses.product_id,
                 instrument_links.judul_link,
                 instrument_links.identity_template,
                 instrument_links.identity_fields,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 research_products.nama_produk'
            );

        $this->applyOwnerScope($builder, 'responses.user_id');

        return $builder;
    }

    private function applyResponseFilters($builder, array $filters): void
    {
        if ($filters['mode'] !== '') {
            $builder->where('responses.mode', $filters['mode']);
        }

        if (($filters['jenis'] ?? '') !== '') {
            $builder->where('instruments.jenis', $filters['jenis']);
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
                 respondents.identity_data,
                 instrument_links.judul_link,
                 instrument_links.identity_template,
                 instrument_links.identity_fields,
                 instrument_links.justification_config,
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
            ->join('research_products', 'research_products.id = responses.product_id', 'left');

        $this->applyOwnerScope($builder, 'responses.user_id');

        return $builder
            ->where('responses.id', $responseId)
            ->first();
    }

    private function getInstrumentOptions(): array
    {
        return $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->select('id, kode, judul')
            ->orderBy('judul', 'ASC')
            ->findAll();
    }

    private function getInstrumentTypeOptions(): array
    {
        $rows = $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->select('jenis')
            ->where('jenis IS NOT NULL', null, false)
            ->groupBy('jenis')
            ->orderBy('jenis', 'ASC')
            ->findAll();

        $types = [];

        foreach ($rows as $row) {
            $jenis = trim((string) ($row['jenis'] ?? ''));

            if ($jenis !== '') {
                $types[] = $jenis;
            }
        }

        return $types;
    }

    private function getLinkOptions(): array
    {
        return $this->linkModel
            ->scopeOwned('instrument_links.user_id')
            ->select(
                'instrument_links.id,
                 instrument_links.judul_link,
                 instrument_links.mode,
                 instrument_links.identity_template,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis'
            )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->orderBy('instrument_links.id', 'DESC')
            ->findAll();
    }

    private function getProductOptions(): array
    {
        return $this->productModel
            ->scopeOwned('research_products.user_id')
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
                 responses.justification_data,
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
                 respondents.identity_data,
                 instrument_links.judul_link,
                 instruments.kode AS instrument_kode,
                 instruments.judul AS instrument_judul,
                 instruments.jenis AS instrument_jenis,
                 research_products.kode AS product_kode,
                 research_products.nama_produk,
                 instrument_aspects.nama_aspek,
                 instrument_items.nomor,
                 instrument_items.tipe_butir,
                 instrument_items.wajib,
                 instrument_items.pernyataan,
                 instrument_items.sumber_dokumen,
                 instrument_items.skor_1_deskripsi,
                 instrument_items.skor_2_deskripsi,
                 instrument_items.skor_3_deskripsi,
                 instrument_items.skor_4_deskripsi,
                 instrument_items.skor_5_deskripsi,
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

        $this->applyOwnerScope($builder, 'responses.user_id');
        $this->applyResponseFilters($builder, $filters);

        return $builder
            ->orderBy('responses.id', 'DESC')
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }

    private function getExportMatrix(array $filters): array
    {
        $responseBuilder = $this->getResponsesQuery();
        $this->applyResponseFilters($responseBuilder, $filters);
        $responses = $responseBuilder
            ->orderBy('responses.id', 'ASC')
            ->findAll();

        $responseIds = array_map(static fn (array $row): int => (int) $row['id'], $responses);

        $headers = [
            'No',
            'Response ID',
            'Waktu Submit',
            'Nama',
            'Email',
            'NIM/No. Identitas',
            'Program Studi',
            'Kelas',
            'Semester',
            'Instansi',
            'Bidang/Jabatan',
            'Judul Link',
            'Kode Instrumen',
            'Instrumen',
            'Produk',
            'Komentar/Saran',
            'Kesimpulan',
        ];

        $itemColumns = $this->getMatrixItemColumns($filters, $responses);
        $answersByResponse = $this->getMatrixAnswers($responseIds);
        $instrumentCodes = [];

        foreach ($itemColumns as $column) {
            $instrumentCodes[(string) ($column['instrument_kode'] ?? '')] = true;
        }

        $multiInstrument = count(array_filter(array_keys($instrumentCodes))) > 1;

        foreach ($itemColumns as $column) {
            $headers[] = $multiInstrument && $column['instrument_kode'] !== ''
                ? $column['instrument_kode'] . '-' . $column['nomor']
                : $column['nomor'];
        }

        $rows = [];

        foreach ($responses as $index => $response) {
            $responseId = (int) $response['id'];
            $row = [
                $index + 1,
                $responseId,
                $response['submitted_at'] ?? '',
                $response['nama'] ?? '',
                $response['email'] ?? '',
                $response['nim'] ?? '',
                $response['program_studi'] ?? '',
                $response['kelas'] ?? '',
                $response['semester'] ?? '',
                $response['instansi'] ?? '',
                $response['bidang_keahlian'] ?? '',
                $response['judul_link'] ?? '',
                $response['kode'] ?? '',
                $response['judul'] ?? '',
                $response['nama_produk'] ?? '',
                $response['komentar_umum'] ?? '',
                $response['kesimpulan'] ?? '',
            ];

            foreach (array_keys($itemColumns) as $columnKey) {
                $row[] = $answersByResponse[$responseId][$columnKey] ?? '';
            }

            $rows[] = $row;
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    private function getExcelScoreMatrix(array $filters): array
    {
        $responseBuilder = $this->getResponsesQuery();
        $this->applyResponseFilters($responseBuilder, $filters);
        $responses = $responseBuilder
            ->orderBy('responses.id', 'ASC')
            ->findAll();

        $responseIds = array_map(static fn (array $row): int => (int) $row['id'], $responses);
        $itemColumns = $this->getMatrixItemColumns($filters, $responses);
        $answersByResponse = $this->getMatrixAnswers($responseIds);
        $instrumentCodes = [];

        foreach ($itemColumns as $column) {
            $instrumentCodes[(string) ($column['instrument_kode'] ?? '')] = true;
        }

        $multiInstrument = count(array_filter(array_keys($instrumentCodes))) > 1;
        $headers = [
            'No',
            'Response ID',
            'Waktu Submit',
            'Nama',
            'Email',
            'NIM/No. Identitas',
            'Program Studi',
            'Kelas',
            'Semester',
            'Instansi',
            'Bidang/Jabatan',
            'Judul Link',
            'Kode Instrumen',
            'Instrumen',
            'Produk',
        ];

        foreach ($itemColumns as $column) {
            $itemLabel = $multiInstrument && $column['instrument_kode'] !== ''
                ? $column['instrument_kode'] . '-' . $column['nomor']
                : $column['nomor'];

            foreach ($this->matrixValueColumnsForItem($column, $itemLabel) as $header) {
                $headers[] = $header;
            }
        }

        $headers[] = 'Komentar/Saran';
        $headers[] = 'Kesimpulan';

        $rows = [];

        foreach ($responses as $index => $response) {
            $responseId = (int) $response['id'];
            $row = [
                $index + 1,
                $responseId,
                $response['submitted_at'] ?? '',
                $response['nama'] ?? '',
                $response['email'] ?? '',
                $response['nim'] ?? '',
                $response['program_studi'] ?? '',
                $response['kelas'] ?? '',
                $response['semester'] ?? '',
                $response['instansi'] ?? '',
                $response['bidang_keahlian'] ?? '',
                $response['judul_link'] ?? '',
                $response['kode'] ?? '',
                $response['judul'] ?? '',
                $response['nama_produk'] ?? '',
            ];

            foreach (array_keys($itemColumns) as $columnKey) {
                $answer = $answersByResponse[$responseId][$columnKey] ?? ['value' => '', 'comment' => ''];

                foreach ($this->matrixAnswerValuesForItem($itemColumns[$columnKey], $answer) as $value) {
                    $row[] = $value;
                }
            }

            $row[] = $response['komentar_umum'] ?? '';
            $row[] = $response['kesimpulan'] ?? '';

            $rows[] = $row;
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    private function getMatrixItemColumns(array $filters, array $responses): array
    {
        $builder = db_connect()->table('instrument_items')
            ->select(
                'instrument_items.id AS item_id,
                 instrument_items.nomor,
                 instrument_items.instrument_id,
                 instruments.kode AS instrument_kode,
                 instruments.jenis AS instrument_jenis'
            )
            ->join('instruments', 'instruments.id = instrument_items.instrument_id');

        $this->applyOwnerScope($builder, 'instrument_items.user_id');

        if ($filters['instrument_id'] !== '') {
            $builder->where('instrument_items.instrument_id', (int) $filters['instrument_id']);
        } elseif ($filters['instrument_link_id'] !== '') {
            $builder
                ->join('instrument_links', 'instrument_links.instrument_id = instrument_items.instrument_id')
                ->where('instrument_links.id', (int) $filters['instrument_link_id']);
        } elseif (!empty($responses)) {
            $instrumentIds = array_values(array_unique(array_map(static fn (array $row): int => (int) $row['instrument_id'], $responses)));
            $builder->whereIn('instrument_items.instrument_id', $instrumentIds);
        }

        $items = $builder
            ->orderBy('instruments.kode', 'ASC')
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->get()
            ->getResultArray();

        $columns = [];

        foreach ($items as $item) {
            $itemId = (int) $item['item_id'];
            $columns[(string) $itemId] = [
                'item_id' => $itemId,
                'nomor' => (string) ($item['nomor'] ?? $itemId),
                'instrument_kode' => (string) ($item['instrument_kode'] ?? ''),
                'instrument_jenis' => (string) ($item['instrument_jenis'] ?? ''),
            ];
        }

        return $columns;
    }

    private function matrixValueColumnsForItem(array $column, string $itemLabel): array
    {
        $layout = instrument_preview_layout($column['instrument_jenis'] ?? '');
        $layoutType = (string) ($layout['type'] ?? 'standard');
        $prefix = 'Butir ' . $itemLabel . ' ';

        return match ($layoutType) {
            'interview_guide' => [$prefix . (string) ($layout['answer'] ?? 'Jawaban')],
            'observation_guide' => [$prefix . (string) ($layout['result'] ?? 'Catatan Aktivitas')],
            'rubric_assessment' => [$prefix . 'Skor yang Diperoleh', $prefix . 'Catatan'],
            'performance_test' => [$prefix . 'Skor', $prefix . 'Catatan'],
            'document_review' => [$prefix . 'Skor', $prefix . 'Komentar'],
            'questionnaire',
            'product_validation_questionnaire',
            'user_response_questionnaire' => [$prefix . 'Skor', $prefix . 'Komentar'],
            default => [$prefix . 'Jawaban', $prefix . 'Komentar'],
        };
    }

    private function matrixAnswerValuesForItem(array $column, array $answer): array
    {
        $layoutType = (string) (instrument_preview_layout($column['instrument_jenis'] ?? '')['type'] ?? 'standard');
        $value = $answer['value'] ?? '';
        $comment = $answer['comment'] ?? '';

        return match ($layoutType) {
            'interview_guide',
            'observation_guide' => [$value],
            default => [$value, $comment],
        };
    }

    private function getMatrixAnswers(array $responseIds): array
    {
        if (empty($responseIds)) {
            return [];
        }

        $items = $this->answerModel
            ->select(
                'response_answers.response_id,
                 response_answers.instrument_item_id,
                 response_answers.skor,
                 response_answers.jawaban_teks,
                 response_answers.komentar'
            )
            ->whereIn('response_answers.response_id', $responseIds)
            ->findAll();

        $answersByResponse = [];

        foreach ($items as $item) {
            $responseId = (int) $item['response_id'];
            $columnKey = (string) ((int) $item['instrument_item_id']);
            $value = $item['skor'] !== null && $item['skor'] !== ''
                ? (string) $item['skor']
                : trim((string) ($item['jawaban_teks'] ?? ''));
            $comment = trim((string) ($item['komentar'] ?? ''));

            $answersByResponse[$responseId][$columnKey] = [
                'value' => $value,
                'comment' => $comment,
            ];
        }

        return $answersByResponse;
    }

    private function spreadsheetToString(Spreadsheet $spreadsheet): string
    {
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');

        return (string) ob_get_clean();
    }

    private function buildLandscapeExportHtml(array $matrix, string $title): string
    {
        $headers = $matrix['headers'] ?? [];
        $rows = $matrix['rows'] ?? [];

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <style>
                @page { size: A4 landscape; margin: 12mm; }
                body { font-family: Arial, sans-serif; font-size: 10px; color: #111827; }
                h1 { font-size: 16px; margin: 0 0 10px; }
                table { width: 100%; border-collapse: collapse; table-layout: fixed; }
                th, td { border: 1px solid #cbd5e1; padding: 4px 5px; vertical-align: top; word-wrap: break-word; }
                th { background: #eaf2f8; font-weight: bold; text-align: center; }
                .empty { padding: 14px; border: 1px solid #cbd5e1; color: #64748b; }
            </style>
        </head>
        <body>
            <h1><?= esc($title) ?></h1>
            <?php if (empty($rows)): ?>
                <div class="empty">Belum ada hasil pengisian.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <th><?= esc((string) $header) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                    <td><?= nl2br(esc((string) $cell)) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </body>
        </html>
        <?php

        return (string) ob_get_clean();
    }

    private function getReportInstrumentTypes(array $filters): array
    {
        $builder = $this->responseModel
            ->select('instruments.jenis')
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->join('instrument_links', 'instrument_links.id = responses.instrument_link_id')
            ->join('instruments', 'instruments.id = responses.instrument_id')
            ->join('research_products', 'research_products.id = responses.product_id', 'left');

        $this->applyOwnerScope($builder, 'responses.user_id');
        $this->applyResponseFilters($builder, $filters);
        $rows = $builder
            ->groupBy('instruments.jenis')
            ->findAll();

        $types = [];

        foreach ($rows as $row) {
            $jenis = trim((string) ($row['jenis'] ?? ''));

            if ($jenis !== '') {
                $types[$jenis] = $jenis;
            }
        }

        return $types;
    }

    private function reportFormatForInstrumentType(string $jenis): string
    {
        return in_array(instrument_type_key($jenis), [
            'panduan_analisis_perangkat_pembelajaran',
            'pedoman_wawancara',
            'pedoman_observasi',
        ], true) ? 'word' : 'excel';
    }

    private function exportTypedWordReport(array $filters, string $jenis)
    {
        $rows = $this->getExportRows($filters);
        $html = $this->buildTypedWordReportHtml($rows, $jenis);
        $filename = $this->safeExportFileName('laporan-' . instrument_preview_layout($jenis)['title']) . '-' . date('Ymd-His') . '.doc';

        return $this->response
            ->setHeader('Content-Type', 'application/msword; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($html);
    }

    private function buildTypedWordReportHtml(array $rows, string $jenis): string
    {
        $layout = instrument_preview_layout($jenis);
        $layoutType = (string) ($layout['type'] ?? 'standard');
        $grouped = [];

        foreach ($rows as $row) {
            $responseId = (int) ($row['response_id'] ?? 0);
            if (!isset($grouped[$responseId])) {
                $grouped[$responseId] = [
                    'meta' => $row,
                    'answers' => [],
                ];
            }

            $grouped[$responseId]['answers'][] = $row;
        }

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <style>
                @page { size: A4 portrait; margin: 16mm; }
                body { font-family: "Times New Roman", serif; font-size: 12pt; color: #111; }
                h1 { font-size: 16pt; text-align: center; margin: 0 0 14pt; }
                h2 { font-size: 13pt; margin: 14pt 0 7pt; }
                table { width: 100%; border-collapse: collapse; margin: 8pt 0 14pt; }
                th, td { border: 1px solid #111; padding: 5pt; vertical-align: top; }
                th { font-weight: bold; text-align: center; }
                .meta th { width: 160px; text-align: left; }
                .page-break { page-break-before: always; }
                .muted { color: #555; }
            </style>
        </head>
        <body>
        <?php if (empty($grouped)): ?>
            <h1><?= esc((string) ($layout['title'] ?? 'Laporan')) ?></h1>
            <p class="muted">Belum ada hasil pengisian.</p>
        <?php else: ?>
            <?php $reportIndex = 0; foreach ($grouped as $entry): ?>
                <?php
                $reportIndex++;
                $meta = $entry['meta'];
                ?>
                <?php if ($reportIndex > 1): ?><div class="page-break"></div><?php endif; ?>
                <h1><?= esc((string) ($layout['title'] ?? 'Laporan Instrumen')) ?></h1>

                <h2>A. Identitas</h2>
                <table class="meta">
                    <tbody>
                        <tr><th>Responden</th><td><?= esc((string) ($meta['nama'] ?? '-')) ?></td></tr>
                        <tr><th>Email</th><td><?= esc((string) ($meta['email'] ?? '-')) ?></td></tr>
                        <tr><th>NIM/Identitas</th><td><?= esc((string) ($meta['nim'] ?? '-')) ?></td></tr>
                        <tr><th>Program Studi</th><td><?= esc((string) ($meta['program_studi'] ?? '-')) ?></td></tr>
                        <tr><th>Instrumen</th><td><?= esc((string) ($meta['instrument_kode'] ?? '-')) ?> - <?= esc((string) ($meta['instrument_judul'] ?? '-')) ?></td></tr>
                        <tr><th>Waktu Submit</th><td><?= esc((string) ($meta['submitted_at'] ?? '-')) ?></td></tr>
                    </tbody>
                </table>

                <h2>B. Hasil Pengisian</h2>
                <table>
                    <thead>
                        <?php if ($layoutType === 'document_review'): ?>
                            <tr>
                                <th rowspan="2" style="width: 34px;">No</th>
                                <th rowspan="2"><?= esc((string) $layout['aspect']) ?></th>
                                <th rowspan="2"><?= esc((string) $layout['item']) ?></th>
                                <th rowspan="2">Sumber Dokumen</th>
                                <th colspan="4">Skor</th>
                                <th rowspan="2">Komentar</th>
                            </tr>
                            <tr>
                                <?php for ($score = 1; $score <= 4; $score++): ?>
                                    <th style="width: 24px;"><?= $score ?></th>
                                <?php endfor; ?>
                            </tr>
                        <?php elseif ($layoutType === 'interview_guide'): ?>
                            <tr>
                                <th style="width: 34px;">No</th>
                                <th><?= esc((string) $layout['aspect']) ?></th>
                                <th><?= esc((string) $layout['item']) ?></th>
                                <th><?= esc((string) $layout['answer']) ?></th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th style="width: 34px;">No</th>
                                <th><?= esc((string) $layout['aspect']) ?></th>
                                <th><?= esc((string) $layout['item']) ?></th>
                                <th><?= esc((string) $layout['result']) ?></th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php foreach ($entry['answers'] as $answer): ?>
                            <tr>
                                <td><?= esc((string) ($answer['nomor'] ?? '-')) ?></td>
                                <td><?= esc((string) ($answer['nama_aspek'] ?? '-')) ?></td>
                                <td><?= nl2br(esc((string) ($answer['pernyataan'] ?? '-'))) ?></td>
                                <?php if ($layoutType === 'document_review'): ?>
                                    <td><?= esc(document_review_source_label($answer['sumber_dokumen'] ?? '')) ?></td>
                                    <?php for ($score = 1; $score <= 4; $score++): ?>
                                        <td style="text-align:center;"><?= (string) ($answer['skor'] ?? '') === (string) $score ? 'X' : '' ?></td>
                                    <?php endfor; ?>
                                    <td><?= nl2br(esc((string) ($answer['komentar'] ?? ''))) ?></td>
                                <?php else: ?>
                                    <td><?= nl2br(esc((string) (($answer['jawaban_teks'] ?? '') ?: ($answer['komentar'] ?? '') ?: ($answer['skor'] ?? '')))) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2>C. Catatan/Kesimpulan</h2>
                <table class="meta">
                    <tbody>
                        <tr><th>Komentar/Saran</th><td><?= nl2br(esc((string) ($meta['komentar_umum'] ?? '-'))) ?></td></tr>
                        <tr><th>Kesimpulan</th><td><?= esc((string) ($meta['kesimpulan'] ?? '-')) ?></td></tr>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
        </body>
        </html>
        <?php

        return (string) ob_get_clean();
    }

    private function exportTypedExcelReport(array $filters, string $jenis)
    {
        $layout = instrument_preview_layout($jenis);
        $rows = $this->buildTypedExcelRows($this->getExportRows($filters), $jenis);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr((string) ($layout['title'] ?? 'Laporan'), 0, 31));

        $sheet->fromArray($rows['headers'], null, 'A1');
        $rowNumber = 2;

        foreach ($rows['rows'] as $row) {
            $sheet->fromArray($row, null, 'A' . $rowNumber);
            $rowNumber++;
        }

        $highestColumn = $sheet->getHighestColumn();
        $highestRow = max(1, $sheet->getHighestRow());
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EAF2F8'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E2EC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($columnIndex = 1; $columnIndex <= $highestColumnIndex; $columnIndex++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $highestColumn . '1');
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        $filename = $this->safeExportFileName('laporan-' . ($layout['title'] ?? 'instrumen')) . '-' . date('Ymd-His') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($this->spreadsheetToString($spreadsheet));
    }

    private function buildTypedExcelRows(array $sourceRows, string $jenis): array
    {
        $layout = instrument_preview_layout($jenis);
        $layoutType = (string) ($layout['type'] ?? 'standard');
        $justification = instrument_public_justification_config($jenis);
        $headers = [
            'Response ID',
            'Waktu Submit',
            'Nama',
            'Email',
            'NIM/No. Identitas',
            'Program Studi',
            'Kelas',
            'Semester',
            'Instansi',
            'Bidang/Jabatan',
            'Judul Link',
            'Kode Instrumen',
            'Instrumen',
            'Produk',
            'No Butir',
            (string) ($layout['aspect'] ?? 'Aspek'),
            (string) ($layout['item'] ?? 'Butir'),
        ];

        if ($layoutType === 'rubric_assessment') {
            foreach (range(1, 5) as $score) {
                $headers[] = 'Deskripsi Skor ' . $score;
            }
            $headers[] = 'Skor yang Diperoleh';
        } elseif (in_array($layoutType, ['questionnaire', 'product_validation_questionnaire', 'user_response_questionnaire', 'performance_test'], true)) {
            $headers[] = 'Skor';
        } else {
            $headers[] = 'Jawaban';
        }

        $headers[] = 'Komentar Butir';

        if (!empty($justification['show_comment'])) {
            $headers[] = (string) ($justification['comment_label'] ?? 'Komentar/Saran');
        }

        if (!empty($justification['show_conclusion'])) {
            $headers[] = (string) ($justification['conclusion_label'] ?? 'Kesimpulan');
        }

        $rows = [];

        foreach ($sourceRows as $sourceRow) {
            $row = [
                $sourceRow['response_id'] ?? '',
                $sourceRow['submitted_at'] ?? '',
                $sourceRow['nama'] ?? '',
                $sourceRow['email'] ?? '',
                $sourceRow['nim'] ?? '',
                $sourceRow['program_studi'] ?? '',
                $sourceRow['kelas'] ?? '',
                $sourceRow['semester'] ?? '',
                $sourceRow['instansi'] ?? '',
                $sourceRow['bidang_keahlian'] ?? '',
                $sourceRow['judul_link'] ?? '',
                $sourceRow['instrument_kode'] ?? '',
                $sourceRow['instrument_judul'] ?? '',
                $sourceRow['nama_produk'] ?? '',
                $sourceRow['nomor'] ?? '',
                $sourceRow['nama_aspek'] ?? '',
                $sourceRow['pernyataan'] ?? '',
            ];

            if ($layoutType === 'rubric_assessment') {
                foreach (range(1, 5) as $score) {
                    $row[] = $sourceRow['skor_' . $score . '_deskripsi'] ?? '';
                }
                $row[] = $sourceRow['skor'] ?? '';
            } elseif (in_array($layoutType, ['questionnaire', 'product_validation_questionnaire', 'user_response_questionnaire', 'performance_test'], true)) {
                $row[] = $sourceRow['skor'] ?? '';
            } else {
                $row[] = trim((string) (($sourceRow['jawaban_teks'] ?? '') ?: ($sourceRow['skor'] ?? '')));
            }

            $row[] = $sourceRow['komentar'] ?? '';

            if (!empty($justification['show_comment'])) {
                $row[] = $sourceRow['komentar_umum'] ?? '';
            }

            if (!empty($justification['show_conclusion'])) {
                $row[] = $sourceRow['kesimpulan'] ?? '';
            }

            $rows[] = $row;
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    private function safeExportFileName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9]+/', '-', $name) ?? 'laporan';
        $name = trim($name, '-');

        return $name !== '' ? $name : 'laporan';
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
            'identity_data',
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
            'justification_data',
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
                $row['identity_data'],
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
                $row['justification_data'],
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return "\xEF\xBB\xBF" . $csv;
    }

    private function findOwnedResponse(int $responseId): ?array
    {
        if ($responseId <= 0) {
            return null;
        }

        return $this->responseModel
            ->scopeOwned('responses.user_id')
            ->where('responses.id', $responseId)
            ->first();
    }
}
