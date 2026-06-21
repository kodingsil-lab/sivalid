<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;

class InstrumentIndicators extends BaseController
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

        if ($instrumentId !== null && ! $this->findOwnedInstrument($instrumentId)) {
            return redirect()
                ->to(base_url('admin/instrument-indicators'))
                ->with('error', 'Instrumen tidak ditemukan atau bukan milik akun Anda.');
        }

        $data = [
            'title'        => 'Indikator Kisi-Kisi Instrumen',
            'instrumentId' => $instrumentId,
            'instruments'  => $this->instrumentModel->scopeOwned('instruments.user_id')->orderBy('judul', 'ASC')->findAll(),
            'indicators'   => $this->indicatorModel->paginateWithRelations($instrumentId, $perPage, 'instrument_indicators'),
            'pager'        => $this->indicatorModel->pager,
            'pagerGroup'   => 'instrument_indicators',
        ];

        return view('admin/indicators/index', $data);
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
            'aspect_id'     => 'required|integer',
            'indikator'     => 'required|min_length[3]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');

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

        $this->indicatorModel->insert([
            'user_id'       => $this->ownerIdFromInstrument($instrument),
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indikator'     => trim((string) $this->request->getPost('indikator')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $indicator = $this->findOwnedIndicator($id);

        if (!$indicator) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data indikator tidak ditemukan.');
        }

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $indicator['instrument_id']))
            ->with('info', 'Edit dilakukan melalui popup pada halaman Kisi-Kisi Instrumen.');
    }

    public function update($id = null)
    {
        $indicator = $this->findOwnedIndicator($id);

        if (!$indicator) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data indikator tidak ditemukan.');
        }

        $rules = [
            'instrument_id' => 'required|integer',
            'aspect_id'     => 'required|integer',
            'indikator'     => 'required|min_length[3]',
            'urutan'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $aspectId     = (int) $this->request->getPost('aspect_id');

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

        $this->indicatorModel->update($id, [
            'user_id'       => $this->ownerIdFromInstrument($instrument),
            'instrument_id' => $instrumentId,
            'aspect_id'     => $aspectId,
            'indikator'     => trim((string) $this->request->getPost('indikator')),
            'urutan'        => (int) $this->request->getPost('urutan'),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $indicator = $this->findOwnedIndicator($id);

        if (!$indicator) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Data indikator tidak ditemukan.');
        }

        $instrumentId = $indicator['instrument_id'];

        $this->indicatorModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-aspects?instrument_id=' . $instrumentId))
            ->with('success', 'Indikator berhasil dihapus.');
    }

    public function bulkDelete()
    {
        $instrumentId = (int) ($this->request->getPost('instrument_id') ?? 0);
        $redirectUrl = base_url('admin/instrument-aspects' . ($instrumentId > 0 ? '?instrument_id=' . $instrumentId : ''));

        if ($instrumentId > 0 && ! $this->findOwnedInstrument($instrumentId)) {
            return redirect()
                ->to(base_url('admin/instrument-aspects'))
                ->with('error', 'Instrumen tidak ditemukan atau bukan milik akun Anda.');
        }

        $ids = $this->selectedIdsFromPost();

        if ($ids === []) {
            return redirect()
                ->to($redirectUrl)
                ->with('error', 'Pilih minimal satu indikator yang akan dihapus.');
        }

        $query = $this->indicatorModel
            ->scopeOwned('instrument_indicators.user_id')
            ->select('instrument_indicators.id')
            ->whereIn('instrument_indicators.id', $ids);

        if ($instrumentId > 0) {
            $query->where('instrument_indicators.instrument_id', $instrumentId);
        }

        $ownedIds = array_map(
            static fn(array $row): int => (int) $row['id'],
            $query->findAll()
        );

        if ($ownedIds === []) {
            return redirect()
                ->to($redirectUrl)
                ->with('error', 'Indikator terpilih tidak ditemukan atau bukan milik akun Anda.');
        }

        $this->indicatorModel->delete($ownedIds);

        return redirect()
            ->to($redirectUrl)
            ->with('success', count($ownedIds) . ' indikator berhasil dihapus.');
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

    private function findOwnedIndicator($id): ?array
    {
        if ((int) $id <= 0) {
            return null;
        }

        return $this->indicatorModel
            ->scopeOwned('instrument_indicators.user_id')
            ->where('instrument_indicators.id', (int) $id)
            ->first();
    }

    private function selectedIdsFromPost(): array
    {
        $ids = (array) $this->request->getPost('ids');
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static fn(int $id): bool => $id > 0);

        return array_values(array_unique($ids));
    }

    private function ownerIdFromInstrument(array $instrument): int
    {
        return (int) ($instrument['user_id'] ?? $this->currentUserId());
    }
}
