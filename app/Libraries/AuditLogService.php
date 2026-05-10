<?php

namespace App\Libraries;

use App\Models\AuditLogModel;

/**
 * AuditLogService
 *
 * Facade ringan untuk menulis ke tabel audit_logs.
 * Gunakan konstanta ACTION_* sebagai nilai action yang konsisten.
 */
class AuditLogService
{
    // Aksi autentikasi
    public const ACTION_LOGIN  = 'login';
    public const ACTION_LOGOUT = 'logout';

    // Aksi instrumen
    public const ACTION_CREATE_INSTRUMENT = 'create_instrument';
    public const ACTION_UPDATE_INSTRUMENT = 'update_instrument';
    public const ACTION_DELETE_INSTRUMENT = 'delete_instrument';
    public const ACTION_MARK_INSTRUMENT_VALID = 'mark_instrument_valid';

    // Aksi link publik
    public const ACTION_CREATE_LINK = 'create_link';
    public const ACTION_DELETE_LINK = 'delete_link';

    // Aksi bundle validator
    public const ACTION_CREATE_BUNDLE = 'create_bundle';
    public const ACTION_UPDATE_BUNDLE = 'update_bundle';
    public const ACTION_DELETE_BUNDLE = 'delete_bundle';
    public const ACTION_REVOKE_BUNDLE_TOKEN = 'revoke_bundle_token';
    public const ACTION_ACTIVATE_BUNDLE_TOKEN = 'activate_bundle_token';
    public const ACTION_BUNDLE_SESSION_START = 'bundle_session_start';
    public const ACTION_BUNDLE_SUBMIT_FINAL = 'bundle_submit_final';
    public const ACTION_BUNDLE_TOKEN_DENIED = 'bundle_token_denied';

    // Aksi submit publik
    public const ACTION_PUBLIC_SUBMIT = 'public_submit';

    // Aksi analisis
    public const ACTION_PROCESS_ANALYSIS = 'process_analysis';

    // Aksi revisi butir
    public const ACTION_CREATE_REVISION = 'create_revision';

    // Aksi hasil pengisian
    public const ACTION_DELETE_SUBMISSION = 'delete_submission';

    // Tipe entitas
    public const ENTITY_INSTRUMENT  = 'instrument';
    public const ENTITY_LINK        = 'link';
    public const ENTITY_RESPONSE    = 'response';
    public const ENTITY_REVISION    = 'revision';
    public const ENTITY_ANALYSIS    = 'analysis';
    public const ENTITY_BUNDLE      = 'bundle';
    public const ENTITY_BUNDLE_TOKEN = 'bundle_token';
    public const ENTITY_BUNDLE_SESSION = 'bundle_session';

    protected AuditLogModel $model;

    public function __construct()
    {
        $this->model = new AuditLogModel();
    }

    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        array $context = []
    ): void {
        $this->model->log($action, $entityType, $entityId, $description, $context);
    }
}
