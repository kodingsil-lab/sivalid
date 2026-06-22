<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstrumentAttachmentModel;
use App\Models\InstrumentItemModel;
use App\Models\InstrumentModel;
use App\Models\SettingModel;

class Instruments extends BaseController
{
    protected InstrumentModel $instrumentModel;
    protected InstrumentAttachmentModel $attachmentModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->attachmentModel = new InstrumentAttachmentModel();
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $perPage = config('Pager')->perPage;

        $query = $this->instrumentModel->scopeOwned('instruments.user_id');

        if ($keyword !== '') {
            $query = $query
                ->groupStart()
                ->like('kode', $keyword)
                ->orLike('judul', $keyword)
                ->orLike('jenis', $keyword)
                ->orLike('sasaran', $keyword)
                ->orLike('keterangan', $keyword)
                ->orLike('status', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'       => 'Master Instrumen',
            'keyword'     => $keyword,
            'instruments' => $this->appendUsageCounts($query->orderBy('sort_order', 'ASC')->orderBy('id', 'DESC')->paginate($perPage, 'instruments')),
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
            'kode'      => 'required|max_length[50]',
            'judul'     => 'required|min_length[5]|max_length[255]',
            'jenis'     => 'required',
            'keterangan' => 'permit_empty|max_length[255]',
            'skala_min' => 'required|integer',
            'skala_max' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $attachmentErrors = $this->attachmentUploadErrors();
        if ($attachmentErrors !== []) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $attachmentErrors);
        }

        $scaleConfig = $this->scaleConfigFromRequest();
        $skalaMin = $scaleConfig['min'];
        $skalaMax = $scaleConfig['max'];

        if ($skalaMin >= $skalaMax) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Skala minimal harus lebih kecil dari skala maksimal.');
        }

        $kode = trim((string) $this->request->getPost('kode'));

        if ($this->codeExistsForOwner($kode)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', ['kode' => 'Kode instrumen sudah digunakan pada akun ini.']);
        }

        $instrumentId = (int) $this->instrumentModel->insert($this->instrumentModel->withOwner([
            'kode'      => $kode,
            'judul'     => trim((string) $this->request->getPost('judul')),
            'jenis'     => trim((string) $this->request->getPost('jenis')),
            'sasaran'   => trim((string) $this->request->getPost('sasaran')),
            'keterangan' => trim((string) $this->request->getPost('keterangan')),
            'pengantar' => trim((string) $this->request->getPost('pengantar')),
            'petunjuk'  => trim((string) $this->request->getPost('petunjuk')),
            'skala_min' => $skalaMin,
            'skala_max' => $skalaMax,
            'skala_labels' => $scaleConfig['labels_json'],
            'sort_order' => $this->getNextSortOrder(),
            'status'    => 'Draft',
        ]), true);

        $this->saveAttachmentsFromRequest($instrumentId);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil ditambahkan.');
    }

    public function show($id = null)
    {
        $instrument = $this->findOwnedInstrument($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $itemModel = new InstrumentItemModel();
        $items = $itemModel->getWithRelations((int) $id);
        usort($items, static function (array $left, array $right): int {
            return [
                (int) ($left['urutan'] ?? 0),
                (int) ($left['nomor'] ?? 0),
                (int) ($left['id'] ?? 0),
            ] <=> [
                (int) ($right['urutan'] ?? 0),
                (int) ($right['nomor'] ?? 0),
                (int) ($right['id'] ?? 0),
            ];
        });

        $data = [
            'title'      => 'Detail Instrumen',
            'instrument' => $instrument,
            'items'      => $items,
            'attachments' => $this->attachmentModel->getByInstrument((int) $id),
        ];

        return view('admin/instruments/show', $data);
    }

    public function edit($id = null)
    {
        $instrument = $this->findOwnedInstrument($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Instrumen',
            'instrument' => $instrument,
            'attachments' => $this->attachmentModel->getByInstrument((int) $id),
            'jenisOptions' => $this->getJenisOptions(),
            'isManualValid' => $this->isManualValidInstrument((int) $id),
            'action'     => base_url('admin/instruments/' . $id),
            'method'     => 'put',
        ];

        return view('admin/instruments/form', $data);
    }

    public function update($id = null)
    {
        $instrument = $this->findOwnedInstrument($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        $rules = [
            'kode'      => 'required|max_length[50]',
            'judul'     => 'required|min_length[5]|max_length[255]',
            'jenis'     => 'required',
            'keterangan' => 'permit_empty|max_length[255]',
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

        $attachmentErrors = $this->attachmentUploadErrors();
        if ($attachmentErrors !== []) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $attachmentErrors);
        }

        $kode = trim((string) $this->request->getPost('kode'));

        if ($this->codeExistsForOwner($kode, (int) $id, (int) ($instrument['user_id'] ?? 0))) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', ['kode' => 'Kode instrumen sudah digunakan pada akun ini.']);
        }

        $scaleConfig = $this->scaleConfigFromRequest();
        $skalaMin = $scaleConfig['min'];
        $skalaMax = $scaleConfig['max'];

        if ($skalaMin >= $skalaMax) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Skala minimal harus lebih kecil dari skala maksimal.');
        }

        $manualStatuses = ['Draft', 'Aktif', 'Perlu Revisi', 'Direvisi', 'Tidak Aktif'];
        $currentStatus = (string) ($instrument['status'] ?? 'Draft');
        $requestedStatus = trim((string) $this->request->getPost('status'));
        $statusToSave = $currentStatus;

        if (! $this->isManualValidInstrument((int) $id) && in_array($requestedStatus, $manualStatuses, true)) {
            $statusToSave = $requestedStatus;
        }

        $this->instrumentModel->update($id, [
            'kode'      => $kode,
            'judul'     => trim((string) $this->request->getPost('judul')),
            'jenis'     => trim((string) $this->request->getPost('jenis')),
            'sasaran'   => trim((string) $this->request->getPost('sasaran')),
            'keterangan' => trim((string) $this->request->getPost('keterangan')),
            'pengantar' => trim((string) $this->request->getPost('pengantar')),
            'petunjuk'  => trim((string) $this->request->getPost('petunjuk')),
            'skala_min' => $skalaMin,
            'skala_max' => $skalaMax,
            'skala_labels' => $scaleConfig['labels_json'],
            'status'    => $statusToSave,
        ]);

        $this->deleteSelectedAttachments((int) $id);
        $this->saveAttachmentsFromRequest((int) $id);

        return redirect()
            ->to(base_url('admin/instruments/' . (int) $id))
            ->with('success', 'Data instrumen berhasil diperbarui.');
    }

    public function move($id = null, ?string $direction = null)
    {
        $instrument = $this->findOwnedInstrument($id);

        if (!$instrument) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Data instrumen tidak ditemukan.');
        }

        if (!in_array($direction, ['up', 'down'], true)) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Arah urutan instrumen tidak valid.');
        }

        $this->normalizeSortOrder();

        $rows = $this->instrumentModel
            ->select('id, sort_order')
            ->scopeOwned('instruments.user_id')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $currentIndex = null;

        foreach ($rows as $index => $row) {
            if ((int) $row['id'] === (int) $id) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === null) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('error', 'Posisi instrumen tidak ditemukan.');
        }

        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        if (!isset($rows[$targetIndex])) {
            return redirect()
                ->to(base_url('admin/instruments'))
                ->with('info', 'Instrumen sudah berada di posisi paling ' . ($direction === 'up' ? 'atas.' : 'bawah.'));
        }

        $current = $rows[$currentIndex];
        $target = $rows[$targetIndex];

        $this->instrumentModel->update((int) $current['id'], ['sort_order' => (int) $target['sort_order']]);
        $this->instrumentModel->update((int) $target['id'], ['sort_order' => (int) $current['sort_order']]);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Urutan instrumen berhasil diperbarui.');
    }

    public function reorder()
    {
        $order = $this->request->getPost('order');
        $offset = (int) ($this->request->getPost('offset') ?? 0);

        if (!is_array($order) || $order === []) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success'  => false,
                    'message'  => 'Urutan instrumen tidak valid.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $ids = array_values(array_unique(array_map(static fn($id): int => (int) $id, $order)));
        $ids = array_values(array_filter($ids, static fn(int $id): bool => $id > 0));

        if (count($ids) !== count($order)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success'  => false,
                    'message'  => 'Urutan instrumen tidak valid.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $existingIds = $this->instrumentModel
            ->select('id')
            ->scopeOwned('instruments.user_id')
            ->whereIn('id', $ids)
            ->findAll();
        $existingIds = array_map(static fn(array $row): int => (int) $row['id'], $existingIds);

        if (count($existingIds) !== count($ids)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success'  => false,
                    'message'  => 'Ada instrumen yang tidak ditemukan.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        $this->normalizeSortOrder();

        foreach ($ids as $index => $instrumentId) {
            $this->instrumentModel->update($instrumentId, [
                'sort_order' => $offset + $index + 1,
            ]);
        }

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Urutan instrumen berhasil disimpan.',
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function delete($id = null)
    {
        $instrument = $this->findOwnedInstrument($id);

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

        $this->deleteAllAttachments((int) $id);
        $this->instrumentModel->delete($id);

        return redirect()
            ->to(base_url('admin/instruments'))
            ->with('success', 'Data instrumen berhasil dihapus.');
    }

    private function getJenisOptions(): array
    {
        return $this->settingModel->getInstrumentTypes(sivalid_default_instrument_types());
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

    private function saveAttachmentsFromRequest(int $instrumentId): void
    {
        if ($instrumentId <= 0) {
            return;
        }

        $files = $this->request->getFileMultiple('attachment_files');
        $titles = $this->request->getPost('attachment_titles');

        if (!is_array($files) || $files === []) {
            return;
        }

        $titles = is_array($titles) ? $titles : [];
        $targetDir = FCPATH . 'uploads/instrument-attachments';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $sortOrder = $this->nextAttachmentSortOrder($instrumentId);

        foreach ($files as $index => $file) {
            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (!$file->isValid()) {
                continue;
            }

            $extension = strtolower((string) ($file->getClientExtension() ?: $file->guessExtension()));
            $mimeType = (string) $file->getMimeType();

            if ($extension !== 'pdf' || !in_array($mimeType, ['application/pdf', 'application/x-pdf'], true)) {
                continue;
            }

            if ($file->getSizeByUnit('kb') > 10240) {
                continue;
            }

            $title = trim((string) ($titles[$index] ?? ''));
            if ($title === '') {
                $title = 'Lampiran Instrumen';
            }

            $fileName = 'lampiran-instrumen-' . $instrumentId . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.pdf';
            $file->move($targetDir, $fileName);

            $this->attachmentModel->insert([
                'instrument_id' => $instrumentId,
                'title'         => $title,
                'file_path'     => 'uploads/instrument-attachments/' . $fileName,
                'sort_order'    => $sortOrder++,
            ]);
        }
    }

    private function attachmentUploadErrors(): array
    {
        $files = $this->request->getFileMultiple('attachment_files');

        if (!is_array($files) || $files === []) {
            return [];
        }

        $errors = [];

        foreach ($files as $index => $file) {
            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $label = 'Lampiran #' . ($index + 1);

            if (!$file->isValid()) {
                $errors['attachment_files_' . $index] = $label . ' gagal diunggah.';
                continue;
            }

            $extension = strtolower((string) ($file->getClientExtension() ?: $file->guessExtension()));
            $mimeType = (string) $file->getMimeType();

            if ($extension !== 'pdf' || !in_array($mimeType, ['application/pdf', 'application/x-pdf'], true)) {
                $errors['attachment_files_' . $index] = $label . ' harus berupa file PDF.';
                continue;
            }

            if ($file->getSizeByUnit('kb') > 10240) {
                $errors['attachment_files_' . $index] = $label . ' maksimal 10 MB.';
            }
        }

        return $errors;
    }

    private function deleteSelectedAttachments(int $instrumentId): void
    {
        $deleteIds = $this->request->getPost('delete_attachments');

        if (!is_array($deleteIds) || $deleteIds === []) {
            return;
        }

        $deleteIds = array_values(array_unique(array_filter(array_map('intval', $deleteIds), static fn(int $id): bool => $id > 0)));

        if ($deleteIds === []) {
            return;
        }

        $attachments = $this->attachmentModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('id', $deleteIds)
            ->findAll();

        foreach ($attachments as $attachment) {
            $path = (string) ($attachment['file_path'] ?? '');

            if ($path !== '' && str_starts_with($path, 'uploads/instrument-attachments/')) {
                $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

                if (is_file($fullPath)) {
                    unlink($fullPath);
                }
            }

            $this->attachmentModel->delete((int) $attachment['id']);
        }
    }

    private function deleteAllAttachments(int $instrumentId): void
    {
        $attachments = $this->attachmentModel
            ->where('instrument_id', $instrumentId)
            ->findAll();

        foreach ($attachments as $attachment) {
            $path = (string) ($attachment['file_path'] ?? '');

            if ($path !== '' && str_starts_with($path, 'uploads/instrument-attachments/')) {
                $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

                if (is_file($fullPath)) {
                    unlink($fullPath);
                }
            }
        }
    }

    private function nextAttachmentSortOrder(int $instrumentId): int
    {
        $row = $this->attachmentModel
            ->selectMax('sort_order')
            ->where('instrument_id', $instrumentId)
            ->first();

        return ((int) ($row['sort_order'] ?? 0)) + 1;
    }

    private function generateNextCode(): string
    {
        $codes = $this->instrumentModel->scopeOwned('instruments.user_id')->select('kode')->findAll();
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
            $candidate = 'INS-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $exists = $this->codeExistsForOwner($candidate);
            $next++;
        } while ($exists);

        return $candidate;
    }

    private function getNextSortOrder(): int
    {
        $row = $this->instrumentModel
            ->selectMax('sort_order')
            ->scopeOwned('instruments.user_id')
            ->first();

        return ((int) ($row['sort_order'] ?? 0)) + 1;
    }

    private function isManualValidInstrument(int $instrumentId): bool
    {
        return db_connect()
            ->table('manual_valid_instruments')
            ->where('instrument_id', $instrumentId)
            ->countAllResults() > 0;
    }

    private function scaleConfigFromRequest(): array
    {
        $templateKey = trim((string) $this->request->getPost('scale_template'));
        $templates = sivalid_scale_templates();

        if (isset($templates[$templateKey])) {
            return [
                'min' => (int) $templates[$templateKey]['min'],
                'max' => (int) $templates[$templateKey]['max'],
                'labels_json' => json_encode($templates[$templateKey]['labels'], JSON_UNESCAPED_UNICODE),
            ];
        }

        return [
            'min' => (int) $this->request->getPost('skala_min'),
            'max' => (int) $this->request->getPost('skala_max'),
            'labels_json' => null,
        ];
    }

    private function normalizeSortOrder(): void
    {
        $rows = $this->instrumentModel
            ->select('id, sort_order')
            ->scopeOwned('instruments.user_id')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();

        foreach ($rows as $index => $row) {
            $expected = $index + 1;

            if ((int) ($row['sort_order'] ?? 0) === $expected) {
                continue;
            }

            $this->instrumentModel->update((int) $row['id'], ['sort_order' => $expected]);
        }
    }

    private function findOwnedInstrument($id): ?array
    {
        if ((int) $id <= 0) {
            return null;
        }

        return $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->where('instruments.id', (int) $id)
            ->first();
    }

    private function codeExistsForOwner(string $kode, ?int $excludeId = null, ?int $ownerId = null): bool
    {
        $ownerId = $ownerId !== null && $ownerId > 0 ? $ownerId : $this->currentUserId();

        $query = $this->instrumentModel->where('kode', $kode);

        if ($ownerId > 0) {
            $query->where('user_id', $ownerId);
        } else {
            $query->where('user_id', -1);
        }

        if ($excludeId !== null) {
            $query->where('id !=', $excludeId);
        }

        return $query->first() !== null;
    }
}
