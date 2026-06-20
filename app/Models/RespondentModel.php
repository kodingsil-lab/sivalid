<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class RespondentModel extends Model
{
    use BelongsToUser;

    protected $table            = 'respondents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'instrument_link_id',
        'bundle_id',
        'nama',
        'email',
        'bidang_keahlian',
        'instansi',
        'jenis_responden',
        'nim',
        'program_studi',
        'semester',
        'kelas',
        'identity_data',
        'tanggal_isi',
    ];

    protected $useTimestamps = true;

    public function hasSubmittedByEmail(int $instrumentLinkId, string $email): bool
    {
        $email = trim($email);

        if ($email === '') {
            return false;
        }

        return $this->where('instrument_link_id', $instrumentLinkId)
            ->where('email', $email)
            ->countAllResults() > 0;
    }

    public function hasSubmittedByNim(int $instrumentLinkId, string $nim): bool
    {
        $nim = trim($nim);

        if ($nim === '') {
            return false;
        }

        return $this->where('instrument_link_id', $instrumentLinkId)
            ->where('nim', $nim)
            ->countAllResults() > 0;
    }
}
