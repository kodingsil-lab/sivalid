<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class InstrumentIndicatorModel extends Model
{
    use BelongsToUser;

    protected $table            = 'instrument_indicators';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'instrument_id',
        'aspect_id',
        'indikator',
        'urutan',
    ];

    protected $useTimestamps = true;

    public function getWithRelations(?int $instrumentId = null): array
    {
        $builder = $this->select(
            'instrument_indicators.*, 
             instruments.kode, 
             instruments.judul,
             instrument_aspects.nama_aspek'
        )
            ->join('instruments', 'instruments.id = instrument_indicators.instrument_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_indicators.aspect_id');

        $this->applyOwnerToBuilder($builder, 'instrument_indicators.user_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_indicators.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->orderBy('instrument_indicators.urutan', 'ASC')
            ->findAll();
    }

    public function paginateWithRelations(?int $instrumentId = null, ?int $perPage = null, string $group = 'instrument_indicators'): array
    {
        $builder = $this->select(
            'instrument_indicators.*, 
             instruments.kode, 
             instruments.judul,
             instrument_aspects.nama_aspek'
        )
            ->join('instruments', 'instruments.id = instrument_indicators.instrument_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_indicators.aspect_id');

        $this->applyOwnerToBuilder($builder, 'instrument_indicators.user_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_indicators.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->orderBy('instrument_indicators.urutan', 'ASC')
            ->paginate($perPage, $group);
    }
}
