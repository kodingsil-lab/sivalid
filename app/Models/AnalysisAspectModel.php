<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalysisAspectModel extends Model
{
    protected $table            = 'analysis_aspects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'analysis_result_id',
        'aspect_id',
        'total_skor',
        'skor_maksimal',
        'rata_rata',
        'persentase',
        'kategori',
    ];

    protected $useTimestamps = true;

    public function getByAnalysis(int $analysisResultId): array
    {
        return $this->select(
            'analysis_aspects.*,
             instrument_aspects.nama_aspek,
             instrument_aspects.urutan'
        )
            ->join('instrument_aspects', 'instrument_aspects.id = analysis_aspects.aspect_id')
            ->where('analysis_aspects.analysis_result_id', $analysisResultId)
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->findAll();
    }
}