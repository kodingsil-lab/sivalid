<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'user_name',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
    ];

    protected $useTimestamps  = false;
    protected $returnType     = 'array';

    /**
     * Simpan log audit. updated_at tidak dipakai, hanya created_at diisi manual.
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null
    ): void {
        $session   = session();
        $userId    = $session->get('user_id');
        $userName  = $session->get('user_name');
        $ipAddress = service('request')->getIPAddress();

        $this->insert([
            'user_id'     => $userId ?: null,
            'user_name'   => $userName ?: null,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'description' => $description,
            'ip_address'  => $ipAddress,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
