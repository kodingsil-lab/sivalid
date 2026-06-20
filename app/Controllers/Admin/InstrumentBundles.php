<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentItemModel;
use App\Models\InstrumentModel;
use App\Models\ValidationBundleAnswerModel;
use App\Models\ValidationBundleInstrumentProgressModel;
use App\Models\ValidationBundleModel;
use App\Models\ValidationBundleInstrumentModel;
use App\Models\ValidationBundleSessionModel;

class InstrumentBundles extends BaseController
{
    protected ValidationBundleModel $bundleModel;
    protected ValidationBundleInstrumentModel $bundleInstrumentModel;
    protected InstrumentModel $instrumentModel;
    protected AuditLogService $auditLog;

    public function __construct()
    {
        $this->bundleModel           = new ValidationBundleModel();
        $this->bundleInstrumentModel = new ValidationBundleInstrumentModel();
        $this->instrumentModel       = new InstrumentModel();
        $this->auditLog              = new AuditLogService();
    }

    public function index()
    {
        $perPage = config('Pager')->perPage;
        $bundles = $this->bundleModel->paginateWithInstrumentCount($perPage, 'validation_bundles');

        return view('admin/bundles/index', [
            'title'   => 'Paket Validasi Instrumen',
            'bundles' => $bundles,
            'pager'   => $this->bundleModel->pager,
            'pagerGroup' => 'validation_bundles',
        ]);
    }

    public function new()
    {
        return view('admin/bundles/form', [
            'title'       => 'Buat Paket Validasi Instrumen',
            'bundle'      => [],
            'selected'    => [],
            'selectedDetails' => [],
            'instruments' => $this->getOwnedInstrumentOptions(),
            'action'      => base_url('admin/instrument-bundles'),
            'method'      => 'post',
        ]);
    }

    public function create()
    {
        $customToken = $this->normalizeToken((string) $this->request->getPost('token'));

        $rules = [
            'judul'           => 'required|min_length[3]|max_length[255]',
            'token'           => 'permit_empty|min_length[4]|max_length[50]|alpha_dash|is_unique[validation_bundles.token]',
            'deskripsi'       => 'permit_empty',
            'sasaran'         => 'permit_empty|max_length[150]',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'token_expires_at'  => 'permit_empty',
            'status'          => 'required',
            'instrument_ids'  => 'required',
        ];

        $messages = [
            'token' => [
                'min_length' => 'Token minimal 4 karakter.',
                'max_length' => 'Token maksimal 50 karakter.',
                'alpha_dash' => 'Token hanya boleh berisi huruf, angka, underscore, atau dash.',
                'is_unique'  => 'Token sudah digunakan. Gunakan token lain.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentIds = $this->request->getPost('instrument_ids');

        if (!is_array($instrumentIds) || count($instrumentIds) === 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Pilih minimal satu instrumen untuk paket.');
        }

        $submittedInstrumentIds = $this->normalizeInstrumentIds($instrumentIds);
        $instrumentIds = $this->filterOwnedInstrumentIds($instrumentIds);

        if ($instrumentIds === [] || count($instrumentIds) !== count($submittedInstrumentIds)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen yang dipilih tidak ditemukan atau bukan milik akun Anda.');
        }

        $token = $customToken !== '' ? $customToken : $this->generateUniqueToken();

        $bundleId = $this->bundleModel->insert($this->bundleModel->withOwner([
            'token'           => $token,
            'token_access_mode' => 'single_use',
            'judul'           => trim((string) $this->request->getPost('judul')),
            'deskripsi'       => trim((string) ($this->request->getPost('deskripsi') ?? '')),
            'sasaran'         => trim((string) ($this->request->getPost('sasaran') ?? '')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'token_expires_at' => $this->toDateTimeOrNull($this->request->getPost('token_expires_at')),
            'token_revoked_at' => null,
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => 1,
        ]));

        $this->bundleInstrumentModel->syncBundle((int) $bundleId, $instrumentIds, $this->collectValidationTexts($instrumentIds));

        $this->auditLog->log(
            AuditLogService::ACTION_CREATE_BUNDLE,
            AuditLogService::ENTITY_BUNDLE,
            (int) $bundleId,
            'Buat paket validasi: ' . trim((string) $this->request->getPost('judul'))
        );

        return redirect()
            ->to(base_url('admin/instrument-bundles'))
            ->with('success', 'Paket validasi berhasil dibuat.');
    }

    public function show($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $instruments = $this->bundleInstrumentModel->getByBundle((int) $id);

        return view('admin/bundles/show', [
            'title'       => 'Detail Paket Validasi Instrumen',
            'bundle'      => $bundle,
            'instruments' => $instruments,
        ]);
    }

    public function sessions($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $sessionModel  = new ValidationBundleSessionModel();
        $progressModel = new ValidationBundleInstrumentProgressModel();

        $sessions    = $sessionModel->getByBundle((int) $id);
        $instruments = $this->bundleInstrumentModel->getByBundle((int) $id);
        $total       = count($instruments);

        $sessionData = [];
        foreach ($sessions as $session) {
            $progressMap = $progressModel->getBySession((int) $session['id']);
            $selesaiCount = count(array_filter($progressMap, static fn(array $p): bool => ($p['status'] ?? 'belum') === 'selesai'));

            $sessionData[] = array_merge($session, [
                'selesai_count' => $selesaiCount,
                'total'         => $total,
            ]);
        }

        return view('admin/bundles/sessions', [
            'title'    => 'Monitor Validator',
            'bundle'   => $bundle,
            'sessions' => $sessionData,
        ]);
    }

    public function sessionDetail($id = null, $sessionId = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $sessionModel  = new ValidationBundleSessionModel();
        $answerModel   = new ValidationBundleAnswerModel();
        $progressModel = new ValidationBundleInstrumentProgressModel();
        $itemModel     = new InstrumentItemModel();
        $aspectModel   = new InstrumentAspectModel();

        $validatorSession = $sessionModel->find($sessionId);

        if (!$validatorSession || (int) $validatorSession['bundle_id'] !== (int) $id) {
            return redirect()
                ->to(base_url('admin/instrument-bundles/' . $id . '/sessions'))
                ->with('error', 'Sesi validator tidak ditemukan.');
        }

        $instruments   = $this->bundleInstrumentModel->getByBundle((int) $id);
        $answersByInst = $answerModel->getGroupedByInstrument((int) $sessionId);
        $progressMap   = $progressModel->getBySession((int) $sessionId);

        $instrumentDetails = [];

        foreach ($instruments as $index => $instr) {
            $instrumentId = (int) $instr['instrument_id'];

            $aspects = $aspectModel
                ->scopeOwned('instrument_aspects.user_id')
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->findAll();

            $aspectNames = [];
            foreach ($aspects as $aspect) {
                $aspectNames[(int) $aspect['id']] = $aspect['nama_aspek'];
            }

            $items = $itemModel
                ->scopeOwned('instrument_items.user_id')
                ->where('instrument_id', $instrumentId)
                ->orderBy('urutan', 'ASC')
                ->orderBy('nomor', 'ASC')
                ->findAll();

            $answersMap = $answersByInst[$instrumentId] ?? [];
            $rows = [];

            foreach ($items as $item) {
                $itemId = (int) $item['id'];
                $answer = $answersMap[$itemId] ?? null;
                $tipeButir = $item['tipe_butir'] ?? 'skala';

                $jawaban = '-';
                if ($answer) {
                    if ($tipeButir === 'skala') {
                        $jawaban = $answer['skor'] !== null ? (string) $answer['skor'] : '-';
                    } else {
                        $jawaban = trim((string) ($answer['jawaban_teks'] ?? ''));
                        if ($jawaban === '') {
                            $jawaban = '-';
                        }
                    }
                }

                $rows[] = [
                    'nomor'      => $item['nomor'] ?? '-',
                    'aspek'      => $aspectNames[(int) ($item['aspect_id'] ?? 0)] ?? '-',
                    'pernyataan' => $item['pernyataan'] ?? '-',
                    'jawaban'    => $jawaban,
                    'komentar'   => trim((string) ($answer['komentar'] ?? '')) ?: '-',
                ];
            }

            $instrumentDetails[] = [
                'position'      => $index + 1,
                'instrument'    => $instr,
                'status'        => $progressMap[$instrumentId]['status'] ?? 'belum',
                'kesimpulan'    => $progressMap[$instrumentId]['kesimpulan'] ?? null,
                'komentar_umum' => $progressMap[$instrumentId]['komentar_umum'] ?? null,
                'items'         => $rows,
            ];
        }

        return view('admin/bundles/session_detail', [
            'title'             => 'Detail Sesi Validator',
            'bundle'            => $bundle,
            'validatorSession'  => $validatorSession,
            'instrumentDetails' => $instrumentDetails,
        ]);
    }

    public function edit($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $bundleInstruments = $this->bundleInstrumentModel->getByBundle((int) $id);
        $selected = array_column($bundleInstruments, 'instrument_id');
        $selectedDetails = [];

        foreach ($bundleInstruments as $bundleInstrument) {
            $selectedDetails[(int) $bundleInstrument['instrument_id']] = $bundleInstrument;
        }

        return view('admin/bundles/form', [
            'title'       => 'Edit Paket Validasi Instrumen',
            'bundle'      => $bundle,
            'selected'    => $selected,
            'selectedDetails' => $selectedDetails,
            'instruments' => $this->getOwnedInstrumentOptions(),
            'action'      => base_url('admin/instrument-bundles/' . $id),
            'method'      => 'put',
        ]);
    }

    public function update($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $customToken = $this->normalizeToken((string) $this->request->getPost('token'));

        $rules = [
            'judul'           => 'required|min_length[3]|max_length[255]',
            'token'           => 'required|min_length[4]|max_length[50]|alpha_dash|is_unique[validation_bundles.token,id,' . (int) $id . ']',
            'deskripsi'       => 'permit_empty',
            'sasaran'         => 'permit_empty|max_length[150]',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'token_expires_at'  => 'permit_empty',
            'status'          => 'required',
            'instrument_ids'  => 'required',
        ];

        $messages = [
            'token' => [
                'required'   => 'Token wajib diisi.',
                'min_length' => 'Token minimal 4 karakter.',
                'max_length' => 'Token maksimal 50 karakter.',
                'alpha_dash' => 'Token hanya boleh berisi huruf, angka, underscore, atau dash.',
                'is_unique'  => 'Token sudah digunakan. Gunakan token lain.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentIds = $this->request->getPost('instrument_ids');

        if (!is_array($instrumentIds) || count($instrumentIds) === 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Pilih minimal satu instrumen untuk paket.');
        }

        $submittedInstrumentIds = $this->normalizeInstrumentIds($instrumentIds);
        $instrumentIds = $this->filterOwnedInstrumentIds($instrumentIds);

        if ($instrumentIds === [] || count($instrumentIds) !== count($submittedInstrumentIds)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen yang dipilih tidak ditemukan atau bukan milik akun Anda.');
        }

        $this->bundleModel->update($id, [
            'token'           => $customToken,
            'token_access_mode' => 'single_use',
            'judul'           => trim((string) $this->request->getPost('judul')),
            'deskripsi'       => trim((string) ($this->request->getPost('deskripsi') ?? '')),
            'sasaran'         => trim((string) ($this->request->getPost('sasaran') ?? '')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'token_expires_at' => $this->toDateTimeOrNull($this->request->getPost('token_expires_at')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => 1,
        ]);

        $this->bundleInstrumentModel->syncBundle((int) $id, $instrumentIds, $this->collectValidationTexts($instrumentIds));

        $this->auditLog->log(
            AuditLogService::ACTION_UPDATE_BUNDLE,
            AuditLogService::ENTITY_BUNDLE,
            (int) $id,
            'Perbarui paket validasi: ' . trim((string) $this->request->getPost('judul'))
        );

        return redirect()
            ->to(base_url('admin/instrument-bundles'))
            ->with('success', 'Paket validasi berhasil diperbarui.');
    }

    public function duplicate($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $bundleInstruments = $this->bundleInstrumentModel->getByBundle((int) $id);

        if (empty($bundleInstruments)) {
            return redirect()
                ->to(base_url('admin/instrument-bundles/' . $id))
                ->with('error', 'Paket ini belum memiliki instrumen untuk diduplikasi.');
        }

        $newToken = $this->generateUniqueToken();
        $newBundleId = $this->bundleModel->insert($this->bundleModel->withOwner([
            'token'              => $newToken,
            'token_access_mode'  => 'single_use',
            'judul'              => $this->generateDuplicateTitle((string) ($bundle['judul'] ?? 'Paket Validasi Instrumen')),
            'deskripsi'          => trim((string) ($bundle['deskripsi'] ?? '')),
            'sasaran'            => '',
            'tanggal_mulai'      => $bundle['tanggal_mulai'] ?? null,
            'tanggal_selesai'    => $bundle['tanggal_selesai'] ?? null,
            'token_expires_at'    => $bundle['token_expires_at'] ?? null,
            'token_revoked_at'    => null,
            'status'             => 'Aktif',
            'maksimal_respon'    => 1,
        ]), true);

        $instrumentIds = array_map(static fn(array $row): int => (int) $row['instrument_id'], $bundleInstruments);
        $validationTexts = [];
        foreach ($bundleInstruments as $row) {
            $instrumentId = (int) $row['instrument_id'];
            $validationTexts[$instrumentId] = [
                'pengantar_validasi' => trim((string) ($row['pengantar_validasi'] ?? '')),
                'petunjuk_validasi'  => trim((string) ($row['petunjuk_validasi'] ?? '')),
                'skala_min'          => (int) ($row['skala_min'] ?? 1),
                'skala_max'          => (int) ($row['skala_max'] ?? 4),
                'skala_labels'       => trim((string) ($row['skala_labels'] ?? '')),
                'status_validasi'    => trim((string) ($row['status_validasi'] ?? 'Siap Divalidasi')),
            ];
        }

        $this->bundleInstrumentModel->syncBundle((int) $newBundleId, $instrumentIds, $validationTexts);

        $this->auditLog->log(
            AuditLogService::ACTION_CREATE_BUNDLE,
            AuditLogService::ENTITY_BUNDLE,
            (int) $newBundleId,
            'Duplikat paket validasi dari #' . (int) $id . ' menjadi #' . (int) $newBundleId
        );

        return redirect()
            ->to(base_url('admin/instrument-bundles/' . $newBundleId . '/edit'))
            ->with('success', 'Paket berhasil diduplikasi. Silakan ubah nama validator dan detail lain bila perlu.');
    }

    public function delete($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $this->bundleModel->delete($id);

        $this->auditLog->log(
            AuditLogService::ACTION_DELETE_BUNDLE,
            AuditLogService::ENTITY_BUNDLE,
            (int) $id,
            'Hapus paket validasi: ' . ($bundle['judul'] ?? '-')
        );

        return redirect()
            ->to(base_url('admin/instrument-bundles'))
            ->with('success', 'Paket validasi berhasil dihapus.');
    }

    public function revokeToken($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $this->bundleModel->update((int) $id, [
            'token_revoked_at' => date('Y-m-d H:i:s'),
        ]);

        $this->auditLog->log(
            AuditLogService::ACTION_REVOKE_BUNDLE_TOKEN,
            AuditLogService::ENTITY_BUNDLE_TOKEN,
            (int) $id,
            'Revoke token paket: ' . ($bundle['judul'] ?? '-')
        );

        return redirect()
            ->to(base_url('admin/instrument-bundles/' . $id))
            ->with('success', 'Token paket berhasil direvoke.');
    }

    public function activateToken($id = null)
    {
        $bundle = $this->findOwnedBundle($id);

        if (!$bundle) {
            return redirect()
                ->to(base_url('admin/instrument-bundles'))
                ->with('error', 'Paket validasi tidak ditemukan.');
        }

        $this->bundleModel->update((int) $id, [
            'token_revoked_at' => null,
        ]);

        $this->auditLog->log(
            AuditLogService::ACTION_ACTIVATE_BUNDLE_TOKEN,
            AuditLogService::ENTITY_BUNDLE_TOKEN,
            (int) $id,
            'Aktifkan ulang token paket: ' . ($bundle['judul'] ?? '-')
        );

        return redirect()
            ->to(base_url('admin/instrument-bundles/' . $id))
            ->with('success', 'Token paket berhasil diaktifkan kembali.');
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = $this->generateReadableToken(12);
            $exists = $this->bundleModel->where('token', $token)->first();
        } while ($exists);

        return $token;
    }

    private function generateReadableToken(int $length = 12): string
    {
        $length = max(8, $length);
        $alphabet = '23456789abcdefghjkmnpqrstuvwxyz';
        $maxIndex = strlen($alphabet) - 1;

        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $alphabet[random_int(0, $maxIndex)];
        }

        return $token;
    }

    private function normalizeToken(string $token): string
    {
        return strtolower(trim($token));
    }

    private function generateDuplicateTitle(string $title): string
    {
        $title = trim($title);

        if ($title === '') {
            return 'Paket Validasi Instrumen - Salinan';
        }

        return preg_match('/\(Salinan\)$/i', $title) === 1
            ? $title
            : $title . ' (Salinan)';
    }

    private function emptyToNull($value)
    {
        return $value === '' || $value === null ? null : $value;
    }

    private function collectValidationTexts(array $instrumentIds): array
    {
        $pengantar = $this->request->getPost('pengantar_validasi');
        $petunjuk  = $this->request->getPost('petunjuk_validasi');
        $skalaMin  = $this->request->getPost('skala_min_validasi');
        $skalaMax  = $this->request->getPost('skala_max_validasi');
        $skalaLabels = $this->request->getPost('skala_labels_validasi');
        $statusValidasi = $this->request->getPost('status_validasi');

        $pengantar = is_array($pengantar) ? $pengantar : [];
        $petunjuk  = is_array($petunjuk) ? $petunjuk : [];
        $skalaMin  = is_array($skalaMin) ? $skalaMin : [];
        $skalaMax  = is_array($skalaMax) ? $skalaMax : [];
        $skalaLabels = is_array($skalaLabels) ? $skalaLabels : [];
        $statusValidasi = is_array($statusValidasi) ? $statusValidasi : [];
        $texts     = [];

        foreach ($instrumentIds as $instrumentId) {
            $instrumentId = (int) $instrumentId;
            $min = max(1, (int) ($skalaMin[$instrumentId] ?? 1));
            $max = max($min, (int) ($skalaMax[$instrumentId] ?? 4));

            $texts[$instrumentId] = [
                'pengantar_validasi' => trim((string) ($pengantar[$instrumentId] ?? '')),
                'petunjuk_validasi'  => trim((string) ($petunjuk[$instrumentId] ?? '')),
                'skala_min'          => $min,
                'skala_max'          => $max,
                'skala_labels'       => trim((string) ($skalaLabels[$instrumentId] ?? '')),
                'status_validasi'    => trim((string) ($statusValidasi[$instrumentId] ?? 'Siap Divalidasi')) ?: 'Siap Divalidasi',
            ];
        }

        return $texts;
    }

    private function toDateTimeOrNull($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function findOwnedBundle($id): ?array
    {
        if ((int) $id <= 0) {
            return null;
        }

        return $this->bundleModel
            ->scopeOwned('validation_bundles.user_id')
            ->where('validation_bundles.id', (int) $id)
            ->first();
    }

    private function getOwnedInstrumentOptions(): array
    {
        return $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->orderBy('kode', 'ASC')
            ->orderBy('judul', 'ASC')
            ->findAll();
    }

    private function filterOwnedInstrumentIds(array $instrumentIds): array
    {
        $ids = $this->normalizeInstrumentIds($instrumentIds);

        if ($ids === []) {
            return [];
        }

        $rows = $this->instrumentModel
            ->scopeOwned('instruments.user_id')
            ->select('id')
            ->whereIn('id', $ids)
            ->findAll();

        return array_map(static fn(array $row): int => (int) $row['id'], $rows);
    }

    private function normalizeInstrumentIds(array $instrumentIds): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $instrumentIds), static fn(int $id): bool => $id > 0)));
    }
}
