<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class ResponseModel extends Model
{
    use BelongsToUser;

    protected $table            = 'responses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'instrument_id',
        'instrument_link_id',
        'bundle_id',
        'product_id',
        'respondent_id',
        'mode',
        'status',
        'komentar_umum',
        'kesimpulan',
        'justification_data',
        'submitted_at',
    ];

    protected $useTimestamps = true;

    public function countByLink(int $instrumentLinkId): int
    {
        return (int) $this->where('instrument_link_id', $instrumentLinkId)->countAllResults();
    }

    public function getWithRespondentByLink(int $instrumentLinkId): array
    {
        return $this->scopeOwned('responses.user_id')
            ->select(
                'responses.*,
                 respondents.nama,
                 respondents.email,
                 respondents.bidang_keahlian,
                 respondents.instansi'
            )
            ->join('respondents', 'respondents.id = responses.respondent_id')
            ->where('responses.instrument_link_id', $instrumentLinkId)
            ->orderBy('responses.id', 'DESC')
            ->findAll();
    }
}
