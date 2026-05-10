<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\SettingModel;

class Instruments extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $perPage = config('Pager')->perPage;

        $query = $this->instrumentModel;

        if ($keyword !== '') {
            $query = $query
                ->groupStart()
                ->like('kode', $keyword)
                ->orLike('judul', $keyword)
                ->orLike('jenis', $keyword)
                ->orLike('sasaran', $keyword)
                ->orLike('status', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'       => 'Master Instrumen',
            'keyword'     => $keyword,
            'instruments' => $this->appendUsageCounts($query->orderBy('id', 'DESC')->paginate($perPage, 'instruments')),
            'pager'       => $this->instrumentModel->pager,
            'pagerGroup'  => 'instruments',
        ];

        return view('admin/instruments/index', $data);
    }

    public function new()
    {
        $data = [
            'title'      => 'Tambah Instrumen',
            'instrument' => null,
            'autoCode'   => $this->generateNextCode(),
            'jenisOptions' => $this->getJenisOptions(),
            'action'     => base_url('admin/instruments'),
            'method'     => 'post',
        ];

        return view('admin/instruments/form', $data);
    }

    public function create()
    {
        $rules = [
            'judul'     => 'required|min_length[5]|max_length[255]',
            'jenis'     => 'required',
            'skala_min' => 'required|integer',
            'skala_max' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $skalaMin = (int) $this->request->getPost('skala_min');
        $skalaMax = (int) $this->request->getPost('skala_max');

        if ($skalaMin >= $skalaMax) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Skala minimal harus lebih kecil dari skala maksimal.');
        }

        $kode = $this->generateNextCode();

        $this->instrumentModel->insert([
            'kode'      => $kode,
            'judul'     => trim((string) $this->request->getPost('judul')),
            'jenis'     => trim((string) $this->request->getPost('jenis')),
            'sasaran'   => trim((string) $this->request->getPost('sasaran')),
            'pengantar' => trim((string) $this->request->getPost('pengantar')),
            'petunjuk'  => trim((string) $this->request->getPost('petunjuk')),
            'skala_min' => $skalaMin,
            'skala_max' => $skalaMax,
            'status'    => 'Draft',
        ]);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil ditambahkan.');
    }

    public function show($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $data = [
            'title'      => 'Detail Instrumen',
            'instrument' => $instrument,
        ];

        return view('admin/instruments/show', $data);
    }

    public function edit($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Instrumen',
            'instrument' => $instrument,
            'jenisOptions' => $this->getJenisOptions(),
            'action'     => base_url('admin/instruments/' . $id),
            'method'     => 'put',
        ];

        return view('admin/instruments/form', $data);
    }

    public function update($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $rules = [
            'judul'     => 'required|min_length[5]|max_length[255]',
            'jenis'     => 'required',
            'skala_min' => 'required|integer',
            'skala_max' => 'required|integer',
            'status'    => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $skalaMin = (int) $this->request->getPost('skala_min');
        $skalaMax = (int) $this->request->getPost('skala_max');

        if ($skalaMin >= $skalaMax) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Skala minimal harus lebih kecil dari skala maksimal.');
        }

        $manualStatuses = ['Draft', 'Aktif'];
        $currentStatus = (string) ($instrument['status'] ?? 'Draft');
        $requestedStatus = trim((string) $this->request->getPost('status'));
        $statusToSave = $currentStatus;

        if (in_array($currentStatus, $manualStatuses, true)) {
            if (in_array($requestedStatus, $manualStatuses, true)) {
                $statusToSave = $requestedStatus;
            }
        }

        $this->instrumentModel->update($id, [
            'judul'     => trim((string) $this->request->getPost('judul')),
            'jenis'     => trim((string) $this->request->getPost('jenis')),
            'sasaran'   => trim((string) $this->request->getPost('sasaran')),
            'pengantar' => trim((string) $this->request->getPost('pengantar')),
            'petunjuk'  => trim((string) $this->request->getPost('petunjuk')),
            'skala_min' => $skalaMin,
            'skala_max' => $skalaMax,
            'status'    => $statusToSave,
        ]);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $instrument = $this->instrumentModel->find($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $usageCounts = $this->getUsageCounts((int) $id);

        if (array_sum($usageCounts) > 0) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with(
                    'error',
                    'Instrumen tidak bisa dihapus karena masih memiliki '
                    . $usageCounts['aspects'] . ' aspek, '
                    . $usageCounts['indicators'] . ' indikator, dan '
                    . $usageCounts['items'] . ' butir. Hapus data tersebut terlebih dahulu.'
                );
        }

        $this->instrumentModel->delete($id);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil dihapus.');
    }

    private function getJenisOptions(): array
    {
        return $this->settingModel->getInstrumentTypes([
            'Angket',
            'Wawancara',
            'Observasi',
            'FGD',
            'Tes Kinerja',
            'Rubrik Penilaian',
            'Dokumentasi',
        ]);
    }

    private function appendUsageCounts(array $instruments): array
    {
        if ($instruments === []) {
            return [];
        }

        $ids = array_map(static fn(array $instrument): int => (int) $instrument['id'], $instruments);
        $countsByTable = [
            'aspects'    => $this->getCountsByInstrument('instrument_aspects', $ids),
            'indicators' => $this->getCountsByInstrument('instrument_indicators', $ids),
            'items'      => $this->getCountsByInstrument('instrument_items', $ids),
        ];

        foreach ($instruments as &$instrument) {
            $instrumentId = (int) $instrument['id'];
            $usageCounts = [
                'aspects'    => $countsByTable['aspects'][$instrumentId] ?? 0,
                'indicators' => $countsByTable['indicators'][$instrumentId] ?? 0,
                'items'      => $countsByTable['items'][$instrumentId] ?? 0,
            ];

            $instrument['usage_counts'] = $usageCounts;
            $instrument['can_delete'] = array_sum($usageCounts) === 0;
        }
        unset($instrument);

        return $instruments;
    }

    private function getCountsByInstrument(string $table, array $instrumentIds): array
    {
        if ($instrumentIds === []) {
            return [];
        }

        $rows = db_connect()
            ->table($table)
            ->select('instrument_id, COUNT(*) AS total')
            ->whereIn('instrument_id', $instrumentIds)
            ->groupBy('instrument_id')
            ->get()
            ->getResultArray();

        $counts = [];

        foreach ($rows as $row) {
            $counts[(int) $row['instrument_id']] = (int) $row['total'];
        }

        return $counts;
    }

    private function getUsageCounts(int $instrumentId): array
    {
        $db = db_connect();

        return [
            'aspects'    => $db->table('instrument_aspects')->where('instrument_id', $instrumentId)->countAllResults(),
            'indicators' => $db->table('instrument_indicators')->where('instrument_id', $instrumentId)->countAllResults(),
            'items'      => $db->table('instrument_items')->where('instrument_id', $instrumentId)->countAllResults(),
        ];
    }

    private function generateNextCode(): string
    {
        $codes = $this->instrumentModel->select('kode')->findAll();
        $max = 0;

        foreach ($codes as $row) {
            $code = trim((string) ($row['kode'] ?? ''));

            if ($code === '') {
                continue;
            }

            if (preg_match('/(\d+)$/', $code, $matches) === 1) {
                $max = max($max, (int) $matches[1]);
            }
        }

        $next = $max + 1;

        do {
            $candidate = str_pad((string) $next, 2, '0', STR_PAD_LEFT);
            $exists = $this->instrumentModel->where('kode', $candidate)->first();
            $next++;
        } while ($exists !== null);

        return $candidate;
    }
}
