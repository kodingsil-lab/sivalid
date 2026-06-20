<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class ValidationBundleModel extends Model
{
    use BelongsToUser;

    protected $table            = 'validation_bundles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'token',
        'token_access_mode',
        'judul',
        'deskripsi',
        'sasaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'token_expires_at',
        'token_revoked_at',
        'status',
        'maksimal_respon',
    ];

    protected $useTimestamps = true;

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    public function getWithInstrumentCount(): array
    {
        $builder = $this->db->table('validation_bundles')
            ->select('validation_bundles.*, COUNT(validation_bundle_instruments.id) AS jumlah_instrumen')
            ->join('validation_bundle_instruments', 'validation_bundle_instruments.bundle_id = validation_bundles.id', 'left')
            ->groupBy('validation_bundles.id')
            ->orderBy('validation_bundles.id', 'DESC');

        $this->applyOwnerToBuilder($builder, 'validation_bundles.user_id');

        return $builder
            ->get()
            ->getResultArray();
    }

    public function paginateWithInstrumentCount(?int $perPage = null, string $group = 'validation_bundles'): array
    {
        $builder = $this->select('validation_bundles.*, COUNT(validation_bundle_instruments.id) AS jumlah_instrumen')
            ->join('validation_bundle_instruments', 'validation_bundle_instruments.bundle_id = validation_bundles.id', 'left')
            ->groupBy('validation_bundles.id')
            ->orderBy('validation_bundles.id', 'DESC');

        $this->applyOwnerToBuilder($builder, 'validation_bundles.user_id');

        return $builder->paginate($perPage, $group);
    }
}
