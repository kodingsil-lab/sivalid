<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentModel;
use App\Models\SettingModel;

class InstrumentTypes extends BaseController
{
    protected SettingModel $settingModel;
    protected InstrumentModel $instrumentModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->instrumentModel = new InstrumentModel();
    }

    public function index()
    {
        $types = $this->settingModel->getGroupRows('instrument_type');

        if ($types === []) {
            $this->seedDefaultTypes();
            $types = $this->settingModel->getGroupRows('instrument_type');
        }

        $usage = [];
        foreach ($this->instrumentModel->select('jenis, COUNT(*) as total')->groupBy('jenis')->findAll() as $row) {
            $usage[(string) ($row['jenis'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        return view('admin/instrument_types/index', [
            'title' => 'Jenis Instrumen',
            'types' => $types,
            'usage' => $usage,
        ]);
    }

    public function create()
    {
        $rules = [
            'jenis' => 'required|min_length[2]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->to(base_url('admin/settings?tab=instrument-types'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $label = trim((string) $this->request->getPost('jenis'));

        $existing = $this->settingModel->getInstrumentTypes();
        foreach ($existing as $item) {
            if (mb_strtolower($item) === mb_strtolower($label)) {
                return redirect()
                    ->to(base_url('admin/settings?tab=instrument-types'))
                    ->with('error', 'Jenis instrumen sudah ada.');
            }
        }

        $this->settingModel->insert([
            'setting_key' => 'instrument_type_' . time() . '_' . random_int(100, 999),
            'setting_value' => $label,
            'setting_group' => 'instrument_type',
        ]);

        return redirect()
            ->to(base_url('admin/settings?tab=instrument-types'))
            ->with('success', 'Jenis instrumen berhasil ditambahkan.');
    }

    public function delete($id = null)
    {
        $type = $this->settingModel
            ->where('id', (int) $id)
            ->where('setting_group', 'instrument_type')
            ->first();

        if (! $type) {
            return redirect()
                ->to(base_url('admin/settings?tab=instrument-types'))
                ->with('error', 'Jenis instrumen tidak ditemukan.');
        }

        $label = trim((string) ($type['setting_value'] ?? ''));
        $used = $this->instrumentModel->where('jenis', $label)->countAllResults();

        if ($used > 0) {
            return redirect()
                ->to(base_url('admin/settings?tab=instrument-types'))
                ->with('error', 'Jenis instrumen tidak bisa dihapus karena masih dipakai pada master instrumen.');
        }

        $this->settingModel->delete((int) $type['id']);

        return redirect()
            ->to(base_url('admin/settings?tab=instrument-types'))
            ->with('success', 'Jenis instrumen berhasil dihapus.');
    }

    private function seedDefaultTypes(): void
    {
        $defaults = [
            'Angket',
            'Wawancara',
            'Observasi',
            'FGD',
            'Tes Kinerja',
            'Rubrik Penilaian',
            'Dokumentasi',
        ];

        foreach ($defaults as $index => $label) {
            $this->settingModel->insert([
                'setting_key' => 'instrument_type_default_' . ($index + 1),
                'setting_value' => $label,
                'setting_group' => 'instrument_type',
            ]);
        }
    }
}
