<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentAttachmentModel extends Model
{
    protected $table            = 'instrument_attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'instrument_id',
        'title',
        'file_path',
        'sort_order',
    ];

    protected $useTimestamps = true;

    public function getByInstrument(int $instrumentId): array
    {
        if ($instrumentId <= 0) {
            return [];
        }

        return $this
            ->where('instrument_id', $instrumentId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
