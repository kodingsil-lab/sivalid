<?php

namespace App\Models;

use CodeIgniter\Model;

class ResponseModel extends Model
{
    protected $table            = 'responses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'instrument_id',
        'instrument_link_id',
        'product_id',
        'respondent_id',
        'mode',
        'status',
        'komentar_umum',
        'kesimpulan',
        'submitted_at',
    ];

    protected $useTimestamps = true;

    public function countByLink(int $instrumentLinkId): int
    {
        return (int) $this->where('instrument_link_id', $instrumentLinkId)->countAllResults();
    }

    public function getWithRespondentByLink(int $instrumentLinkId): array
    {
        return $this->select(
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