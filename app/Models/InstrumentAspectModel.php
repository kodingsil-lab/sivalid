<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentAspectModel extends Model
{
    protected $table            = 'instrument_aspects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'instrument_id',
        'nama_aspek',
        'deskripsi',
        'urutan',
    ];

    protected $useTimestamps = true;

    public function getWithInstrument(?int $instrumentId = null): array
    {
        $builder = $this->select('instrument_aspects.*, instruments.kode, instruments.judul')
            ->join('instruments', 'instruments.id = instrument_aspects.instrument_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_aspects.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->findAll();
    }
}