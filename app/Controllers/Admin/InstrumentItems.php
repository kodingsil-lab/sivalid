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

        $data = [
            'title'        => 'Butir Pernyataan Instrumen',
            'instrumentId' => $instrumentId,
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

        $aspects = [];
        $indicators = [];

        if ($instrumentId !== null) {
            $aspects = $this->aspectModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();

            $indicators = $this->indicatorModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();
        }

        $nextNumber = 1;

        if ($instrumentId !== null) {
            $lastItem = $this->itemModel
                ->where('instrument_id', $instrumentId)
                ->orderBy('nomor', 'DESC')
                ->first();

            if ($lastItem) {
                $nextNumber = ((int) $lastItem['nomor']) + 1;
            }
        }

        $data = [
            'title'        => 'Tambah Butir Pernyataan',
            'item'         => null,
            'instrumentId' => $instrumentId,
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

        $aspect = $this->aspectModel->where([
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
            $indicator = $this->indicatorModel->where([
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
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indicator_id'  => !empty($indicatorId) ? (int) $indicatorId : null,
            'nomor'         => (int) $this->request->getPost('nomor'),
            'pernyataan'    => trim((string) $this->request->getPost('pernyataan')),
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
        $item = $this->itemModel->find($id);

        if (!$item) {
            return redirect()
                ->to(base_url('admin/instrument-items'))
                ->with('error', 'Data butir pernyataan tidak ditemukan.');
        }

        $aspects = $this->aspectModel
            ->where('instrument_id', $item['instrument_id'])
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $indicators = $this->indicatorModel
            ->where('instrument_id', $item['instrument_id'])
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $data = [
            'title'        => 'Edit Butir Pernyataan',
            'item'         => $item,
            'instrumentId' => $item['instrument_id'],
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
        $item = $this->itemModel->find($id);

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

        $aspect = $this->aspectModel->where([
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
            $indicator = $this->indicatorModel->where([
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
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indicator_id'  => !empty($indicatorId) ? (int) $indicatorId : null,
            'nomor'         => (int) $this->request->getPost('nomor'),
            'pernyataan'    => trim((string) $this->request->getPost('pernyataan')),
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
        $item = $this->itemModel->find($id);

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
        $instrument   = $this->instrumentModel->find($instrumentId);

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
            $result = $this->importRows($instrumentId, $rows);
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
        $fileName = 'template-import-butir-instrumen.xlsx';
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

        $sharedStrings = [
            'No',
            'Aspek',
            'Indikator',
            'Pernyataan',
            'Tipe Butir',
            'Wajib',
            'Urutan',
            'Status',
            'Pendahuluan',
            'Kejelasan latar belakang dan urgensi pengembangan model pembelajaran.',
            'Model pembelajaran memiliki latar belakang pengembangan yang jelas.',
            'skala',
            'Ya',
            'Aktif',
            'Model pembelajaran sesuai dengan kebutuhan pembelajaran.',
        ];

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml('Template Butir'));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->xlsxStylesXml());
        $zip->addFromString('xl/sharedStrings.xml', $this->xlsxSharedStringsXml($sharedStrings));
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxTemplateSheetXml());
        $zip->close();

        return $this->response
            ->download($tempPath, null)
            ->setFileName($fileName);
    }

    private function importRows(int $instrumentId, array $rows): array
    {
        if (count($rows) < 2) {
            throw new \RuntimeException('File tidak berisi data.');
        }

        $headers = $this->mapImportHeaders($rows[0]);
        foreach (['aspect_name' => 'Aspek', 'statement' => 'Pernyataan'] as $key => $label) {
            if (!isset($headers[$key])) {
                throw new \RuntimeException('Kolom "' . $label . '" wajib ada.');
            }
        }

        $aspects = $this->aspectModel
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
            ->where('instrument_id', $instrumentId)
            ->findAll();
        $indicatorsByAspectAndText = [];
        foreach ($indicators as $indicator) {
            $aspectId = (int) $indicator['aspect_id'];
            $indicatorsByAspectAndText[$aspectId][$this->normalizeImportText((string) $indicator['indikator'])] = $indicator;
        }

        $existingItems = $this->itemModel
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
            $type = $this->normalizeItemType((string) ($row[$headers['item_type'] ?? -1] ?? 'skala'));
            $required = $this->normalizeRequired((string) ($row[$headers['required'] ?? -1] ?? 'Ya'));
            $status = $this->normalizeItemStatus((string) ($row[$headers['status'] ?? -1] ?? 'Aktif'));

            $this->itemModel->insert([
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
                'indicator_id'  => $indicatorId,
                'nomor'         => $number,
                'pernyataan'    => $statement,
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

    private function mapImportHeaders(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $index => $header) {
            $key = $this->normalizeImportText((string) $header);

            if (in_array($key, ['no', 'nomor', 'nomor butir'], true)) {
                $map['number'] = $index;
            } elseif (in_array($key, ['aspek', 'nama aspek'], true)) {
                $map['aspect_name'] = $index;
            } elseif (in_array($key, ['indikator', 'nama indikator'], true)) {
                $map['indicator_text'] = $index;
            } elseif (in_array($key, ['pernyataan', 'butir', 'butir pernyataan'], true)) {
                $map['statement'] = $index;
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

    private function xlsxTemplateSheetXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols><col min="1" max="1" width="8" customWidth="1"/><col min="2" max="2" width="24" customWidth="1"/><col min="3" max="3" width="56" customWidth="1"/><col min="4" max="4" width="64" customWidth="1"/><col min="5" max="5" width="14" customWidth="1"/><col min="6" max="6" width="10" customWidth="1"/><col min="7" max="7" width="10" customWidth="1"/><col min="8" max="8" width="14" customWidth="1"/></cols>'
            . '<sheetData>'
            . '<row r="1"><c r="A1" t="s" s="1"><v>0</v></c><c r="B1" t="s" s="1"><v>1</v></c><c r="C1" t="s" s="1"><v>2</v></c><c r="D1" t="s" s="1"><v>3</v></c><c r="E1" t="s" s="1"><v>4</v></c><c r="F1" t="s" s="1"><v>5</v></c><c r="G1" t="s" s="1"><v>6</v></c><c r="H1" t="s" s="1"><v>7</v></c></row>'
            . '<row r="2"><c r="A2"><v>1</v></c><c r="B2" t="s"><v>8</v></c><c r="C2" t="s"><v>9</v></c><c r="D2" t="s"><v>10</v></c><c r="E2" t="s"><v>11</v></c><c r="F2" t="s"><v>12</v></c><c r="G2"><v>1</v></c><c r="H2" t="s"><v>13</v></c></row>'
            . '<row r="3"><c r="A3"><v>2</v></c><c r="B3" t="s"><v>8</v></c><c r="C3" t="s"><v>9</v></c><c r="D3" t="s"><v>14</v></c><c r="E3" t="s"><v>11</v></c><c r="F3" t="s"><v>12</v></c><c r="G3"><v>2</v></c><c r="H3" t="s"><v>13</v></c></row>'
            . '</sheetData>'
            . '</worksheet>';
    }
}
