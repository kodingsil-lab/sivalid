<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentRevisionModel extends Model
{
    protected $table            = 'instrument_revisions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'instrument_item_id',
        'analysis_result_id',
        'pernyataan_lama',
        'pernyataan_baru',
        'alasan_revisi',
        'sumber_revisi',
        'tanggal_revisi',
    ];

    protected $useTimestamps = true;

    public function getByItem(int $itemId): array
    {
        return $this->where('instrument_item_id', $itemId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function getWithItem(?int $instrumentId = null): array
    {
        $builder = $this->select(
            'instrument_revisions.*,
             instrument_items.nomor,
             instrument_items.pernyataan,
             instrument_items.status,
             instruments.kode,
             instruments.judul,
             instrument_aspects.nama_aspek'
        )
            ->join('instrument_items', 'instrument_items.id = instrument_revisions.instrument_item_id')
            ->join('instruments', 'instruments.id = instrument_items.instrument_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_items.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instrument_revisions.id', 'DESC')
            ->findAll();
    }
}