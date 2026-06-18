<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\JustificationSchema;
use App\Libraries\RespondentIdentitySchema;
use App\Libraries\WorkflowStatusService;
use App\Models\InstrumentLinkModel;
use App\Models\InstrumentModel;
use App\Models\ResponseModel;

class InstrumentLinks extends BaseController
{
    protected InstrumentLinkModel $linkModel;
    protected InstrumentModel $instrumentModel;
    protected ResponseModel $responseModel;
    protected WorkflowStatusService $workflowStatusService;

    public function __construct()
    {
        $this->linkModel       = new InstrumentLinkModel();
        $this->instrumentModel = new InstrumentModel();
        $this->responseModel   = new ResponseModel();
        $this->workflowStatusService = new WorkflowStatusService();
    }

    public function index()
    {
        $perPage = config('Pager')->perPage;
        $links = $this->linkModel->paginateWithInstrument('validasi_instrumen', $perPage, 'instrument_links');

        foreach ($links as &$link) {
            $link['jumlah_respon'] = $this->responseModel->countByLink((int) $link['id']);
        }

        unset($link);

        $data = [
            'title' => 'Link Validasi Instrumen',
            'links' => $links,
            'pager' => $this->linkModel->pager,
            'pagerGroup' => 'instrument_links',
        ];

        return view('admin/links/index', $data);
    }

    public function new()
    {
        $data = [
            'title'       => 'Buat Link Validasi Instrumen',
            'link'        => [
                'instrument_id' => $this->request->getGet('instrument_id'),
                'identity_template' => 'validator',
            ],
            'instruments' => $this->instrumentModel
                ->orderBy('judul', 'ASC')
                ->findAll(),
            'identityTemplates' => RespondentIdentitySchema::templates(),
            'identityFields' => RespondentIdentitySchema::fieldsForTemplate('validator'),
            'justificationTemplates' => JustificationSchema::templates(),
            'justificationConfig' => ['template' => 'validasi_instrumen'] + JustificationSchema::configForTemplate('validasi_instrumen'),
            'action'      => base_url('admin/instrument-links'),
            'method'      => 'post',
        ];

        return view('admin/links/form', $data);
    }

    public function create()
    {
        $rules = [
            'instrument_id'   => 'required|integer',
            'judul_link'      => 'required|min_length[3]|max_length[255]',
            'sasaran'         => 'permit_empty|max_length[150]',
            'identity_template' => 'permit_empty|max_length[50]',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'status'          => 'required',
            'maksimal_respon' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
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

        $token = $this->generateUniqueToken();

        $identityTemplate = $this->identityTemplateFromRequest();
        $identityFields = $this->identityFieldsFromRequest($identityTemplate);
        $justificationConfig = $this->justificationConfigFromRequest();

        $this->linkModel->insert([
            'instrument_id'   => $instrumentId,
            'product_id'      => null,
            'token'           => $token,
            'mode'            => 'validasi_instrumen',
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'identity_template' => $identityTemplate,
            'identity_fields'  => json_encode($identityFields, JSON_UNESCAPED_UNICODE),
            'justification_config' => json_encode($justificationConfig, JSON_UNESCAPED_UNICODE),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        $this->workflowStatusService->markInstrumentInValidation($instrumentId);

        $statusLabel = status_display_label('Dalam Validasi Instrumen');

        return redirect()
            ->to(base_url('admin/instrument-links'))
            ->with('success', 'Link validasi instrumen berhasil dibuat. Status instrumen diperbarui menjadi ' . $statusLabel . '.');
    }

    public function edit($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/instrument-links'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        if (empty($link['identity_template'])) {
            $link['identity_template'] = RespondentIdentitySchema::defaultTemplateForLink($link);
        }

        $data = [
            'title'       => 'Edit Link Validasi Instrumen',
            'link'        => $link,
            'instruments' => $this->instrumentModel
                ->orderBy('judul', 'ASC')
                ->findAll(),
            'identityTemplates' => RespondentIdentitySchema::templates(),
            'identityFields' => RespondentIdentitySchema::fieldsForLink($link),
            'justificationTemplates' => JustificationSchema::templates(),
            'justificationConfig' => ['template' => JustificationSchema::defaultTemplateForLink($link)] + JustificationSchema::configForLink($link),
            'action'      => base_url('admin/instrument-links/' . $id),
            'method'      => 'put',
        ];

        return view('admin/links/form', $data);
    }

    public function update($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/instrument-links'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        $rules = [
            'instrument_id'   => 'required|integer',
            'judul_link'      => 'required|min_length[3]|max_length[255]',
            'sasaran'         => 'permit_empty|max_length[150]',
            'identity_template' => 'permit_empty|max_length[50]',
            'tanggal_mulai'   => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'status'          => 'required',
            'maksimal_respon' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
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

        $identityTemplate = $this->identityTemplateFromRequest();
        $identityFields = $this->identityFieldsFromRequest($identityTemplate);
        $justificationConfig = $this->justificationConfigFromRequest();

        $this->linkModel->update($id, [
            'instrument_id'   => $instrumentId,
            'judul_link'      => trim((string) $this->request->getPost('judul_link')),
            'sasaran'         => trim((string) $this->request->getPost('sasaran')),
            'identity_template' => $identityTemplate,
            'identity_fields'  => json_encode($identityFields, JSON_UNESCAPED_UNICODE),
            'justification_config' => json_encode($justificationConfig, JSON_UNESCAPED_UNICODE),
            'tanggal_mulai'   => $this->emptyToNull($this->request->getPost('tanggal_mulai')),
            'tanggal_selesai' => $this->emptyToNull($this->request->getPost('tanggal_selesai')),
            'status'          => trim((string) $this->request->getPost('status')),
            'maksimal_respon' => $this->emptyToNull($this->request->getPost('maksimal_respon')),
        ]);

        return redirect()
            ->to(base_url('admin/instrument-links'))
            ->with('success', 'Link validasi instrumen berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()
                ->to(base_url('admin/instrument-links'))
                ->with('error', 'Link validasi instrumen tidak ditemukan.');
        }

        $this->linkModel->delete($id);

        return redirect()
            ->to(base_url('admin/instrument-links'))
            ->with('success', 'Link validasi instrumen berhasil dihapus.');
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

        return isset($templates[$template]) ? $template : 'validator';
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
