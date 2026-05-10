<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationBundleSessionModel extends Model
{
    protected $table            = 'validation_bundle_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'bundle_id',
        'validator_nama',
        'validator_email',
        'validator_instansi',
        'validator_bidang_keahlian',
        'status_session',
        'started_at',
        'submitted_at',
    ];

    protected $useTimestamps = true;

    public function getByBundle(int $bundleId): array
    {
        return $this->where('bundle_id', $bundleId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function countByBundle(int $bundleId): int
    {
        return (int) $this->where('bundle_id', $bundleId)->countAllResults();
    }
}
