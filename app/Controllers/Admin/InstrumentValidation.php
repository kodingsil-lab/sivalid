<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\ManualValidInstrumentModel;

class InstrumentValidation extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected ManualValidInstrumentModel $manualValidInstrumentModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->manualValidInstrumentModel = new ManualValidInstrumentModel();
    }

    public function valid()
    {
        $selectedRows = $this->manualValidInstrumentModel
            ->scopeOwned('manual_valid_instruments.user_id')
            ->select('instrument_id')
            ->findAll();
        $selectedIds = array_map(static fn(array $row): int => (int) $row['instrument_id'], $selectedRows);

        $masterQuery = $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->orderBy('kode', 'ASC')
            ->orderBy('judul', 'ASC');

        if ($selectedIds !== []) {
            $masterQuery->whereNotIn('id', $selectedIds);
        }

        $data = [
            'title'             => 'Instrumen Valid',
            'instruments'       => $this->manualValidInstrumentModel->getWithInstrument(),
            'masterInstruments' => $masterQuery->findAll(),
        ];

        return view('admin/validations/valid_instruments', $data);
    }

    public function chooseFromMaster()
    {
        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->findOwnedInstrument($instrumentId);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instrumen-valid'))
                ->with('error', 'Master instrumen tidak ditemukan.');
        }

        $alreadySelected = $this->manualValidInstrumentModel
            ->scopeOwned('manual_valid_instruments.user_id')
            ->where('instrument_id', $instrumentId)
            ->first();

        if ($alreadySelected) {
            return redirect()
                ->to(base_url('admin/instrumen-valid'))
                ->with('error', 'Instrumen ini sudah ada di daftar Instrumen Valid.');
        }

        $db = db_connect();
        $db->transStart();

        $this->manualValidInstrumentModel->insert([
            'user_id'       => $this->ownerIdFromInstrument($instrument),
            'instrument_id' => $instrumentId,
            'source'        => 'master',
        ]);

        $this->instrumentModel->update($instrumentId, [
            'status' => 'Valid',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->to(base_url('admin/instrumen-valid'))
                ->with('error', 'Instrumen gagal ditambahkan dari master.');
        }

        return redirect()
            ->to(base_url('admin/instrumen-valid'))
            ->with('success', 'Instrumen berhasil ditambahkan dari master instrumen.');
    }

    public function delete($id = null)
    {
        $manualValidInstrument = $this->findOwnedManualValidInstrument($id);

        if (!$manualValidInstrument) {
            return redirect()
                ->to(base_url('admin/instrumen-valid'))
                ->with('error', 'Data instrumen valid tidak ditemukan.');
        }

        $instrumentId = (int) ($manualValidInstrument['instrument_id'] ?? 0);

        $db = db_connect();
        $db->transStart();

        $this->manualValidInstrumentModel->delete((int) $id);

        if ($instrumentId > 0) {
            $stillMarkedValid = $this->manualValidInstrumentModel
                ->scopeOwned('manual_valid_instruments.user_id')
                ->where('instrument_id', $instrumentId)
                ->first();

            if (!$stillMarkedValid) {
                $instrument = $this->findOwnedInstrument($instrumentId);
                $currentStatus = (string) ($instrument['status'] ?? '');

                if (in_array($currentStatus, ['Valid', 'Siap Disebar'], true)) {
                    $this->instrumentModel->update($instrumentId, [
                        'status' => 'Aktif',
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->to(base_url('admin/instrumen-valid'))
                ->with('error', 'Instrumen gagal dihapus dari daftar Instrumen Valid.');
        }

        return redirect()
            ->to(base_url('admin/instrumen-valid'))
            ->with('success', 'Instrumen berhasil dihapus dari daftar Instrumen Valid dan status master dikembalikan.');
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

    private function findOwnedManualValidInstrument($id): ?array
    {
        if ((int) $id <= 0) {
            return null;
        }

        return $this->manualValidInstrumentModel
            ->scopeOwned('manual_valid_instruments.user_id')
            ->where('manual_valid_instruments.id', (int) $id)
            ->first();
    }

    private function ownerIdFromInstrument(array $instrument): int
    {
        return (int) ($instrument['user_id'] ?? $this->currentUserId());
    }
}
