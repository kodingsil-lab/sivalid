<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AuditLogService;
use App\Models\InstrumentAttachmentModel;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;
use App\Models\InstrumentItemModel;
use App\Models\ValidationBundleModel;
use App\Models\ValidationBundleInstrumentModel;
use App\Models\ValidationBundleSessionModel;
use App\Models\ValidationBundleAnswerModel;
use App\Models\ValidationBundleInstrumentProgressModel;
use App\Models\SettingModel;

class PublicBundle extends BaseController
{
    protected ValidationBundleModel $bundleModel;
    protected ValidationBundleInstrumentModel $bundleInstrumentModel;
    protected ValidationBundleSessionModel $sessionModel;
    protected ValidationBundleAnswerModel $answerModel;
    protected ValidationBundleInstrumentProgressModel $progressModel;
    protected InstrumentAttachmentModel $attachmentModel;
    protected InstrumentAspectModel $aspectModel;
    protected InstrumentIndicatorModel $indicatorModel;
    protected InstrumentItemModel $itemModel;
    protected SettingModel $settingModel;
    protected AuditLogService $auditLog;

    public function __construct()
    {
        $this->bundleModel           = new ValidationBundleModel();
        $this->bundleInstrumentModel = new ValidationBundleInstrumentModel();
        $this->sessionModel          = new ValidationBundleSessionModel();
        $this->answerModel           = new ValidationBundleAnswerModel();
        $this->progressModel         = new ValidationBundleInstrumentProgressModel();
        $this->attachmentModel       = new InstrumentAttachmentModel();
        $this->aspectModel           = new InstrumentAspectModel();
        $this->indicatorModel        = new InstrumentIndicatorModel();
        $this->itemModel             = new InstrumentItemModel();
        $this->settingModel          = new SettingModel();
        $this->auditLog              = new AuditLogService();
    }

    // ─── Landing page ─────────────────────────────────────────────────────────

    /**
     * Show the bundle landing page.
     * - No session: show identity form.
     * - Has session: show instrument list with progress badges.
     */
    public function show(string $token = '')
    {
        $bundle = $this->getValidatedBundle($token);

        if (isset($bundle['error_view'])) {
            return $bundle['error_view'];
        }

        $instruments      = $this->bundleInstrumentModel->getByBundle((int) $bundle['id']);
        $validatorSession = $this->resolveSession($bundle);
        $profile          = $this->settingModel->getUserProfileValues((int) ($bundle['user_id'] ?? 0));
        $editIdentity     = $this->request->getGet('identitas') === 'edit';

        if (!$validatorSession || $editIdentity) {
            return view('public/bundle_landing', [
                'title'            => esc($bundle['judul']),
                'bundle'           => $bundle,
                'instruments'      => $instruments,
                'profile'          => $profile,
                'validatorSession' => $validatorSession,
                'state'            => 'identity',
            ]);
        }

        $progressMap = $this->syncSessionProgressStatuses((int) $validatorSession['id'], $instruments);

        return view('public/bundle_landing', [
            'title'            => esc($bundle['judul']),
            'bundle'           => $bundle,
            'instruments'      => $instruments,
            'state'            => 'progress',
            'validatorSession' => $validatorSession,
            'progressMap'      => $progressMap,
            'profile'          => $profile,
        ]);
    }

    /**
     * POST: create a new validator session and redirect to landing.
     */
    public function startSession(string $token = '')
    {
        $bundle = $this->getValidatedBundle($token);

        if (isset($bundle['error_view'])) {
            return $bundle['error_view'];
        }

        $rules = [
            'nama'            => 'required|min_length[3]|max_length[150]',
            'email'           => 'permit_empty|valid_email|max_length[150]',
            'bidang_keahlian' => 'permit_empty|max_length[150]',
            'instansi'        => 'permit_empty|max_length[150]',
        ];

        $messages = [
            'nama' => [
                'required'   => 'Mohon isi nama lengkap Bapak/Ibu terlebih dahulu.',
                'min_length' => 'Nama lengkap minimal 3 karakter.',
                'max_length' => 'Nama lengkap maksimal 150 karakter.',
            ],
            'email' => [
                'valid_email' => 'Mohon isi alamat email dengan format yang benar.',
                'max_length'  => 'Alamat email maksimal 150 karakter.',
            ],
            'bidang_keahlian' => [
                'max_length' => 'Bidang keahlian maksimal 150 karakter.',
            ],
            'instansi' => [
                'max_length' => 'Instansi atau lembaga maksimal 150 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $now = date('Y-m-d H:i:s');
        $existingSession = $this->resolveSession($bundle);
        $sessionPayload = [
            'validator_nama'             => trim((string) $this->request->getPost('nama')),
            'validator_email'            => trim((string) ($this->request->getPost('email') ?? '')),
            'validator_instansi'         => trim((string) ($this->request->getPost('instansi') ?? '')),
            'validator_bidang_keahlian'  => trim((string) ($this->request->getPost('bidang_keahlian') ?? '')),
        ];

        if ($existingSession) {
            $this->sessionModel->update((int) $existingSession['id'], $sessionPayload);
            session()->set($this->sessionKey((int) $bundle['id']), (int) $existingSession['id']);

            return redirect()
                ->to(base_url('paket/' . $token))
                ->with('success', 'Identitas validator berhasil diperbarui.');
        }

        if (($bundle['token_access_mode'] ?? 'single_use') === 'single_use' && $this->sessionModel->countByBundle((int) $bundle['id']) > 0) {
            return redirect()
                ->to(base_url('paket/' . $token))
                ->with('error', 'Token ini hanya untuk satu validator dan sudah digunakan.');
        }

        $sessionId = $this->sessionModel->insert(array_merge([
            'bundle_id'      => (int) $bundle['id'],
            'status_session' => 'draft',
            'started_at'     => $now,
        ], $sessionPayload), true);

        session()->set($this->sessionKey((int) $bundle['id']), (int) $sessionId);

        $this->auditLog->log(
            AuditLogService::ACTION_BUNDLE_SESSION_START,
            AuditLogService::ENTITY_BUNDLE_SESSION,
            (int) $sessionId,
            'Sesi validator dimulai untuk bundle #' . (int) $bundle['id'],
            [
                'user_name' => trim((string) $this->request->getPost('nama')),
            ]
        );

        return redirect()->to(base_url('paket/' . $token));
    }

    // ─── Instrument form ──────────────────────────────────────────────────────

    /**
     * GET: show the form for a single instrument, pre-filled with saved answers.
     */
    public function showInstrument(string $token = '', int $position = 1)
    {
        $bundle = $this->getValidatedBundle($token);

        if (isset($bundle['error_view'])) {
            return $bundle['error_view'];
        }

        $validatorSession = $this->resolveSession($bundle);

        if (!$validatorSession) {
            return redirect()->to(base_url('paket/' . $token));
        }

        $instruments = $this->bundleInstrumentModel->getByBundle((int) $bundle['id']);

        if (empty($instruments)) {
            return redirect()->to(base_url('paket/' . $token));
        }

        $instrumentEntry = $this->findAtPosition($instruments, $position);

        if (!$instrumentEntry) {
            return redirect()->to(base_url('paket/' . $token));
        }

        $instrumentId = (int) $instrumentEntry['instrument_id'];
        $total        = count($instruments);
        $nextPos      = $position < $total ? $position + 1 : null;
        $prevPos      = $position > 1 ? $position - 1 : null;

        $aspects = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $indicators = $this->indicatorModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->orderBy('urutan', 'ASC')
            ->orderBy('nomor', 'ASC')
            ->findAll();

        $savedAnswers  = $this->answerModel->getBySessionAndInstrument(
            (int) $validatorSession['id'], $instrumentId
        );
        $progressMap   = $this->syncSessionProgressStatuses((int) $validatorSession['id'], $instruments);
        $savedProgress = $progressMap[$instrumentId] ?? $this->progressModel->getBySessionAndInstrument(
            (int) $validatorSession['id'], $instrumentId
        );
        $scale         = $this->getScaleRange($instrumentEntry);

        return view('public/bundle_instrument', [
            'title'            => esc($bundle['judul']),
            'bundle'           => $bundle,
            'instruments'      => $instruments,
            'instrumentEntry'  => $instrumentEntry,
            'position'         => $position,
            'total'            => $total,
            'nextPos'          => $nextPos,
            'prevPos'          => $prevPos,
            'aspects'          => $aspects,
            'indicators'       => $indicators,
            'items'            => $items,
            'attachments'      => $this->attachmentModel->getByInstrument($instrumentId),
            'scale'            => $scale,
            'validatorSession' => $validatorSession,
            'isFinal'          => false,
            'savedAnswers'     => $savedAnswers,
            'savedProgress'    => $savedProgress,
            'progressMap'      => $progressMap,
            'saveUrl'          => base_url('paket/' . $token . '/isi/' . $position),
            'autosaveUrl'      => base_url('paket/' . $token . '/isi/' . $position . '/autosave'),
            'summaryUrl'       => base_url('paket/' . $token . '/ringkasan'),
        ]);
    }

    /**
     * POST: save draft answers for one instrument and redirect.
     */
    public function saveInstrument(string $token = '', int $position = 1)
    {
        $bundle = $this->getValidatedBundle($token);

        if (isset($bundle['error_view'])) {
            return $bundle['error_view'];
        }

        $validatorSession = $this->resolveSession($bundle);

        if (!$validatorSession) {
            return redirect()->to(base_url('paket/' . $token));
        }

        // Honeypot
        if (trim((string) $this->request->getPost('website')) !== '') {
            return view('public/thanks', [
                'title'   => 'Pengisian Tidak Dapat Diproses',
                'message' => 'Pengisian tidak dapat diproses karena terdeteksi sebagai aktivitas tidak wajar.',
            ]);
        }

        $instruments     = $this->bundleInstrumentModel->getByBundle((int) $bundle['id']);
        $instrumentEntry = $this->findAtPosition($instruments, $position);

        if (!$instrumentEntry) {
            return redirect()->to(base_url('paket/' . $token));
        }

        $instrumentId = (int) $instrumentEntry['instrument_id'];
        $total        = count($instruments);
        $nextPos      = $position < $total ? $position + 1 : null;

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->findAll();
        $aspectNames = $this->aspectNamesForInstrument($instrumentId);

        $rawAnswers = $this->request->getPost('answers');
        if (!is_array($rawAnswers)) {
            $rawAnswers = [];
        }

        $scale = $this->getScaleRange($instrumentEntry);

        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $answer = $rawAnswers[$itemId] ?? [];
            $tipe   = $item['tipe_butir'] ?? 'skala';

            if ($tipe === 'skala' && isset($answer['skor']) && $answer['skor'] !== '') {
                $score = (int) $answer['skor'];
                if ($score < $scale['min'] || $score > $scale['max']) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Skor butir nomor ' . $item['nomor'] . ' hanya boleh bernilai ' . $scale['min'] . ' sampai ' . $scale['max'] . '.');
                }
            }
        }

        $toSave = [];
        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $answer = $rawAnswers[$itemId] ?? [];
            $tipe   = $item['tipe_butir'] ?? 'skala';

            $toSave[] = [
                'instrument_item_id' => $itemId,
                'snapshot_nomor' => (string) ($item['nomor'] ?? $itemId),
                'snapshot_aspek' => $aspectNames[(int) ($item['aspect_id'] ?? 0)] ?? '-',
                'snapshot_pernyataan' => (string) ($item['pernyataan'] ?? '-'),
                'snapshot_tipe_butir' => (string) $tipe,
                'snapshot_sumber_dokumen' => (string) ($item['sumber_dokumen'] ?? ''),
                'skor'               => ($tipe === 'skala' && isset($answer['skor']) && $answer['skor'] !== '') ? (string) $answer['skor'] : null,
                'jawaban_teks'       => ($tipe !== 'skala') ? trim((string) ($answer['jawaban_teks'] ?? '')) : null,
                'komentar'           => isset($answer['komentar']) ? trim((string) $answer['komentar']) : null,
            ];
        }

        $this->answerModel->saveForSession((int) $validatorSession['id'], $instrumentId, $toSave);

        $savedAnswers = $this->answerModel->getBySessionAndInstrument(
            (int) $validatorSession['id'], $instrumentId
        );
        $status = $this->computeStatus($savedAnswers, $items);

        $progressData = [
            'status'        => $status,
            'kesimpulan'    => trim((string) ($this->request->getPost('kesimpulan') ?? '')) ?: null,
            'komentar_umum' => trim((string) ($this->request->getPost('komentar_umum') ?? '')) ?: null,
        ];

        $this->progressModel->saveProgress((int) $validatorSession['id'], $instrumentId, $progressData);

        $action = (string) ($this->request->getPost('action') ?? 'save');

        if ($action === 'save_next') {
            if ($nextPos !== null) {
                return redirect()
                    ->to(base_url('paket/' . $token . '/isi/' . $nextPos));
            }

            return redirect()
                ->to(base_url('paket/' . $token))
                ->with('success', 'Semua instrumen telah tersimpan. Silakan tinjau progres Anda.');
        }

        return redirect()
            ->to(base_url('paket/' . $token . '/isi/' . $position))
            ->with('success', 'Progres instrumen ' . $position . ' berhasil disimpan.');
    }

    /**
     * POST (AJAX): autosave answers silently, return JSON.
     */
    public function autosave(string $token = '', int $position = 1)
    {
        $bundle = $this->bundleModel->findByToken($token);

        if (!$bundle) {
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode(['ok' => false, 'error' => 'Bundle not found']));
        }

        $validatorSession = $this->resolveSession($bundle);

        if (!$validatorSession) {
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode(['ok' => false, 'error' => 'No session']));
        }

        $instruments     = $this->bundleInstrumentModel->getByBundle((int) $bundle['id']);
        $instrumentEntry = $this->findAtPosition($instruments, $position);

        if (!$instrumentEntry) {
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode(['ok' => false, 'error' => 'Instrument not found']));
        }

        $instrumentId = (int) $instrumentEntry['instrument_id'];

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->findAll();
        $aspectNames = $this->aspectNamesForInstrument($instrumentId);

        $rawAnswers = $this->request->getPost('answers');
        if (!is_array($rawAnswers)) {
            $rawAnswers = [];
        }

        $scale  = $this->getScaleRange($instrumentEntry);
        $toSave = [];

        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $answer = $rawAnswers[$itemId] ?? [];
            $tipe   = $item['tipe_butir'] ?? 'skala';

            if ($tipe === 'skala' && isset($answer['skor']) && $answer['skor'] !== '') {
                $score = (int) $answer['skor'];
                if ($score < $scale['min'] || $score > $scale['max']) {
                    continue; // skip invalid scores silently during autosave
                }
            }

            $toSave[] = [
                'instrument_item_id' => $itemId,
                'snapshot_nomor' => (string) ($item['nomor'] ?? $itemId),
                'snapshot_aspek' => $aspectNames[(int) ($item['aspect_id'] ?? 0)] ?? '-',
                'snapshot_pernyataan' => (string) ($item['pernyataan'] ?? '-'),
                'snapshot_tipe_butir' => (string) $tipe,
                'snapshot_sumber_dokumen' => (string) ($item['sumber_dokumen'] ?? ''),
                'skor'               => ($tipe === 'skala' && isset($answer['skor']) && $answer['skor'] !== '') ? (string) $answer['skor'] : null,
                'jawaban_teks'       => ($tipe !== 'skala') ? trim((string) ($answer['jawaban_teks'] ?? '')) : null,
                'komentar'           => isset($answer['komentar']) ? trim((string) $answer['komentar']) : null,
            ];
        }

        $this->answerModel->saveForSession((int) $validatorSession['id'], $instrumentId, $toSave);

        $savedAnswers = $this->answerModel->getBySessionAndInstrument(
            (int) $validatorSession['id'], $instrumentId
        );
        $status = $this->computeStatus($savedAnswers, $items);

        $progressData = [
            'status'        => $status,
            'kesimpulan'    => trim((string) ($this->request->getPost('kesimpulan') ?? '')) ?: null,
            'komentar_umum' => trim((string) ($this->request->getPost('komentar_umum') ?? '')) ?: null,
        ];

        $this->progressModel->saveProgress((int) $validatorSession['id'], $instrumentId, $progressData);

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode([
                'ok'        => true,
                'status'    => $status,
                'saved_at'  => date('H:i:s'),
                'csrf_name' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]));
    }

    /**
     * GET: show the post-submission summary for the current validator session.
     */
    public function summary(string $token = '')
    {
        $bundle = $this->bundleModel->findByToken($token);

        if (!$bundle) {
            return view('public/thanks', [
                'title'   => 'Paket Tidak Ditemukan',
                'message' => 'Paket validasi tidak ditemukan.',
            ]);
        }

        $validatorSession = $this->resolveSession($bundle);

        if (!$validatorSession) {
            return redirect()->to(base_url('paket/' . $token));
        }

        $instruments = $this->bundleInstrumentModel->getByBundle((int) $bundle['id']);
        $progressMap = $this->progressModel->getBySession((int) $validatorSession['id']);
        $answersGrouped = $this->answerModel->getGroupedByInstrument((int) $validatorSession['id']);

        // Build per-instrument summary data
        $instrumentSummaries = [];
        foreach ($instruments as $idx => $instr) {
            $instrId  = (int) $instr['instrument_id'];
            $prog     = $progressMap[$instrId] ?? null;
            $answers  = $answersGrouped[$instrId] ?? [];

            $skors = array_filter(array_column($answers, 'skor'), fn($s) => $s !== null);
            $avgSkor = !empty($skors) ? round(array_sum($skors) / count($skors), 2) : null;

            $instrumentSummaries[] = [
                'position'      => $idx + 1,
                'instrument'    => $instr,
                'status'        => $prog['status'] ?? 'belum',
                'kesimpulan'    => $prog['kesimpulan'] ?? null,
                'komentar_umum' => $prog['komentar_umum'] ?? null,
                'jumlah_butir'  => count($answers),
                'avg_skor'      => $avgSkor,
            ];
        }

        $selesaiCount = count(array_filter($instrumentSummaries, fn($s) => $s['status'] === 'selesai'));

        return view('public/bundle_summary', [
            'title'                => esc($bundle['judul']),
            'bundle'               => $bundle,
            'validatorSession'     => $validatorSession,
            'instrumentSummaries'  => $instrumentSummaries,
            'selesaiCount'         => $selesaiCount,
            'total'                => count($instruments),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function getValidatedBundle(string $token): array
    {
        if ($token === '') {
            return ['error_view' => redirect()->to(base_url())];
        }

        $bundle = $this->bundleModel->findByToken($token);

        if (!$bundle) {
            $this->auditLog->log(
                AuditLogService::ACTION_BUNDLE_TOKEN_DENIED,
                AuditLogService::ENTITY_BUNDLE_TOKEN,
                null,
                'Akses token tidak valid: ' . $token
            );

            return [
                'error_view' => view('public/thanks', [
                    'title'   => 'Paket Tidak Ditemukan',
                    'message' => 'Paket validasi tidak ditemukan. Periksa kembali alamat link yang Anda gunakan.',
                ]),
            ];
        }

        $errorView = $this->bundleAvailabilityErrorView($bundle);
        if ($errorView !== null) {
            $this->auditLog->log(
                AuditLogService::ACTION_BUNDLE_TOKEN_DENIED,
                AuditLogService::ENTITY_BUNDLE_TOKEN,
                (int) $bundle['id'],
                'Akses token ditolak.'
            );

            return ['error_view' => $errorView];
        }

        return $bundle;
    }

    private function bundleAvailabilityErrorView(array $bundle): ?string
    {
        if ($bundle['status'] !== 'Aktif') {
            return view('public/thanks', [
                'title'   => 'Paket Tidak Aktif',
                'message' => 'Paket validasi ini belum aktif atau sudah ditutup.',
            ]);
        }

        if (!empty($bundle['token_revoked_at'])) {
            return view('public/thanks', [
                'title'   => 'Token Tidak Aktif',
                'message' => 'Token validasi ini sudah dinonaktifkan oleh admin.',
            ]);
        }

        if (!empty($bundle['token_expires_at']) && time() > strtotime((string) $bundle['token_expires_at'])) {
            return view('public/thanks', [
                'title'   => 'Token Kedaluwarsa',
                'message' => 'Token validasi ini sudah melewati masa berlaku.',
            ]);
        }

        $today = date('Y-m-d');

        if (!empty($bundle['tanggal_mulai']) && $today < $bundle['tanggal_mulai']) {
            return view('public/thanks', [
                'title'   => 'Paket Belum Dibuka',
                'message' => 'Paket validasi ini baru dapat digunakan mulai tanggal ' . format_tanggal_indonesia($bundle['tanggal_mulai']) . '.',
            ]);
        }

        if (!empty($bundle['tanggal_selesai']) && $today > $bundle['tanggal_selesai']) {
            return view('public/thanks', [
                'title'   => 'Paket Sudah Ditutup',
                'message' => 'Masa pengisian paket ini sudah berakhir pada tanggal ' . format_tanggal_indonesia($bundle['tanggal_selesai']) . '.',
            ]);
        }

        return null;
    }

    private function resolveSession(array $bundle): ?array
    {
        $bundleId = (int) ($bundle['id'] ?? 0);
        if ($bundleId <= 0) {
            return null;
        }

        $sessionId = session()->get($this->sessionKey($bundleId));

        if ($sessionId) {
            $validatorSession = $this->sessionModel->find((int) $sessionId);

            if ($validatorSession && (int) $validatorSession['bundle_id'] === $bundleId) {
                return $validatorSession;
            }

            session()->remove($this->sessionKey($bundleId));
        }

        // Token-based continuation for one-validator bundles.
        if (($bundle['token_access_mode'] ?? 'single_use') !== 'single_use') {
            return null;
        }

        $validatorSession = $this->sessionModel
            ->where('bundle_id', $bundleId)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$validatorSession) {
            return null;
        }

        session()->set($this->sessionKey($bundleId), (int) $validatorSession['id']);

        return $validatorSession;
    }

    private function sessionKey(int $bundleId): string
    {
        return 'bvs_' . $bundleId;
    }

    private function findAtPosition(array $instruments, int $position): ?array
    {
        foreach ($instruments as $idx => $instr) {
            if ($idx + 1 === $position) {
                return $instr;
            }
        }

        return null;
    }

    private function getScaleRange(array $instrument): array
    {
        $min = isset($instrument['skala_min']) ? (int) $instrument['skala_min'] : 1;
        $max = isset($instrument['skala_max']) ? (int) $instrument['skala_max'] : 4;

        if ($min <= 0) {
            $min = 1;
        }

        if ($max < $min) {
            $max = $min;
        }

        return [
            'min'   => $min,
            'max'   => $max,
            'range' => range($min, $max),
            'options' => sivalid_scale_options(['skala_min' => $min, 'skala_max' => $max] + $instrument),
            'labels' => sivalid_scale_labels(['skala_min' => $min, 'skala_max' => $max] + $instrument),
        ];
    }

    private function aspectNamesForInstrument(int $instrumentId): array
    {
        $rows = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->findAll();

        $names = [];

        foreach ($rows as $row) {
            $names[(int) ($row['id'] ?? 0)] = (string) ($row['nama_aspek'] ?? '-');
        }

        return $names;
    }

    private function syncSessionProgressStatuses(int $sessionId, array $instruments): array
    {
        $progressMap = $this->progressModel->getBySession($sessionId);
        $hasChanges = false;

        foreach ($instruments as $instrument) {
            $instrumentId = (int) ($instrument['instrument_id'] ?? 0);
            if ($instrumentId <= 0) {
                continue;
            }

            $items = $this->itemModel
                ->where('instrument_id', $instrumentId)
                ->whereIn('status', $this->itemModel->usableStatuses())
                ->findAll();

            $savedAnswers = $this->answerModel->getBySessionAndInstrument($sessionId, $instrumentId);
            $status = $this->computeStatus($savedAnswers, $items);

            $existing = $progressMap[$instrumentId] ?? null;
            $existingStatus = $existing['status'] ?? 'belum';

            if ($existing && $existingStatus !== $status) {
                $this->progressModel->saveProgress($sessionId, $instrumentId, [
                    'status'        => $status,
                    'kesimpulan'    => $existing['kesimpulan'] ?? null,
                    'komentar_umum' => $existing['komentar_umum'] ?? null,
                ]);
                $hasChanges = true;
            }
        }

        return $hasChanges ? $this->progressModel->getBySession($sessionId) : $progressMap;
    }

    /**
     * Compute per-instrument completion status based on saved answers vs required items.
     * Returns 'belum' | 'proses' | 'selesai'.
     */
    private function computeStatus(array $savedAnswersByItemId, array $items): string
    {
        if (empty($items)) {
            return 'selesai';
        }

        if (empty($savedAnswersByItemId)) {
            return 'belum';
        }
        $completedCount = 0;
        foreach ($items as $item) {
            $answer = $savedAnswersByItemId[(int) $item['id']] ?? null;

            if (!$answer) {
                continue;
            }

            $tipe = $item['tipe_butir'] ?? 'skala';

            $scoreRaw = $answer['skor'] ?? null;
            $scoreInt = is_numeric($scoreRaw) ? (int) $scoreRaw : null;
            $hasSkor = $scoreInt !== null && $scoreInt > 0;
            $hasJawabanTeks = array_key_exists('jawaban_teks', $answer)
                && trim((string) $answer['jawaban_teks']) !== '';

            if ($tipe === 'skala' && $hasSkor) {
                $completedCount++;
            } elseif ($tipe !== 'skala' && $hasJawabanTeks) {
                $completedCount++;
            }
        }

        if ($completedCount === 0) {
            return 'belum';
        }

        return $completedCount >= count($items) ? 'selesai' : 'proses';
    }
}
