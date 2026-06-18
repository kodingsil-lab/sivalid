<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use App\Libraries\WorkflowStatusService;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentItemModel;
use App\Models\ValidationBundleAnswerModel;
use App\Models\ValidationBundleInstrumentModel;
use App\Models\ValidationBundleInstrumentProgressModel;
use App\Models\ValidationBundleModel;
use App\Models\ValidationBundleSessionModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InstrumentValidationResults extends BaseController
{
    protected WorkflowStatusService $workflowStatusService;
    protected AuditLogService $auditLog;

    public function __construct()
    {
        $this->workflowStatusService = new WorkflowStatusService();
        $this->auditLog = new AuditLogService();
    }

    public function index()
    {
        $perPage = config('Pager')->perPage;
        $sessionModel = new ValidationBundleSessionModel();

        $builder = $sessionModel
            ->select(
                "validation_bundle_sessions.*,
                 validation_bundles.judul AS bundle_judul,
                 validation_bundles.token AS bundle_token,
                 COUNT(DISTINCT validation_bundle_instruments.instrument_id) AS total_instrumen,
                 COUNT(DISTINCT CASE WHEN validation_bundle_instrument_progress.status = 'selesai' THEN validation_bundle_instrument_progress.instrument_id END) AS selesai_count,
                 COUNT(DISTINCT validation_bundle_instrument_progress.instrument_id) AS touched_count,
                 MAX(validation_bundle_instrument_progress.saved_at) AS last_saved_at"
            )
            ->join('validation_bundles', 'validation_bundles.id = validation_bundle_sessions.bundle_id')
            ->join('validation_bundle_instruments', 'validation_bundle_instruments.bundle_id = validation_bundle_sessions.bundle_id', 'left')
            ->join('validation_bundle_instrument_progress', 'validation_bundle_instrument_progress.session_id = validation_bundle_sessions.id', 'left')
            ->groupBy('validation_bundle_sessions.id')
            ->having('touched_count >', 0)
            ->orderBy('last_saved_at', 'DESC')
            ->orderBy('validation_bundle_sessions.id', 'DESC');

        $sessions = $builder->paginate($perPage, 'validation_results');

        return view('admin/validation_results/index', [
            'title'      => 'Hasil Validasi Instrumen',
            'sessions'   => $sessions,
            'pager'      => $sessionModel->pager,
            'pagerGroup' => 'validation_results',
        ]);
    }

    public function show($sessionId = null)
    {
        $data = $this->buildSessionReportData((int) $sessionId);

        if ($data === null) {
            return redirect()
                ->to(base_url('admin/hasil-validasi-instrumen'))
                ->with('error', 'Hasil validasi tidak ditemukan.');
        }

        return view('admin/validation_results/show', array_merge($data, [
            'title' => 'Detail Hasil Validasi Instrumen',
        ]));
    }

    public function export($sessionId = null)
    {
        $data = $this->buildSessionReportData((int) $sessionId);

        if ($data === null) {
            return redirect()
                ->to(base_url('admin/hasil-validasi-instrumen'))
                ->with('error', 'Hasil validasi tidak ditemukan.');
        }

        $spreadsheet = new Spreadsheet();
        $summary = $spreadsheet->getActiveSheet();
        $summary->setTitle('Ringkasan');
        $this->fillSummarySheet($summary, $data);

        foreach ($data['instrumentDetails'] as $index => $detail) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($this->safeSheetTitle('Instrumen ' . ($index + 1)));
            $this->fillInstrumentSheet($sheet, $detail);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = $this->downloadFileName($data['bundle']['judul'] ?? 'hasil-validasi', $data['validatorSession']['validator_nama'] ?? 'validator');
        $tempPath = WRITEPATH . 'cache/' . $fileName;

        (new Xlsx($spreadsheet))->save($tempPath);
        $spreadsheet->disconnectWorksheets();

        return $this->response
            ->download($tempPath, null)
            ->setFileName($fileName)
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function setInstrumentValid($sessionId = null, $instrumentId = null)
    {
        $sessionId = (int) $sessionId;
        $instrumentId = (int) $instrumentId;
        $data = $this->buildSessionReportData($sessionId);

        if ($data === null || $instrumentId <= 0) {
            return redirect()
                ->to(base_url('admin/hasil-validasi-instrumen'))
                ->with('error', 'Hasil validasi tidak ditemukan.');
        }

        $selectedDetail = null;
        foreach ($data['instrumentDetails'] as $detail) {
            if ((int) ($detail['instrument']['instrument_id'] ?? 0) === $instrumentId) {
                $selectedDetail = $detail;
                break;
            }
        }

        if ($selectedDetail === null) {
            return redirect()
                ->to(base_url('admin/hasil-validasi-instrumen/' . $sessionId))
                ->with('error', 'Instrumen tidak ditemukan pada hasil validasi ini.');
        }

        if (($selectedDetail['status'] ?? 'belum') !== 'selesai') {
            return redirect()
                ->to(base_url('admin/hasil-validasi-instrumen/' . $sessionId))
                ->with('error', 'Instrumen belum selesai divalidasi, sehingga belum dapat ditetapkan valid.');
        }

        $this->workflowStatusService->markInstrumentValid($instrumentId);

        $validatorName = (string) ($data['validatorSession']['validator_nama'] ?? '-');
        $bundleTitle = (string) ($data['bundle']['judul'] ?? '-');

        $this->auditLog->log(
            AuditLogService::ACTION_MARK_INSTRUMENT_VALID,
            AuditLogService::ENTITY_INSTRUMENT,
            $instrumentId,
            'Instrumen ditetapkan Valid dari hasil paket validasi. Sesi ID=' . $sessionId . ', Validator=' . $validatorName . ', Paket=' . $bundleTitle
        );

        return redirect()
            ->to(base_url('admin/hasil-validasi-instrumen/' . $sessionId))
            ->with('success', 'Instrumen berhasil ditetapkan sebagai Valid.');
    }

    private function buildSessionReportData(int $sessionId): ?array
    {
        if ($sessionId <= 0) {
            return null;
        }

        $sessionModel = new ValidationBundleSessionModel();
        $bundleModel = new ValidationBundleModel();
        $bundleInstrumentModel = new ValidationBundleInstrumentModel();
        $answerModel = new ValidationBundleAnswerModel();
        $progressModel = new ValidationBundleInstrumentProgressModel();
        $itemModel = new InstrumentItemModel();
        $aspectModel = new InstrumentAspectModel();

        $validatorSession = $sessionModel->find($sessionId);

        if (!$validatorSession) {
            return null;
        }

        $bundle = $bundleModel->find((int) $validatorSession['bundle_id']);

        if (!$bundle) {
            return null;
        }

        $instruments = $bundleInstrumentModel->getByBundle((int) $bundle['id']);
        $answersByInstrument = $answerModel->getGroupedByInstrument($sessionId);
        $progressMap = $progressModel->getBySession($sessionId);

        if (empty($progressMap)) {
            return null;
        }

        $instrumentDetails = [];
        $selesaiCount = 0;

        foreach ($instruments as $index => $instrument) {
            $instrumentId = (int) $instrument['instrument_id'];
            $progress = $progressMap[$instrumentId] ?? null;

            if (($progress['status'] ?? 'belum') === 'selesai') {
                $selesaiCount++;
            }

            $aspects = $aspectModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();

            $aspectNames = [];
            foreach ($aspects as $aspect) {
                $aspectNames[(int) $aspect['id']] = $aspect['nama_aspek'];
            }

            $items = $itemModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->orderBy('nomor', 'ASC')
                ->findAll();

            $answersMap = $answersByInstrument[$instrumentId] ?? [];
            $rows = [];

            foreach ($items as $item) {
                $itemId = (int) $item['id'];
                $answer = $answersMap[$itemId] ?? null;
                $tipeButir = $item['tipe_butir'] ?? 'skala';

                $rows[] = [
                    'nomor'        => $item['nomor'] ?? '-',
                    'aspek'        => $aspectNames[(int) ($item['aspect_id'] ?? 0)] ?? '-',
                    'pernyataan'   => $item['pernyataan'] ?? '-',
                    'tipe_butir'   => $tipeButir,
                    'skor'         => $answer['skor'] ?? null,
                    'jawaban_teks' => trim((string) ($answer['jawaban_teks'] ?? '')),
                    'komentar'     => trim((string) ($answer['komentar'] ?? '')),
                ];
            }

            $instrumentDetails[] = [
                'position'      => $index + 1,
                'instrument'    => $instrument,
                'status'        => $progress['status'] ?? 'belum',
                'kesimpulan'    => $progress['kesimpulan'] ?? null,
                'komentar_umum' => $progress['komentar_umum'] ?? null,
                'saved_at'      => $progress['saved_at'] ?? null,
                'items'         => $rows,
            ];
        }

        return [
            'bundle'            => $bundle,
            'validatorSession'  => $validatorSession,
            'instrumentDetails' => $instrumentDetails,
            'summary'           => [
                'total_instrumen' => count($instruments),
                'selesai_count'   => $selesaiCount,
                'last_saved_at'   => $this->latestSavedAt($instrumentDetails),
            ],
        ];
    }

    private function fillSummarySheet(Worksheet $sheet, array $data): void
    {
        $bundle = $data['bundle'];
        $session = $data['validatorSession'];
        $summary = $data['summary'];

        $sheet->setCellValue('A1', 'Ringkasan Hasil Validasi Instrumen');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $infoRows = [
            ['Paket', $bundle['judul'] ?? '-'],
            ['Validator', $session['validator_nama'] ?? '-'],
            ['Email', $session['validator_email'] ?: '-'],
            ['Instansi', $session['validator_instansi'] ?: '-'],
            ['Bidang Keahlian', $session['validator_bidang_keahlian'] ?: '-'],
            ['Progress', ((int) $summary['selesai_count']) . '/' . ((int) $summary['total_instrumen']) . ' selesai'],
            ['Mulai', $session['started_at'] ?? '-'],
            ['Submit', $session['submitted_at'] ?? '-'],
            ['Terakhir Diisi', $summary['last_saved_at'] ?? '-'],
        ];

        $row = 3;
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $row++;
        }

        $row += 1;
        $sheet->fromArray(['No', 'Kode', 'Instrumen', 'Status', 'Kesimpulan', 'Terakhir Diisi'], null, 'A' . $row);
        $this->styleHeader($sheet, 'A' . $row . ':F' . $row);
        $row++;

        foreach ($data['instrumentDetails'] as $detail) {
            $sheet->fromArray([
                $detail['position'],
                $detail['instrument']['kode'] ?? '-',
                $detail['instrument']['judul'] ?? '-',
                $detail['status'] ?? '-',
                $detail['kesimpulan'] ?: '-',
                $detail['saved_at'] ?: '-',
            ], null, 'A' . $row);
            $row++;
        }

        $this->autosize($sheet, 'A', 'F');
        $sheet->getStyle('A:F')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    }

    private function fillInstrumentSheet(Worksheet $sheet, array $detail): void
    {
        $instrument = $detail['instrument'];

        $sheet->setCellValue('A1', ($instrument['kode'] ?? '-') . ' - ' . ($instrument['judul'] ?? '-'));
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $sheet->setCellValue('A3', 'Status');
        $sheet->setCellValue('B3', $detail['status'] ?? '-');
        $sheet->setCellValue('A4', 'Kesimpulan');
        $sheet->setCellValue('B4', $detail['kesimpulan'] ?: '-');
        $sheet->setCellValue('A5', 'Komentar Umum');
        $sheet->setCellValue('B5', $detail['komentar_umum'] ?: '-');

        $sheet->fromArray(['No', 'Aspek', 'Pernyataan', 'Tipe Butir', 'Skor', 'Jawaban Teks', 'Komentar'], null, 'A7');
        $this->styleHeader($sheet, 'A7:G7');

        $row = 8;
        foreach ($detail['items'] as $item) {
            $sheet->fromArray([
                $item['nomor'] ?? '-',
                $item['aspek'] ?? '-',
                $item['pernyataan'] ?? '-',
                $item['tipe_butir'] ?? '-',
                $item['skor'] ?? '-',
                $item['jawaban_teks'] !== '' ? $item['jawaban_teks'] : '-',
                $item['komentar'] !== '' ? $item['komentar'] : '-',
            ], null, 'A' . $row);
            $row++;
        }

        $this->autosize($sheet, 'A', 'G');
        $sheet->getColumnDimension('C')->setWidth(55);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getStyle('A:G')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('C:G')->getAlignment()->setWrapText(true);

        if ($row > 8) {
            $sheet->getStyle('A7:G' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    }

    private function styleHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9EEF5');
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function autosize(Worksheet $sheet, string $start, string $end): void
    {
        foreach (range($start, $end) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function latestSavedAt(array $instrumentDetails): ?string
    {
        $latest = null;

        foreach ($instrumentDetails as $detail) {
            if (empty($detail['saved_at'])) {
                continue;
            }

            if ($latest === null || strtotime($detail['saved_at']) > strtotime($latest)) {
                $latest = $detail['saved_at'];
            }
        }

        return $latest;
    }

    private function safeSheetTitle(string $title): string
    {
        $title = preg_replace('/[\\\\\\/\\?\\*\\[\\]\\:]/', ' ', $title) ?: 'Sheet';

        return mb_substr(trim($title), 0, 31) ?: 'Sheet';
    }

    private function downloadFileName(string $bundleTitle, string $validatorName): string
    {
        $name = strtolower($bundleTitle . '-' . $validatorName);
        $name = preg_replace('/[^a-z0-9]+/i', '-', $name) ?: 'hasil-validasi';
        $name = trim($name, '-');

        return $name . '-' . date('Ymd-His') . '.xlsx';
    }
}
