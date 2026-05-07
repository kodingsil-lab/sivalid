<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalysisResultModel extends Model
{
    protected $table            = 'analysis_results';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'instrument_id',
        'instrument_link_id',
        'product_id',
        'mode',
        'jumlah_responden',
        'jumlah_butir',
        'total_skor',
        'skor_maksimal',
        'rata_rata',
        'persentase',
        'kategori',
        'catatan',
    ];

    protected $useTimestamps = true;

    public function getLatestByLink(int $instrumentLinkId): ?array
    {
        return $this->where('instrument_link_id', $instrumentLinkId)
            ->orderBy('id', 'DESC')
            ->first();
    }
}