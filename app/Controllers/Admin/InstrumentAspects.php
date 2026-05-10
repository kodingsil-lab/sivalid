<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;
use ZipArchive;

class InstrumentAspects extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentAspectModel $aspectModel;
    protected InstrumentIndicatorModel $indicatorModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->aspectModel     = new InstrumentAspectModel();
        $this->indicatorModel  = new InstrumentIndicatorModel();
    }

    public function index()
    {
        $instrumentId = $this->request->getGet('instrument_id');
        $instrumentId = $instrumentId !== null && $instrumentId !== '' ? (int) $instrumentId : null;
        $perPage = config('Pager')->perPage;

        $data = [
            'title'        => 'Kisi-Kisi Instrumen',
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->getOrderedByCodeSequence(),
            'aspects'      => $this->aspectModel->getWithInstrument($instrumentId),
            'aspectList'   => $this->aspectModel->paginateWithInstrument($instrumentId, $perPage, 'instrument_aspects'),
            'indicators'   => $this->indicatorModel->getWithRelations($instrumentId),
            'pager'        => $this->aspectModel->pager,
            'pagerGroup'   => 'instrument_aspects',
        ];

        return view('admin/aspects/index', $data);
    }

    public function new()
    {
        $instrumentId = (int) ($this->request->getGet('instrument_id') ?? 0);

        return redirect()
            ->to(base_url('admin/instrument-aspects' . ($instrumentId > 0 ? '?instrument_id=' . $instrumentId : '')))
            ->with('info', 'Form lama sudah dinonaktifkan. Gunakan popup pada halaman Kisi-Kisi Instrumen.');
    }

    public function create()
    {
        $rules = [
            'instrument_id' => 'required|integer',
            'nama_aspek'    => 'required|min_length[2]|max_length[200]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->aspectModel->insert([
            'instrument_id' => (int) $this->request->getPost('instrument_id'),
            'nama_aspek'    => trim((string) $this->request->getPost('nama_aspek')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $this->request->getPost('instrument_id')))
            ->with('success', 'Aspek instrumen berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $aspect = $this->aspectModel->find($id);

        if (!$aspect) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data aspek tidak ditemukan.');
        }

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $aspect['instrument_id']))
            ->with('info', 'Edit dilakukan melalui popup pada halaman Kisi-Kisi Instrumen.');
    }

    public function update($id = null)
    {
        $aspect = $this->aspectModel->find($id);

        if (!$aspect) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data aspek tidak ditemukan.');
        }

        $rules = [
            'instrument_id' => 'required|integer',
            'nama_aspek'    => 'required|min_length[2]|max_length[200]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->aspectModel->update($id, [
            'instrument_id' => (int) $this->request->getPost('instrument_id'),
            'nama_aspek'    => trim((string) $this->request->getPost('nama_aspek')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $this->request->getPost('instrument_id')))
            ->with('success', 'Aspek instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $aspect = $this->aspectModel->find($id);

        if (!$aspect) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data aspek tidak ditemukan.');
        }

        $instrumentId = $aspect['instrument_id'];

        $this->aspectModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Aspek instrumen berhasil dihapus.');
    }

    public function import()
    {
        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument   = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        $rules = [
            'file_excel' => 'uploaded[file_excel]|max_size[file_excel,4096]|ext_in[file_excel,xlsx]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
                ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()
                ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
                ->with('error', 'File Excel tidak valid.');
        }

        try {
            $rows = $this->readXlsxRows($file->getTempName());
            $result = $this->importRows($instrumentId, $rows);
        } catch (\Throwable $exception) {
            return redirect()
                ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
                ->with('error', 'Import gagal: ' . $exception->getMessage());
        }

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', sprintf(
                'Import selesai. Aspek baru: %d, indikator baru: %d, indikator duplikat dilewati: %d.',
                $result['aspects_created'],
                $result['indicators_created'],
                $result['indicators_skipped']
            ));
    }

    public function importTemplate()
    {
        $fileName = 'template-import-kisi-kisi.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'kisi_template_');

        if ($tempPath === false) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Template gagal dibuat.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Template gagal dibuat.');
        }

        $sharedStrings = [
            'No Aspek',
            'Aspek',
            'Deskripsi Aspek',
            'No Indikator',
            'Indikator',
            'Pendahuluan',
            '-',
            'Kejelasan latar belakang dan urgensi pengembangan model pembelajaran.',
            'Kesesuaian tujuan model pembelajaran dengan kebutuhan pembelajaran.',
            'Sintaks',
            'Kejelasan urutan sintaks pembelajaran.',
        ];

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
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
        if (!isset($headers['aspect_name'])) {
            throw new \RuntimeException('Kolom "Aspek" wajib ada.');
        }
        if (!isset($headers['indicator_text'])) {
            throw new \RuntimeException('Kolom "Indikator" wajib ada.');
        }

        $existingAspects = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $aspectsByName = [];
        $nextAspectOrder = 1;
        foreach ($existingAspects as $aspect) {
            $aspectsByName[$this->normalizeImportText((string) $aspect['nama_aspek'])] = $aspect;
            $nextAspectOrder = max($nextAspectOrder, (int) ($aspect['urutan'] ?? 0) + 1);
        }

        $existingIndicators = $this->indicatorModel
            ->where('instrument_id', $instrumentId)
            ->findAll();

        $indicatorsByAspect = [];
        $nextIndicatorOrders = [];
        foreach ($existingIndicators as $indicator) {
            $aspectId = (int) $indicator['aspect_id'];
            $indicatorsByAspect[$aspectId][$this->normalizeImportText((string) $indicator['indikator'])] = true;
            $nextIndicatorOrders[$aspectId] = max($nextIndicatorOrders[$aspectId] ?? 1, (int) ($indicator['urutan'] ?? 0) + 1);
        }

        $createdAspects = 0;
        $createdIndicators = 0;
        $skippedIndicators = 0;
        $db = db_connect();

        $db->transStart();

        foreach (array_slice($rows, 1) as $row) {
            if ($this->isImportRowEmpty($row)) {
                continue;
            }

            $aspectName = trim((string) ($row[$headers['aspect_name']] ?? ''));
            if ($aspectName === '') {
                continue;
            }

            $aspectKey = $this->normalizeImportText($aspectName);
            $aspect = $aspectsByName[$aspectKey] ?? null;

            if (!$aspect) {
                $aspectOrder = $this->readImportNumber($row[$headers['aspect_order']] ?? null) ?? $nextAspectOrder++;
                $aspectDescription = trim((string) ($row[$headers['aspect_description'] ?? -1] ?? ''));

                $aspectId = $this->aspectModel->insert([
                    'instrument_id' => $instrumentId,
                    'nama_aspek'    => $aspectName,
                    'deskripsi'     => $aspectDescription,
                    'urutan'        => $aspectOrder,
                ], true);

                $aspect = [
                    'id'            => $aspectId,
                    'instrument_id' => $instrumentId,
                    'nama_aspek'    => $aspectName,
                    'deskripsi'     => $aspectDescription,
                    'urutan'        => $aspectOrder,
                ];
                $aspectsByName[$aspectKey] = $aspect;
                $createdAspects++;
            }

            $indicatorText = trim((string) ($row[$headers['indicator_text'] ?? -1] ?? ''));
            if ($indicatorText === '') {
                continue;
            }

            $aspectId = (int) $aspect['id'];
            $indicatorKey = $this->normalizeImportText($indicatorText);
            if (isset($indicatorsByAspect[$aspectId][$indicatorKey])) {
                $skippedIndicators++;
                continue;
            }

            $indicatorOrder = $this->readImportNumber($row[$headers['indicator_order'] ?? -1] ?? null)
                ?? ($nextIndicatorOrders[$aspectId] ?? 1);

            $this->indicatorModel->insert([
                'instrument_id' => $instrumentId,
                'aspect_id'     => $aspectId,
                'indikator'     => $indicatorText,
                'urutan'        => $indicatorOrder,
            ]);

            $indicatorsByAspect[$aspectId][$indicatorKey] = true;
            $nextIndicatorOrders[$aspectId] = max($nextIndicatorOrders[$aspectId] ?? 1, $indicatorOrder + 1);
            $createdIndicators++;
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new \RuntimeException('Data gagal disimpan ke database.');
        }

        return [
            'aspects_created'     => $createdAspects,
            'indicators_created'  => $createdIndicators,
            'indicators_skipped'  => $skippedIndicators,
        ];
    }

    private function mapImportHeaders(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $index => $header) {
            $key = $this->normalizeImportText((string) $header);

            if (in_array($key, ['no aspek', 'urutan aspek', 'nomor aspek'], true)) {
                $map['aspect_order'] = $index;
            } elseif (in_array($key, ['aspek', 'nama aspek'], true)) {
                $map['aspect_name'] = $index;
            } elseif (in_array($key, ['deskripsi aspek', 'deskripsi'], true)) {
                $map['aspect_description'] = $index;
            } elseif (in_array($key, ['no indikator', 'urutan indikator', 'nomor indikator'], true)) {
                $map['indicator_order'] = $index;
            } elseif (in_array($key, ['indikator', 'nama indikator'], true)) {
                $map['indicator_text'] = $index;
            }
        }

        return $map;
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

    private function xlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Template Import" sheetId="1" r:id="rId1"/></sheets>'
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
            . '<cols><col min="1" max="1" width="12" customWidth="1"/><col min="2" max="2" width="24" customWidth="1"/><col min="3" max="3" width="28" customWidth="1"/><col min="4" max="4" width="14" customWidth="1"/><col min="5" max="5" width="72" customWidth="1"/></cols>'
            . '<sheetData>'
            . '<row r="1"><c r="A1" t="s" s="1"><v>0</v></c><c r="B1" t="s" s="1"><v>1</v></c><c r="C1" t="s" s="1"><v>2</v></c><c r="D1" t="s" s="1"><v>3</v></c><c r="E1" t="s" s="1"><v>4</v></c></row>'
            . '<row r="2"><c r="A2"><v>1</v></c><c r="B2" t="s"><v>5</v></c><c r="C2" t="s"><v>6</v></c><c r="D2"><v>1</v></c><c r="E2" t="s"><v>7</v></c></row>'
            . '<row r="3"><c r="A3"><v>1</v></c><c r="B3" t="s"><v>5</v></c><c r="C3" t="s"><v>6</v></c><c r="D3"><v>2</v></c><c r="E3" t="s"><v>8</v></c></row>'
            . '<row r="4"><c r="A4"><v>2</v></c><c r="B4" t="s"><v>9</v></c><c r="C4" t="s"><v>6</v></c><c r="D4"><v>1</v></c><c r="E4" t="s"><v>10</v></c></row>'
            . '</sheetData>'
            . '</worksheet>';
    }
}
