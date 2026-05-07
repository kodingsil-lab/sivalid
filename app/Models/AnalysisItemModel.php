<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalysisItemModel extends Model
{
    protected $table            = 'analysis_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'analysis_result_id',
        'instrument_item_id',
        'total_skor',
        'rata_rata',
        'kategori',
        'rekomendasi',
    ];

    protected $useTimestamps = true;

    public function getByAnalysis(int $analysisResultId): array
    {
        return $this->select(
            'analysis_items.*,
             instrument_items.nomor,
             instrument_items.pernyataan,
             instrument_items.urutan,
             instrument_aspects.nama_aspek'
        )
            ->join('instrument_items', 'instrument_items.id = analysis_items.instrument_item_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id')
            ->where('analysis_items.analysis_result_id', $analysisResultId)
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }
}