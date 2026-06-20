<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class ManualValidInstrumentModel extends Model
{
    use BelongsToUser;

    protected $table            = 'manual_valid_instruments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'instrument_id',
        'source',
        'source_instrument_id',
    ];

    protected $useTimestamps = true;

    public function getWithInstrument(): array
    {
        return $this
            ->select(
                'manual_valid_instruments.*,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 instruments.sasaran,
                 instruments.skala_min,
                 instruments.skala_max,
                 instruments.status'
            )
            ->scopeOwned('manual_valid_instruments.user_id')
            ->join('instruments', 'instruments.id = manual_valid_instruments.instrument_id')
            ->orderBy('manual_valid_instruments.id', 'DESC')
            ->findAll();
    }
}
