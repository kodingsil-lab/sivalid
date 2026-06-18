<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\JustificationSchema;
use App\Libraries\RespondentIdentitySchema;
use App\Libraries\WorkflowStatusService;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ManualValidInstrumentModel;
use App\Models\ResponseModel;

class RespondentLinks extends BaseController
{
    private const GENERAL_RESPONDENT_MODE = 'respon_mahasiswa';

    protected InstrumentLinkModel $linkModel;
    protected InstrumentModel $instrumentModel;
    protected ManualValidInstrumentModel $manualValidInstrumentModel;
    protected ResponseModel $responseModel;
    protected WorkflowStatusService $workflowStatusService;

    protected array $respondentModes = [
        'respon_mahasiswa',
        'observasi',
        'fgd',
        'tes_kinerja',
    ];

    public function __construct()
    {
        $this->linkModel = new InstrumentLinkModel();
        $this->instrumentModel = new InstrumentModel();
        $this->manualValidInstrumentModel = new ManualValidInstrumentModel();
        $this->responseModel = new ResponseModel();
        $this->workflowStatusService = new WorkflowStatusService();
    }

    public function index()
    {
        $perPage = config('Pager')->perPage;

        $links = $this->linkModel
            ->select(
                'instrument_links.*,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 instruments.status AS instrument_status'
            )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->whereIn('instrument_links.mode', $this->respondentModes)
            ->orderBy('instrument_links.id', 'DESC')
            ->paginate($perPage, 'respondent_links');

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
        }

        unset($link);

        return view('admin/respondent_links/index', [
            'title'      => 'Link Penyebaran Instrumen',
            'links'      => $links,
            'pager'      => $this->linkModel->pager,
            'pagerGroup' => 'respondent_links',
        ]);
    }

    public function new()
    {
        return view('admin/respondent_links/form', [
            'title'       => 'Buat Link Penyebaran Instrumen',
            'link'        => [
                'mode' => self::GENERAL_RESPONDENT_MODE,
                'identity_template' => 'mahasiswa',
            ],
            'instruments' => $this->getValidInstruments(),
            'identityTemplates' => RespondentIdentitySchema::templates(),
            'identityFields' => RespondentIdentitySchema::fieldsForTemplate('mahasiswa'),
            'justificationTemplates' => JustificationSchema::templates(),
            'justificationConfig' => ['template' => 'responden'] + JustificationSchema::configForTemplate('responden'),
            'action'      => base_url('admin/respondent-links'),
            'method'      => 'post',
        ]);
    }

    public function create()
    {
        if (!$this->validate($this->rules())) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen tidak ditemukan.');
        }

        if (!$this->isManualValidInstrument($instrumentId)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen yang disebarkan harus ada di daftar Instrumen Valid.');
        }

        $token = $this->generateUniqueToken();

        $identityTemplate = $this->identityTemplateFromRequest();
        $identityFields = $this->identityFieldsFromRequest($identityTemplate);
        $justificationConfig = $this->justificationConfigFromRequest();

        $this->linkModel->insert([
            'instrument_id'   => $instrumentId,
            'product_id'      => null,
            'token'           => $token,
            'mode'            => self::GENERAL_RESPONDENT_MODE,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'identity_template' => $identityTemplate,
            'identity_fields'  => json_encode($identityFields, JSON_UNESCAPED_UNICODE),
            'justification_config' => json_encode($justificationConfig, JSON_UNESCAPED_UNICODE),
            'pengantar_penyebaran' => trim((string) $this->request->getPost('pengantar_penyebaran')),
            'petunjuk_penyebaran' => trim((string) $this->request->getPost('petunjuk_penyebaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        $this->workflowStatusService->markInstrumentReadyToShare($instrumentId);

        return redirect()
            ->to(base_url('admin/respondent-links'))
            ->with('success', 'Link penyebaran instrumen berhasil dibuat.');
    }

    public function edit($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || !in_array($link['mode'], $this->respondentModes, true)) {
            return redirect()
                ->to(base_url('admin/respondent-links'))
                ->with('error', 'Link penyebaran instrumen tidak ditemukan.');
        }

        if (empty($link['identity_template'])) {
            $link['identity_template'] = RespondentIdentitySchema::defaultTemplateForLink($link);
        }

        return view('admin/respondent_links/form', [
            'title'       => 'Edit Link Penyebaran Instrumen',
            'link'        => $link,
            'instruments' => $this->getValidInstruments(),
            'identityTemplates' => RespondentIdentitySchema::templates(),
            'identityFields' => RespondentIdentitySchema::fieldsForLink($link),
            'justificationTemplates' => JustificationSchema::templates(),
            'justificationConfig' => ['template' => JustificationSchema::defaultTemplateForLink($link)] + JustificationSchema::configForLink($link),
            'action'      => base_url('admin/respondent-links/' . $id),
            'method'      => 'put',
        ]);
    }

    public function update($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || !in_array($link['mode'], $this->respondentModes, true)) {
            return redirect()
                ->to(base_url('admin/respondent-links'))
                ->with('error', 'Link penyebaran instrumen tidak ditemukan.');
        }

        if (!$this->validate($this->rules())) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $instrumentId = (int) $this->request->getPost('instrument_id');
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument || !$this->isManualValidInstrument($instrumentId)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Instrumen harus ada di daftar Instrumen Valid.');
        }

        $identityTemplate = $this->identityTemplateFromRequest();
        $identityFields = $this->identityFieldsFromRequest($identityTemplate);
        $justificationConfig = $this->justificationConfigFromRequest();

        $this->linkModel->update($id, [
            'instrument_id'   => $instrumentId,
            'mode'            => self::GENERAL_RESPONDENT_MODE,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'identity_template' => $identityTemplate,
            'identity_fields'  => json_encode($identityFields, JSON_UNESCAPED_UNICODE),
            'justification_config' => json_encode($justificationConfig, JSON_UNESCAPED_UNICODE),
            'pengantar_penyebaran' => trim((string) $this->request->getPost('pengantar_penyebaran')),
            'petunjuk_penyebaran' => trim((string) $this->request->getPost('petunjuk_penyebaran')),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        return redirect()
            ->to(base_url('admin/respondent-links'))
            ->with('success', 'Link penyebaran instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link || !in_array($link['mode'], $this->respondentModes, true)) {
            return redirect()
                ->to(base_url('admin/respondent-links'))
                ->with('error', 'Link penyebaran instrumen tidak ditemukan.');
        }

        $this->linkModel->delete($id);

        return redirect()
            ->to(base_url('admin/respondent-links'))
            ->with('success', 'Link penyebaran instrumen berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'instrument_id'   => 'required|integer',
            'judul_link'      => 'required|min_length[3]|max_length[255]',
            'sasaran'         => 'permit_empty|max_length[150]',
            'identity_template' => 'permit_empty|max_length[50]',
            'pengantar_penyebaran' => 'permit_empty',
            'petunjuk_penyebaran' => 'permit_empty',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'status'          => 'required',
            'maksimal_respon' => 'permit_empty|integer',
        ];
    }

    private function getValidInstruments(): array
    {
        return $this->manualValidInstrumentModel
            ->select('instruments.*')
            ->join('instruments', 'instruments.id = manual_valid_instruments.instrument_id')
            ->orderBy('instruments.judul', 'ASC')
            ->findAll();
    }

    private function isManualValidInstrument(int $instrumentId): bool
    {
        return $this->manualValidInstrumentModel
            ->where('instrument_id', $instrumentId)
            ->first() !== null;
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
            $exists = $this->linkModel->where('token', $token)->first();
        } while ($exists);

        return $token;
    }

    private function emptyToNull($value)
    {
        return $value === '' || $value === null ? null : $value;
    }

    private function identityTemplateFromRequest(): string
    {
        $template = trim((string) $this->request->getPost('identity_template'));
        $templates = RespondentIdentitySchema::templates();

        return isset($templates[$template]) ? $template : 'umum';
    }

    private function identityFieldsFromRequest(string $template): array
    {
        $keys = $this->request->getPost('identity_field_key');
        $labels = $this->request->getPost('identity_field_label');
        $types = $this->request->getPost('identity_field_type');
        $required = $this->request->getPost('identity_field_required');

        if (!is_array($keys) || !is_array($labels)) {
            return RespondentIdentitySchema::fieldsForTemplate($template);
        }

        $fields = [];

        foreach ($keys as $index => $key) {
            $fields[] = [
                'key' => $key,
                'label' => $labels[$index] ?? '',
                'type' => is_array($types) ? ($types[$index] ?? 'text') : 'text',
                'required' => is_array($required) && isset($required[$index]),
            ];
        }

        return RespondentIdentitySchema::normalizeFields($fields);
    }

    private function justificationConfigFromRequest(): array
    {
        $template = trim((string) $this->request->getPost('justification_template'));
        $base = JustificationSchema::configForTemplate($template);

        return JustificationSchema::normalizeConfig([
            'label' => $base['label'] ?? 'Custom',
            'template' => $template,
            'comment_label' => $this->request->getPost('justification_comment_label'),
            'comment_placeholder' => $this->request->getPost('justification_comment_placeholder'),
            'comment_required' => (bool) $this->request->getPost('justification_comment_required'),
            'conclusion_label' => $this->request->getPost('justification_conclusion_label'),
            'conclusion_required' => (bool) $this->request->getPost('justification_conclusion_required'),
            'conclusion_options' => $this->request->getPost('justification_conclusion_options'),
        ]);
    }
}
