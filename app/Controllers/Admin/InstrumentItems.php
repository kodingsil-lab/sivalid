<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;
use App\Models\InstrumentItemModel;
use ZipArchive;

class InstrumentItems extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentAspectModel $aspectModel;
    protected InstrumentIndicatorModel $indicatorModel;
    protected InstrumentItemModel $itemModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->aspectModel     = new InstrumentAspectModel();
        $this->indicatorModel  = new InstrumentIndicatorModel();
        $this->itemModel       = new InstrumentItemModel();
    }

    public function index()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;
        $perPage = config('Pager')->perPage;

        $selectedInstrument = $instrumentId !== null ? $this->findOwnedInstrument($instrumentId) : null;

        if ($instrumentId !== null && ! $selectedInstrument) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Instrumen tidak ditemukan atau bukan milik akun Anda.');
        }

        $itemLayout = instrument_item_entry_layout($selectedInstrument['jenis'] ?? '');

        $data = [
            'title'        => ($itemLayout['item_label'] ?? 'Butir') . ' Instrumen',
            'instrumentId' => $instrumentId,
            'selectedInstrument' => $selectedInstrument,
            'itemLayout'   => $itemLayout,
            'instruments'  => $this->instrumentModel->getOrderedByCodeSequence(),
            'items'        => $this->itemModel->paginateWithRelations($instrumentId, $perPage, 'instrument_items'),
            'pager'        => $this->itemModel->pager,
            'pagerGroup'   => 'instrument_items',
        ];

        return view('admin/items/index', $data);
    }

    public function new()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;

        $selectedInstrument = null;
        $aspects = [];
        $indicators = [];

        if ($instrumentId !== null) {
            $selectedInstrument = $this->findOwnedInstrument($instrumentId);

            if (! $selectedInstrument) {
                return redirect()
                    ->to(base_url('admin/instrument-items'))
                    ->with('error', 'Instrumen tidak ditemukan atau bukan milik akun Anda.');
            }

            $aspects = $this->aspectModel
                ->scopeOwned('instrument_aspects.user_id')
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();

            $indicators = $this->indicatorModel
                ->scopeOwned('instrument_indicators.user_id')
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();
        }

        $nextNumber = 1;

        if ($instrumentId !== null) {
            $lastItem = $this->itemModel
                ->scopeOwned('instrument_items.user_id')
                ->where('instrument_id', $instrumentId)
                ->orderBy('nomor', 'DESC')
                ->first();

            if ($lastItem) {
                $nextNumber = ((int) $lastItem['nomor']) + 1;
            }
        }

        $itemLayout = instrument_item_entry_layout($selectedInstrument['jenis'] ?? '');

        $data = [
            'title'        => 'Tambah ' . ($itemLayout['item_label'] ?? 'Butir'),
            'item'         => null,
            'instrumentId' => $instrumentId,
            'selectedInstrument' => $selectedInstrument,
            'itemLayout'   => $itemLayout,
            'instruments'  => $this->instrumentModel->getOrderedByCodeSequence(),
            'aspects'      => $aspects,
            'indicators'   => $indicators,
            'nextNumber'   => $nextNumber,
            'action'       => base_url('admin/instrument-items'),
            'method'       => 'post',
        ];

        return view('admin/items/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'nomor'         => 'required|integer',
            'pernyataan'    => 'required|min_length[5]',
            'sumber_dokumen'=> 'permit_empty|max_length[150]',
            'skor_1_deskripsi' => 'permit_empty',
            'skor_2_deskripsi' => 'permit_empty',
            'skor_3_deskripsi' => 'permit_empty',
            'skor_4_deskripsi' => 'permit_empty',
            'skor_5_deskripsi' => 'permit_empty',
            'tipe_butir'    => 'required',
            'urutan'        => 'required|integer',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');
        $indicatorId  = $this->request->getPost('indicator_id');

        $instrument = $this->findOwnedInstrument($instrumentId);

        if (! $instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan atau bukan milik akun Anda.');
        }

        $aspect = $this->aspectModel->scopeOwned('instrument_aspects.user_id')->where([
            'id'            => $aspectId,
            'instrument_id' => $instrumentId,
        ])->first();

        if (!$aspect) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Aspek tidak sesuai dengan instrumen yang dipilih.');
        }

        if (!empty($indicatorId)) {
            $indicator = $this->indicatorModel->scopeOwned('instrument_indicators.user_id')->where([
                'id'            => (int) $indicatorId,
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
            ])->first();

            if (!$indicator) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Indikator tidak sesuai dengan instrumen dan aspek yang dipilih.');
            }
        }

        $this->itemModel->insert([
            'user_id'       => $this->ownerIdFromInstrument($instrument),
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indicator_id'  => !empty($indicatorId) ? (int) $indicatorId : null,
            'nomor'         => (int) $this->request->getPost('nomor'),
            'pernyataan'    => trim((string) $this->request->getPost('pernyataan')),
            'sumber_dokumen'=> trim((string) $this->request->getPost('sumber_dokumen')),
            'skor_1_deskripsi' => trim((string) $this->request->getPost('skor_1_deskripsi')),
            'skor_2_deskripsi' => trim((string) $this->request->getPost('skor_2_deskripsi')),
            'skor_3_deskripsi' => trim((string) $this->request->getPost('skor_3_deskripsi')),
            'skor_4_deskripsi' => trim((string) $this->request->getPost('skor_4_deskripsi')),
            'skor_5_deskripsi' => trim((string) $this->request->getPost('skor_5_deskripsi')),
            'tipe_butir'    => trim((string) $this->request->getPost('tipe_butir')),
            'wajib'         => (int) $this->request->getPost('wajib'),
            'urutan'        => (int) $this->request->getPost('urutan'),
            'status'        => trim((string) $this->request->getPost('status')),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', 'Butir pernyataan berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $item = $this->findOwnedItem($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $aspects = $this->aspectModel
            ->scopeOwned('instrument_aspects.user_id')
            ->where('instrument_id', $item['instrument_id'])
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $indicators = $this->indicatorModel
            ->scopeOwned('instrument_indicators.user_id')
            ->where('instrument_id', $item['instrument_id'])
            ->orderBy('urutan', 'ASC')
            ->findAll();
        $selectedInstrument = $this->findOwnedInstrument((int) $item['instrument_id']);

        $data = [
            'title'        => 'Edit ' . (($selectedInstrument ? instrument_item_entry_layout($selectedInstrument['jenis'] ?? '') : [])['item_label'] ?? 'Butir'),
            'item'         => $item,
            'instrumentId' => $item['instrument_id'],
            'selectedInstrument' => $selectedInstrument,
            'itemLayout'   => instrument_item_entry_layout($selectedInstrument['jenis'] ?? ''),
            'instruments'  => $this->instrumentModel->getOrderedByCodeSequence(),
            'aspects'      => $aspects,
            'indicators'   => $indicators,
            'nextNumber'   => $item['nomor'],
            'action'       => base_url('admin/instrument-items/' . $id),
            'method'       => 'put',
        ];

        return view('admin/items/form', $data);
    }

    public function update($id = null)
    {
        $item = $this->findOwnedItem($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'nomor'         => 'required|integer',
            'pernyataan'    => 'required|min_length[5]',
            'sumber_dokumen'=> 'permit_empty|max_length[150]',
            'skor_1_deskripsi' => 'permit_empty',
            'skor_2_deskripsi' => 'permit_empty',
            'skor_3_deskripsi' => 'permit_empty',
            'skor_4_deskripsi' => 'permit_empty',
            'skor_5_deskripsi' => 'permit_empty',
            'tipe_butir'    => 'required',
            'urutan'        => 'required|integer',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');
        $indicatorId  = $this->request->getPost('indicator_id');

        $instrument = $this->findOwnedInstrument($instrumentId);

        if (! $instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan atau bukan milik akun Anda.');
        }

        $aspect = $this->aspectModel->scopeOwned('instrument_aspects.user_id')->where([
            'id'            => $aspectId,
            'instrument_id' => $instrumentId,
        ])->first();

        if (!$aspect) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Aspek tidak sesuai dengan instrumen yang dipilih.');
        }

        if (!empty($indicatorId)) {
            $indicator = $this->indicatorModel->scopeOwned('instrument_indicators.user_id')->where([
                'id'            => (int) $indicatorId,
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
            ])->first();

            if (!$indicator) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Indikator tidak sesuai dengan instrumen dan aspek yang dipilih.');
            }
        }

        $this->itemModel->update($id, [
            'user_id'       => $this->ownerIdFromInstrument($instrument),
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indicator_id'  => !empty($indicatorId) ? (int) $indicatorId : null,
            'nomor'         => (int) $this->request->getPost('nomor'),
            'pernyataan'    => trim((string) $this->request->getPost('pernyataan')),
            'sumber_dokumen'=> trim((string) $this->request->getPost('sumber_dokumen')),
            'skor_1_deskripsi' => trim((string) $this->request->getPost('skor_1_deskripsi')),
            'skor_2_deskripsi' => trim((string) $this->request->getPost('skor_2_deskripsi')),
            'skor_3_deskripsi' => trim((string) $this->request->getPost('skor_3_deskripsi')),
            'skor_4_deskripsi' => trim((string) $this->request->getPost('skor_4_deskripsi')),
            'skor_5_deskripsi' => trim((string) $this->request->getPost('skor_5_deskripsi')),
            'tipe_butir'    => trim((string) $this->request->getPost('tipe_butir')),
            'wajib'         => (int) $this->request->getPost('wajib'),
            'urutan'        => (int) $this->request->getPost('urutan'),
            'status'        => trim((string) $this->request->getPost('status')),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', 'Butir pernyataan berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $item = $this->findOwnedItem($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $instrumentId = $item['instrument_id'];

        $this->itemModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', 'Butir pernyataan berhasil dihapus.');
    }

    public function import()
    {
        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument   = $this->findOwnedInstrument($instrumentId);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        $rules = [
            'file_excel' => 'uploaded[file_excel]|max_size[file_excel,4096]|ext_in[file_excel,xlsx]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
                ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()
                ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
                ->with('error', 'File Excel tidak valid.');
        }

        try {
            $rows = $this->readXlsxRows($file->getTempName());
            $result = $this->importRows($instrumentId, $this->ownerIdFromInstrument($instrument), $rows, instrument_item_entry_layout($instrument['jenis'] ?? ''));
        } catch (\Throwable $exception) {
            return redirect()
                ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
                ->with('error', 'Import gagal: ' . $exception->getMessage());
        }

        return redirect()
            ->to(base_url('admin/instrument-items?instrument_id=' . $instrumentId))
            ->with('success', sprintf(
                'Import selesai. Butir baru: %d, butir duplikat dilewati: %d.',
                $result['items_created'],
                $result['items_skipped']
            ));
    }

    public function importTemplate()
    {
        $instrumentId = (int) $this->request->getGet('instrument_id');
        $instrument = $this->findOwnedInstrument($instrumentId);
        $itemLayout = instrument_item_entry_layout($instrument['jenis'] ?? '');
        $safeTitle = preg_replace('/[^a-z0-9]+/i', '-', strtolower($itemLayout['title'] ?? 'instrumen')) ?? 'instrumen';
        $safeTitle = trim($safeTitle, '-') ?: 'instrumen';
        $fileName = 'template-import-butir-' . $safeTitle . '.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'butir_template_');

        if ($tempPath === false) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Template gagal dibuat.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Template gagal dibuat.');
        }

        $templateRows = $this->importTemplateRows($itemLayout);
        $sharedStrings = $this->xlsxSharedStringsFromRows($templateRows);

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml('Template Butir'));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->xlsxStylesXml());
        $zip->addFromString('xl/sharedStrings.xml', $this->xlsxSharedStringsXml($sharedStrings));
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxTemplateSheetXml($templateRows, $sharedStrings));
        $zip->close();

        return $this->response
            ->download($tempPath, null)
            ->setFileName($fileName);
    }

    private function importRows(int $instrumentId, int $ownerId, array $rows, array $itemLayout): array
    {
        if (count($rows) < 2) {
            throw new \RuntimeException('File tidak berisi data.');
        }

        $headers = $this->mapImportHeaders($rows[0], (string) ($itemLayout['type'] ?? 'standard'));
        foreach (['aspect_name' => $itemLayout['aspect_label'] ?? 'Aspek', 'statement' => $itemLayout['item_label'] ?? 'Butir'] as $key => $label) {
            if (!isset($headers[$key])) {
                throw new \RuntimeException('Kolom "' . $label . '" wajib ada.');
            }
        }

        $aspects = $this->aspectModel
            ->scopeOwned('instrument_aspects.user_id')
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();
        if (empty($aspects)) {
            throw new \RuntimeException('Instrumen ini belum memiliki aspek. Buat/import kisi-kisi terlebih dahulu.');
        }

        $aspectsByName = [];
        foreach ($aspects as $aspect) {
            $aspectsByName[$this->normalizeImportText((string) $aspect['nama_aspek'])] = $aspect;
        }

        $indicators = $this->indicatorModel
            ->scopeOwned('instrument_indicators.user_id')
            ->where('instrument_id', $instrumentId)
            ->findAll();
        $indicatorsByAspectAndText = [];
        foreach ($indicators as $indicator) {
            $aspectId = (int) $indicator['aspect_id'];
            $indicatorsByAspectAndText[$aspectId][$this->normalizeImportText((string) $indicator['indikator'])] = $indicator;
        }

        $existingItems = $this->itemModel
            ->scopeOwned('instrument_items.user_id')
            ->where('instrument_id', $instrumentId)
            ->findAll();
        $itemsByText = [];
        $nextNumber = 1;
        $nextOrder = 1;
        foreach ($existingItems as $item) {
            $itemsByText[$this->normalizeImportText((string) $item['pernyataan'])] = true;
            $nextNumber = max($nextNumber, (int) ($item['nomor'] ?? 0) + 1);
            $nextOrder = max($nextOrder, (int) ($item['urutan'] ?? 0) + 1);
        }

        $createdItems = 0;
        $skippedItems = 0;
        $db = db_connect();

        $db->transStart();

        foreach (array_slice($rows, 1) as $offset => $row) {
            $excelRowNumber = $offset + 2;
            if ($this->isImportRowEmpty($row)) {
                continue;
            }

            $aspectName = trim((string) ($row[$headers['aspect_name']] ?? ''));
            $statement = trim((string) ($row[$headers['statement']] ?? ''));
            if ($aspectName === '' || $statement === '') {
                continue;
            }

            $aspect = $aspectsByName[$this->normalizeImportText($aspectName)] ?? null;
            if (!$aspect) {
                throw new \RuntimeException('Aspek pada baris ' . $excelRowNumber . ' tidak ditemukan: ' . $aspectName);
            }

            $statementKey = $this->normalizeImportText($statement);
            if (isset($itemsByText[$statementKey])) {
                $skippedItems++;
                continue;
            }

            $aspectId = (int) $aspect['id'];
            $indicatorId = null;
            $indicatorText = trim((string) ($row[$headers['indicator_text'] ?? -1] ?? ''));
            if ($indicatorText !== '') {
                $indicator = $indicatorsByAspectAndText[$aspectId][$this->normalizeImportText($indicatorText)] ?? null;
                if (!$indicator) {
                    throw new \RuntimeException('Indikator pada baris ' . $excelRowNumber . ' tidak ditemukan pada aspek "' . $aspectName . '".');
                }
                $indicatorId = (int) $indicator['id'];
            }

            $number = $this->readImportNumber($row[$headers['number'] ?? -1] ?? null) ?? $nextNumber++;
            $order = $this->readImportNumber($row[$headers['order'] ?? -1] ?? null) ?? $nextOrder++;
            $sourceDocument = trim((string) ($row[$headers['source_document'] ?? -1] ?? ''));
            $rubricScores = [];
            for ($score = 1; $score <= 5; $score++) {
                $rubricScores['skor_' . $score . '_deskripsi'] = trim((string) ($row[$headers['score_' . $score] ?? -1] ?? ''));
            }
            $type = $this->normalizeItemType((string) ($row[$headers['item_type'] ?? -1] ?? 'skala'));
            $required = $this->normalizeRequired((string) ($row[$headers['required'] ?? -1] ?? 'Ya'));
            $status = $this->normalizeItemStatus((string) ($row[$headers['status'] ?? -1] ?? 'Aktif'));

            $this->itemModel->insert([
                'user_id'       => $ownerId,
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
                'indicator_id'  => $indicatorId,
                'nomor'         => $number,
                'pernyataan'    => $statement,
                'sumber_dokumen'=> $sourceDocument,
                'skor_1_deskripsi' => $rubricScores['skor_1_deskripsi'],
                'skor_2_deskripsi' => $rubricScores['skor_2_deskripsi'],
                'skor_3_deskripsi' => $rubricScores['skor_3_deskripsi'],
                'skor_4_deskripsi' => $rubricScores['skor_4_deskripsi'],
                'skor_5_deskripsi' => $rubricScores['skor_5_deskripsi'],
                'tipe_butir'    => $type,
                'wajib'         => $required,
                'urutan'        => $order,
                'status'        => $status,
            ]);

            $itemsByText[$statementKey] = true;
            $nextNumber = max($nextNumber, $number + 1);
            $nextOrder = max($nextOrder, $order + 1);
            $createdItems++;
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new \RuntimeException('Data gagal disimpan ke database.');
        }

        return [
            'items_created' => $createdItems,
            'items_skipped' => $skippedItems,
        ];
    }

    private function mapImportHeaders(array $headerRow, string $layoutType = 'standard'): array
    {
        $map = [];
        $statementHeaders = match ($layoutType) {
            'document_review' => ['item telaah', 'pernyataan', 'butir', 'butir pernyataan'],
            'interview_guide' => ['pertanyaan wawancara', 'pertanyaan', 'pernyataan', 'butir', 'butir pernyataan'],
            'observation_guide' => ['indikator', 'indikator yang diamati', 'pernyataan', 'butir', 'butir pernyataan'],
            'performance_test' => ['fokus penilaian', 'pernyataan', 'butir', 'butir pernyataan'],
            'rubric_assessment' => ['indikator', 'fokus penilaian', 'pernyataan', 'butir', 'butir pernyataan'],
            default => ['pernyataan', 'butir', 'butir pernyataan'],
        };

        foreach ($headerRow as $index => $header) {
            $key = $this->normalizeImportText((string) $header);

            if (in_array($key, ['no', 'nomor', 'nomor butir'], true)) {
                $map['number'] = $index;
            } elseif (in_array($key, ['aspek', 'nama aspek'], true)) {
                $map['aspect_name'] = $index;
            } elseif (in_array($key, $statementHeaders, true)) {
                $map['statement'] = $index;
            } elseif (in_array($key, ['indikator', 'indikator kisi kisi', 'indikator kisi-kisi', 'nama indikator'], true)) {
                $map['indicator_text'] = $index;
            } elseif (in_array($key, ['sumber dokumen', 'sumber', 'dokumen'], true)) {
                $map['source_document'] = $index;
            } elseif (preg_match('/^skor\s*([1-5])$/', $key, $matches) === 1) {
                $map['score_' . $matches[1]] = $index;
            } elseif (in_array($key, ['tipe', 'tipe butir'], true)) {
                $map['item_type'] = $index;
            } elseif (in_array($key, ['wajib', 'wajib diisi'], true)) {
                $map['required'] = $index;
            } elseif (in_array($key, ['urutan', 'urutan tampil'], true)) {
                $map['order'] = $index;
            } elseif ($key === 'status') {
                $map['status'] = $index;
            }
        }

        return $map;
    }

    private function normalizeItemType(string $value): string
    {
        $value = $this->normalizeImportText($value);
        $allowed = ['skala', 'komentar', 'isian', 'pilihan', 'catatan'];

        return in_array($value, $allowed, true) ? $value : 'skala';
    }

    private function normalizeRequired(string $value): int
    {
        $value = $this->normalizeImportText($value);

        return in_array($value, ['0', 'tidak', 'no', 'n'], true) ? 0 : 1;
    }

    private function normalizeItemStatus(string $value): string
    {
        $value = $this->normalizeImportText($value);
        $statusMap = [
            'aktif'       => 'Aktif',
            'perlu revisi'=> 'Perlu Revisi',
            'direvisi'    => 'Direvisi',
            'tidak aktif' => 'Tidak Aktif',
        ];

        return $statusMap[$value] ?? 'Aktif';
    }

    private function readXlsxRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('File .xlsx tidak dapat dibuka.');
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetPath = $this->findFirstWorksheetPath($zip);
        if ($sheetPath === null) {
            $zip->close();
            throw new \RuntimeException('Worksheet tidak ditemukan.');
        }

        $sheetXml = $zip->getFromName($sheetPath);
        $zip->close();

        if ($sheetXml === false) {
            throw new \RuntimeException('Worksheet tidak dapat dibaca.');
        }

        $sheet = simplexml_load_string($sheetXml);
        if (!$sheet) {
            throw new \RuntimeException('Worksheet tidak valid.');
        }

        $namespaces = $sheet->getNamespaces(true);
        $namespace = $namespaces[''] ?? null;
        $sheetData = $namespace ? $sheet->children($namespace)->sheetData : $sheet->sheetData;
        $rows = [];

        foreach ($sheetData->children($namespace) as $row) {
            $values = [];
            foreach ($row->children($namespace) as $cell) {
                $attributes = $cell->attributes();
                $reference = (string) ($attributes['r'] ?? '');
                $columnIndex = $this->xlsxColumnIndex($reference);
                $values[$columnIndex] = $this->readXlsxCellValue($cell, $sharedStrings, $namespace);
            }

            if ($values !== []) {
                ksort($values);
                $max = max(array_keys($values));
                $rows[] = array_replace(array_fill(0, $max + 1, ''), $values);
            }
        }

        return $rows;
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $strings = [];
        $shared = simplexml_load_string($xml);
        if (!$shared) {
            return [];
        }

        $namespaces = $shared->getNamespaces(true);
        $namespace = $namespaces[''] ?? null;

        foreach ($shared->children($namespace) as $item) {
            $text = '';
            foreach ($item->xpath('.//*[local-name()="t"]') as $node) {
                $text .= (string) $node;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function findFirstWorksheetPath(ZipArchive $zip): ?string
    {
        $paths = [];
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            if (is_string($name) && preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                $paths[] = $name;
            }
        }

        natsort($paths);

        return $paths ? array_values($paths)[0] : null;
    }

    private function readXlsxCellValue(\SimpleXMLElement $cell, array $sharedStrings, ?string $namespace): string
    {
        $attributes = $cell->attributes();
        $type = (string) ($attributes['t'] ?? '');
        $children = $cell->children($namespace);

        if ($type === 's') {
            $index = (int) ($children->v ?? -1);
            return trim((string) ($sharedStrings[$index] ?? ''));
        }

        if ($type === 'inlineStr') {
            $text = '';
            foreach ($cell->xpath('.//*[local-name()="t"]') as $node) {
                $text .= (string) $node;
            }
            return trim($text);
        }

        return trim((string) ($children->v ?? ''));
    }

    private function xlsxColumnIndex(string $cellReference): int
    {
        preg_match('/^[A-Z]+/i', $cellReference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    private function normalizeImportText(string $text): string
    {
        return preg_replace('/\s+/', ' ', strtolower(trim($text))) ?? '';
    }

    private function readImportNumber($value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return max(1, (int) $value);
    }

    private function isImportRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function xlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '</Types>';
    }

    private function xlsxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function xlsxWorkbookXml(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . htmlspecialchars($sheetName, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function xlsxWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '</Relationships>';
    }

    private function xlsxStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function xlsxSharedStringsXml(array $strings): string
    {
        $items = '';
        foreach ($strings as $string) {
            $items .= '<si><t>' . htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</t></si>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">'
            . $items
            . '</sst>';
    }

    private function importTemplateRows(array $itemLayout): array
    {
        $type = (string) ($itemLayout['type'] ?? 'standard');
        $itemHeader = (string) ($itemLayout['excel_item_header'] ?? 'Butir Pernyataan');
        $headers = ['No', (string) ($itemLayout['aspect_label'] ?? 'Aspek')];

        if ($type === 'standard' || str_contains($type, 'questionnaire')) {
            $headers[] = 'Indikator Kisi-Kisi';
        }

        $headers[] = $itemHeader;

        if (! empty($itemLayout['show_source_document'])) {
            $headers[] = 'Sumber Dokumen';
        }

        if (! empty($itemLayout['show_rubric_scores'])) {
            foreach (range(1, 5) as $score) {
                $headers[] = 'Skor ' . $score;
            }
        }

        $headers = array_merge($headers, ['Tipe Butir', 'Wajib', 'Urutan', 'Status']);

        $sampleOne = match ($type) {
            'document_review' => ['1', 'Pendekatan Pembelajaran', 'RPS mencantumkan pendekatan pembelajaran.', 'RPS', 'skala', 'Ya', '1', 'Aktif'],
            'interview_guide' => ['1', 'Perencanaan Pembelajaran', 'Bagaimana Bapak/Ibu merencanakan pembelajaran?', 'isian', 'Ya', '1', 'Aktif'],
            'observation_guide' => ['1', 'Kondisi Pembelajaran', 'Pengamatan terhadap proses pembelajaran.', 'isian', 'Ya', '1', 'Aktif'],
            'performance_test' => ['1', 'Kelayakan topik dan fokus artikel', 'Kejelasan topik, relevansi isu, batasan masalah, tujuan artikel, dan kesesuaian dengan bidang kajian.', 'skala', 'Ya', '1', 'Aktif'],
            'rubric_assessment' => [
                '1',
                'Isi Tulisan',
                'Kejelasan topik, argumentasi yang kuat, dan kelengkapan isi sesuai tujuan.',
                'Topik tidak jelas dan isi tidak sesuai.',
                'Topik kurang jelas dan argumentasi lemah.',
                'Topik cukup jelas dan argumentasi mulai tampak.',
                'Topik jelas dan argumentasi cukup kuat.',
                'Topik sangat jelas, argumentasi kuat, dan isi lengkap.',
                'skala',
                'Ya',
                '1',
                'Aktif',
            ],
            default => ['1', 'Isi', 'Indikator kisi-kisi contoh', 'Butir pernyataan contoh.', 'skala', 'Ya', '1', 'Aktif'],
        };

        $sampleTwo = $sampleOne;
        $sampleTwo[0] = '2';
        $sampleTwo[count($sampleTwo) - 2] = '2';

        return [$headers, $sampleOne, $sampleTwo];
    }

    private function xlsxSharedStringsFromRows(array $rows): array
    {
        $strings = [];

        foreach ($rows as $row) {
            foreach ($row as $value) {
                $value = (string) $value;
                if ($value !== '' && !in_array($value, $strings, true)) {
                    $strings[] = $value;
                }
            }
        }

        return $strings;
    }

    private function xlsxTemplateSheetXml(array $rows, array $sharedStrings): string
    {
        $stringIndex = array_flip($sharedStrings);
        $maxColumns = max(array_map('count', $rows));
        $columns = '';

        for ($column = 1; $column <= $maxColumns; $column++) {
            $width = $column === 1 ? 8 : ($column <= 3 ? 28 : 22);
            $columns .= '<col min="' . $column . '" max="' . $column . '" width="' . $width . '" customWidth="1"/>';
        }

        $sheetData = '';
        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $sheetData .= '<row r="' . $rowNumber . '">';

            foreach ($row as $columnIndex => $value) {
                $cellRef = $this->xlsxColumnName($columnIndex + 1) . $rowNumber;
                $style = $rowIndex === 0 ? ' s="1"' : '';
                $sheetData .= '<c r="' . $cellRef . '" t="s"' . $style . '><v>' . ($stringIndex[(string) $value] ?? 0) . '</v></c>';
            }

            $sheetData .= '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>' . $columns . '</cols>'
            . '<sheetData>' . $sheetData . '</sheetData>'
            . '</worksheet>';
    }

    private function xlsxColumnName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function findOwnedInstrument(int $instrumentId): ?array
    {
        if ($instrumentId <= 0) {
            return null;
        }

        return $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->where('instruments.id', $instrumentId)
            ->first();
    }

    private function findOwnedItem($id): ?array
    {
        if ((int) $id <= 0) {
            return null;
        }

        return $this->itemModel
            ->scopeOwned('instrument_items.user_id')
            ->where('instrument_items.id', (int) $id)
            ->first();
    }

    private function ownerIdFromInstrument(array $instrument): int
    {
        return (int) ($instrument['user_id'] ?? $this->currentUserId());
    }
}
