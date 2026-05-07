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

    protected AuditLogModel $model;

    public function __construct()
    {
        $this->model = new AuditLogModel();
    }

    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null
    ): void {
        $this->model->log($action, $entityType, $entityId, $description);
    }
}
