<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\JustificationSchema;
use App\Libraries\RespondentIdentitySchema;
use App\Models\InstrumentAspectModel;
use App\Models\InstrumentIndicatorModel;
use App\Models\InstrumentItemModel;
use App\Models\InstrumentLinkModel;
use App\Models\ResearchProductModel;
use App\Models\RespondentModel;
use App\Models\ResponseAnswerModel;
use App\Models\ResponseModel;

class PublicForm extends BaseController
{
    protected InstrumentLinkModel $linkModel;
    protected InstrumentAspectModel $aspectModel;
    protected InstrumentIndicatorModel $indicatorModel;
    protected InstrumentItemModel $itemModel;
    protected ResearchProductModel $productModel;
    protected RespondentModel $respondentModel;
    protected ResponseModel $responseModel;
    protected ResponseAnswerModel $answerModel;

    public function __construct()
    {
        $this->linkModel       = new InstrumentLinkModel();
        $this->aspectModel     = new InstrumentAspectModel();
        $this->indicatorModel  = new InstrumentIndicatorModel();
        $this->itemModel       = new InstrumentItemModel();
        $this->productModel    = new ResearchProductModel();
        $this->respondentModel = new RespondentModel();
        $this->responseModel   = new ResponseModel();
        $this->answerModel     = new ResponseAnswerModel();
    }

    public function show($token = null)
    {
        if (!$token) {
            return redirect()->to(base_url());
        }

        $link = $this->getValidatedLink((string) $token);

        if (isset($link['error_view'])) {
            return $link['error_view'];
        }

        if (!$this->getRespondentIdentity($link) || $this->request->getGet('identitas') === 'edit') {
            return $this->showIdentityForm($link);
        }

        if ($link['mode'] === 'validasi_instrumen') {
            return $this->showValidasiInstrumen($link);
        }

        if ($link['mode'] === 'validasi_produk') {
            return $this->showValidasiProduk($link);
        }

        if ($link['mode'] === 'respon_mahasiswa') {
            return $this->showResponMahasiswa($link);
        }

        if ($link['mode'] === 'observasi') {
            return $this->showObservasi($link);
        }

        if ($link['mode'] === 'fgd') {
            return $this->showFgd($link);
        }

        if ($link['mode'] === 'tes_kinerja') {
            return $this->showTesKinerja($link);
        }

        return view('public/thanks', [
            'title'   => 'Mode Belum Tersedia',
            'message' => 'Mode pengisian ini belum tersedia pada tahap sekarang.',
        ]);
    }

    private function showValidasiInstrumen(array $link)
    {
        $instrumentId = (int) $link['instrument_id'];

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

        $scale = $this->getScaleRange($link);

        $data = [
            'title'      => 'Validasi Instrumen',
            'link'       => $link,
            'aspects'    => $aspects,
            'indicators' => $indicators,
            'items'      => $items,
            'scale'      => $scale,
            'respondentIdentity' => $this->getRespondentIdentity($link),
            'identityFields' => RespondentIdentitySchema::fieldsForLink($link),
            'justificationConfig' => JustificationSchema::configForLink($link),
        ];

        return view('public/validasi_instrumen', $data);
    }

    private function showValidasiProduk(array $link)
    {
        $instrumentId = (int) $link['instrument_id'];

        if (empty($link['product_id'])) {
            return view('public/thanks', [
                'title'   => 'Produk Tidak Tersedia',
                'message' => 'Link validasi produk ini belum memiliki produk yang divalidasi.',
            ]);
        }

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->orderBy('urutan', 'ASC')
            ->orderBy('nomor', 'ASC')
            ->findAll();

        $aspects = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $scale = $this->getScaleRange($link);

        $data = [
            'title'   => 'Validasi Produk',
            'link'    => $link,
            'aspects' => $aspects,
            'items'   => $items,
            'scale'   => $scale,
            'respondentIdentity' => $this->getRespondentIdentity($link),
            'identityFields' => RespondentIdentitySchema::fieldsForLink($link),
            'justificationConfig' => JustificationSchema::configForLink($link),
        ];

        return view('public/validasi_produk', $data);
    }

    private function showResponMahasiswa(array $link)
    {
        return $this->showGenericRespondentForm($link, 'public/pengisian_instrumen', 'Pengisian Instrumen');
    }

    private function showObservasi(array $link)
    {
        return $this->showGenericRespondentForm($link, 'public/observasi', 'Observasi');
    }

    private function showFgd(array $link)
    {
        return $this->showGenericRespondentForm($link, 'public/fgd', 'FGD');
    }

    private function showTesKinerja(array $link)
    {
        return $this->showGenericRespondentForm($link, 'public/tes_kinerja', 'Tes Kinerja');
    }

    private function showGenericRespondentForm(array $link, string $view, string $title)
    {
        $instrumentId = (int) $link['instrument_id'];

        $aspects = $this->aspectModel
            ->where('instrument_id', $instrumentId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->orderBy('urutan', 'ASC')
            ->orderBy('nomor', 'ASC')
            ->findAll();

        $scale = $this->getScaleRange($link);

        return view($view, [
            'title'   => $title,
            'link'    => $link,
            'aspects' => $aspects,
            'items'   => $items,
            'scale'   => $scale,
            'respondentIdentity' => $this->getRespondentIdentity($link),
            'identityFields' => RespondentIdentitySchema::fieldsForLink($link),
            'justificationConfig' => JustificationSchema::configForLink($link),
        ]);
    }

    public function submit($token = null)
    {
        if (!$token) {
            return redirect()->to(base_url());
        }

        $link = $this->getValidatedLink((string) $token);

        if (isset($link['error_view'])) {
            return $link['error_view'];
        }

        $website = trim((string) $this->request->getPost('website'));

        if ($website !== '') {
            return view('public/thanks', [
                'title'   => 'Pengisian Tidak Dapat Diproses',
                'message' => 'Pengisian tidak dapat diproses karena terdeteksi sebagai aktivitas tidak wajar.',
            ]);
        }

        if ((string) $this->request->getPost('action') === 'start_identity') {
            return $this->startIdentity($link);
        }

        $allowedSubmitModes = [
            'validasi_instrumen',
            'validasi_produk',
            'respon_mahasiswa',
            'observasi',
            'fgd',
            'tes_kinerja',
        ];

        if (!in_array($link['mode'], $allowedSubmitModes, true)) {
            return view('public/thanks', [
                'title'   => 'Mode Belum Tersedia',
                'message' => 'Mode pengisian ini belum dapat disimpan pada tahap sekarang.',
            ]);
        }

        if (!$this->getRespondentIdentity($link)) {
            return redirect()
                ->to(base_url('isi/' . $link['token']))
                ->with('error', 'Silakan lengkapi identitas terlebih dahulu sebelum mengisi instrumen.');
        }

        $identityFields = RespondentIdentitySchema::fieldsForLink($link);
        $justificationConfig = JustificationSchema::configForLink($link);
        $rules = $this->identityValidationRules($identityFields) + [
            'komentar_umum'    => !empty($justificationConfig['comment_required']) ? 'required' : 'permit_empty',
            'kesimpulan'       => !empty($justificationConfig['conclusion_required']) ? 'required|max_length[150]' : 'permit_empty|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $identity = $this->identityFromRequest($identityFields);
        $justificationData = $this->justificationFromRequest($justificationConfig);
        $email = $identity['email'];
        $nim = $identity['nim'];

        if ($email !== '' && $this->respondentModel->hasSubmittedByEmail((int) $link['id'], $email)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email ini sudah pernah digunakan untuk mengisi link ini.');
        }

        if (
            in_array($link['mode'], ['respon_mahasiswa', 'tes_kinerja'], true)
            && $nim !== ''
            && $this->respondentModel->hasSubmittedByNim((int) $link['id'], $nim)
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'NIM/Nomor identitas ini sudah pernah digunakan untuk mengisi link ini.');
        }

        $answers = $this->request->getPost('answers');

        if (!is_array($answers)) {
            $answers = [];
        }

        $instrumentId = (int) $link['instrument_id'];
        $scale = $this->getScaleRange($link);
        $skalaMin = $scale['min'];
        $skalaMax = $scale['max'];

        $items = $this->itemModel
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', $this->itemModel->usableStatuses())
            ->findAll();

        if (empty($items)) {
            return redirect()
                ->back()
                ->with('error', 'Butir instrumen yang dapat digunakan belum tersedia.');
        }

        foreach ($items as $item) {
            $itemId    = (int) $item['id'];
            $tipeButir = $item['tipe_butir'] ?? 'skala';
            $wajib     = (int) ($item['wajib'] ?? 1) === 1;

            $answer = $answers[$itemId] ?? [];

            if (!is_array($answer)) {
                $answer = [];
            }

            if ($tipeButir === 'skala') {
                if ($wajib && !isset($answer['skor'])) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Butir nomor ' . $item['nomor'] . ' wajib diberi skor.');
                }

                if (isset($answer['skor']) && $answer['skor'] !== '') {
                    $score = (int) $answer['skor'];

                    if ($score < $skalaMin || $score > $skalaMax) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', 'Skor butir nomor ' . $item['nomor'] . ' hanya boleh bernilai ' . $skalaMin . ' sampai ' . $skalaMax . '.');
                    }
                }

                continue;
            }

            $jawabanTeks = trim((string) ($answer['jawaban_teks'] ?? ''));

            if ($wajib && $jawabanTeks === '') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Butir nomor ' . $item['nomor'] . ' wajib diisi.');
            }
        }

        $db = db_connect();
        $db->transBegin();

        $lockedLink = $this->lockLinkForSubmit($db, (int) $link['id']);

        if (!$lockedLink) {
            $db->transRollback();

            return view('public/thanks', [
                'title'   => 'Link Tidak Ditemukan',
                'message' => 'Link pengisian tidak ditemukan atau sudah tidak tersedia.',
            ]);
        }

        $linkErrorView = $this->linkAvailabilityErrorView($lockedLink);

        if ($linkErrorView !== null) {
            $db->transRollback();

            return $linkErrorView;
        }

        if (!empty($lockedLink['maksimal_respon'])) {
            $totalResponse = $this->responseModel->countByLink((int) $link['id']);

            if ($totalResponse >= (int) $lockedLink['maksimal_respon']) {
                $db->transRollback();

                return view('public/thanks', [
                    'title'   => 'Kuota Pengisian Penuh',
                    'message' => 'Jumlah maksimal respon untuk link ini sudah terpenuhi. Pengisian tidak dapat dilanjutkan.',
                ]);
            }
        }

        if ($email !== '' && $this->respondentModel->hasSubmittedByEmail((int) $link['id'], $email)) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email ini sudah pernah digunakan untuk mengisi link ini.');
        }

        if (
            in_array($link['mode'], ['respon_mahasiswa', 'tes_kinerja'], true)
            && $nim !== ''
            && $this->respondentModel->hasSubmittedByNim((int) $link['id'], $nim)
        ) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'NIM/Nomor identitas ini sudah pernah digunakan untuk mengisi link ini.');
        }

        $now = date('Y-m-d H:i:s');
        $respondentId = $this->respondentModel->insert([
            'instrument_link_id' => (int) $link['id'],
            'nama'               => $identity['nama'],
            'email'              => $identity['email'],
            'bidang_keahlian'    => $identity['bidang_keahlian'],
            'instansi'           => $identity['instansi'],
            'jenis_responden'    => $this->jenisRespondenFromMode($link['mode']),
            'nim'                => $identity['nim'],
            'program_studi'      => $identity['program_studi'],
            'semester'           => $identity['semester'],
            'kelas'              => $identity['kelas'],
            'identity_data'       => json_encode($identity, JSON_UNESCAPED_UNICODE),
            'tanggal_isi'        => $now,
        ], true);

        $responseId = $this->responseModel->insert([
            'instrument_id'      => $instrumentId,
            'instrument_link_id' => (int) $link['id'],
            'product_id'         => !empty($link['product_id']) ? (int) $link['product_id'] : null,
            'respondent_id'      => (int) $respondentId,
            'mode'               => $link['mode'],
            'status'             => 'Terkirim',
            'komentar_umum'      => $justificationData['komentar_umum'],
            'kesimpulan'         => $justificationData['kesimpulan'],
            'justification_data' => json_encode($justificationData, JSON_UNESCAPED_UNICODE),
            'submitted_at'       => $now,
        ], true);

        foreach ($items as $item) {
            $itemId    = (int) $item['id'];
            $tipeButir = $item['tipe_butir'] ?? 'skala';

            $answer = $answers[$itemId] ?? [];

            if (!is_array($answer)) {
                $answer = [];
            }

            $score = null;
            $jawabanTeks = null;

            if ($tipeButir === 'skala') {
                if (isset($answer['skor']) && $answer['skor'] !== '') {
                    $score = (int) $answer['skor'];
                }
            } else {
                $jawabanTeks = trim((string) ($answer['jawaban_teks'] ?? ''));
            }

            $comment = $answer['komentar'] ?? null;

            $this->answerModel->insert([
                'response_id'         => (int) $responseId,
                'instrument_item_id'  => $itemId,
                'skor'                => $score,
                'jawaban_teks'        => $jawabanTeks,
                'komentar'            => is_string($comment) ? trim($comment) : null,
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Data gagal disimpan. Silakan coba lagi.');
        }

        $db->transCommit();
        session()->remove($this->identitySessionKey((int) $link['id']));

        $message = $link['mode'] === 'validasi_produk'
            ? 'Hasil validasi produk berhasil dikirim. Terima kasih atas penilaian dan masukan Bapak/Ibu Validator.'
            : 'Hasil validasi instrumen berhasil dikirim. Terima kasih atas penilaian dan masukan Bapak/Ibu Validator.';

        return view('public/thanks', [
            'title'   => 'Terima Kasih',
            'message' => $message,
        ]);
    }

    public function thanks()
    {
        return view('public/thanks', [
            'title'   => 'Terima Kasih',
            'message' => 'Terima kasih. Data berhasil diproses.',
        ]);
    }

    private function jenisRespondenFromMode(string $mode): string
    {
        if ($mode === 'validasi_produk') {
            return 'validator_produk';
        }

        if ($mode === 'respon_mahasiswa') {
            return 'responden';
        }

        if ($mode === 'observasi') {
            return 'observer';
        }

        if ($mode === 'fgd') {
            return 'peserta_fgd';
        }

        if ($mode === 'tes_kinerja') {
            return 'penilai_kinerja';
        }

        return 'validator_instrumen';
    }

    private function getScaleRange(array $link): array
    {
        $min = isset($link['skala_min']) ? (int) $link['skala_min'] : 1;
        $max = isset($link['skala_max']) ? (int) $link['skala_max'] : 4;

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
        ];
    }

    private function lockLinkForSubmit($db, int $linkId): ?array
    {
        return $db->query(
            'SELECT id, status, tanggal_mulai, tanggal_selesai, maksimal_respon FROM instrument_links WHERE id = ? FOR UPDATE',
            [$linkId]
        )->getRowArray();
    }

    private function linkAvailabilityErrorView(array $link): ?string
    {
        if ($link['status'] !== 'Aktif') {
            return view('public/thanks', [
                'title'   => 'Link Tidak Aktif',
                'message' => 'Link pengisian ini belum aktif atau sudah ditutup oleh admin.',
            ]);
        }

        $today = date('Y-m-d');

        if (!empty($link['tanggal_mulai']) && $today < $link['tanggal_mulai']) {
            return view('public/thanks', [
                'title'   => 'Link Belum Dibuka',
                'message' => 'Link pengisian ini baru dapat digunakan mulai tanggal ' . format_tanggal_indonesia($link['tanggal_mulai']) . '.',
            ]);
        }

        if (!empty($link['tanggal_selesai']) && $today > $link['tanggal_selesai']) {
            return view('public/thanks', [
                'title'   => 'Link Sudah Ditutup',
                'message' => 'Masa pengisian link ini sudah berakhir pada tanggal ' . format_tanggal_indonesia($link['tanggal_selesai']) . '.',
            ]);
        }

        return null;
    }

    private function getValidatedLink(string $token): array
    {
        $link = $this->linkModel->findByToken($token);

        if (!$link) {
            return [
                'error_view' => view('public/thanks', [
                    'title'   => 'Link Tidak Ditemukan',
                    'message' => 'Link pengisian tidak ditemukan. Periksa kembali alamat link yang Bapak/Ibu gunakan.',
                ]),
            ];
        }

        $errorView = $this->linkAvailabilityErrorView($link);

        if ($errorView !== null) {
            return [
                'error_view' => $errorView,
            ];
        }

        if (!empty($link['maksimal_respon'])) {
            $totalResponse = $this->responseModel->countByLink((int) $link['id']);

            if ($totalResponse >= (int) $link['maksimal_respon']) {
                return [
                    'error_view' => view('public/thanks', [
                        'title'   => 'Kuota Pengisian Penuh',
                        'message' => 'Jumlah maksimal respon untuk link ini sudah terpenuhi. Pengisian tidak dapat dilanjutkan.',
                    ]),
                ];
            }
        }

        return $link;
    }

    private function showIdentityForm(array $link)
    {
        return view('public/respondent_identity', [
            'title' => $this->identityTitle($link['mode']),
            'link' => $link,
            'respondentIdentity' => $this->getRespondentIdentity($link),
            'identityFields' => RespondentIdentitySchema::fieldsForLink($link),
        ]);
    }

    private function startIdentity(array $link)
    {
        $identityFields = RespondentIdentitySchema::fieldsForLink($link);
        $rules = $this->identityValidationRules($identityFields);

        $messages = [
            'nama' => [
                'required'   => 'Mohon isi nama lengkap terlebih dahulu.',
                'min_length' => 'Nama lengkap minimal 3 karakter.',
                'max_length' => 'Nama lengkap maksimal 150 karakter.',
            ],
            'email' => [
                'valid_email' => 'Mohon isi alamat email dengan format yang benar.',
                'max_length'  => 'Alamat email maksimal 150 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $identity = $this->identityFromRequest($identityFields);
        $email = $identity['email'];
        $nim = $identity['nim'];

        if ($email !== '' && $this->respondentModel->hasSubmittedByEmail((int) $link['id'], $email)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email ini sudah pernah digunakan untuk mengisi link ini.');
        }

        if (
            in_array($link['mode'], ['respon_mahasiswa', 'tes_kinerja'], true)
            && $nim !== ''
            && $this->respondentModel->hasSubmittedByNim((int) $link['id'], $nim)
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'NIM/Nomor identitas ini sudah pernah digunakan untuk mengisi link ini.');
        }

        session()->set($this->identitySessionKey((int) $link['id']), $identity);

        return redirect()->to(base_url('isi/' . $link['token']));
    }

    private function identityFromRequest(array $fields): array
    {
        $identity = [];

        foreach ($fields as $field) {
            $key = (string) ($field['key'] ?? '');

            if ($key === '') {
                continue;
            }

            $identity[$key] = trim((string) $this->request->getPost($key));
        }

        foreach (RespondentIdentitySchema::knownKeys() as $key) {
            $identity[$key] = trim((string) ($identity[$key] ?? ''));
        }

        return $identity;
    }

    private function getRespondentIdentity(array $link): ?array
    {
        $identity = session()->get($this->identitySessionKey((int) $link['id']));

        return is_array($identity) && trim((string) ($identity['nama'] ?? '')) !== ''
            ? $identity
            : null;
    }

    private function identitySessionKey(int $linkId): string
    {
        return 'public_form_identity_' . $linkId;
    }

    private function identityTitle(string $mode): string
    {
        if ($mode === 'validasi_produk' || $mode === 'validasi_instrumen') {
            return 'Identitas Validator';
        }

        if ($mode === 'observasi') {
            return 'Identitas Observer';
        }

        if ($mode === 'fgd') {
            return 'Identitas Peserta FGD';
        }

        if ($mode === 'tes_kinerja') {
            return 'Identitas Peserta/Mahasiswa';
        }

        return 'Identitas Responden';
    }

    private function identityValidationRules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = (string) ($field['key'] ?? '');

            if ($key === '') {
                continue;
            }

            $ruleParts = !empty($field['required']) ? ['required'] : ['permit_empty'];
            $type = (string) ($field['type'] ?? 'text');

            if ($type === 'email') {
                $ruleParts[] = 'valid_email';
            }

            if ($type === 'textarea') {
                $ruleParts[] = 'max_length[1000]';
            } else {
                $ruleParts[] = in_array($key, ['semester', 'kelas', 'nim'], true) ? 'max_length[50]' : 'max_length[150]';
            }

            if ($key === 'nama') {
                $ruleParts[] = 'min_length[3]';
            }

            $rules[$key] = implode('|', $ruleParts);
        }

        if (!isset($rules['nama'])) {
            $rules['nama'] = 'required|min_length[3]|max_length[150]';
        }

        return $rules;
    }

    private function justificationFromRequest(array $config): array
    {
        return [
            'comment_label' => (string) ($config['comment_label'] ?? 'Komentar/Saran'),
            'conclusion_label' => (string) ($config['conclusion_label'] ?? 'Kesimpulan'),
            'komentar_umum' => trim((string) $this->request->getPost('komentar_umum')),
            'kesimpulan' => trim((string) $this->request->getPost('kesimpulan')),
        ];
    }
}
