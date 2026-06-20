<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class InstrumentAspectModel extends Model
{
    use BelongsToUser;

    protected $table            = 'instrument_aspects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
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

        $this->applyOwnerToBuilder($builder, 'instrument_aspects.user_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_aspects.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->findAll();
    }

    public function paginateWithInstrument(?int $instrumentId = null, ?int $perPage = null, string $group = 'instrument_aspects'): array
    {
        $builder = $this->select('instrument_aspects.*, instruments.kode, instruments.judul')
            ->join('instruments', 'instruments.id = instrument_aspects.instrument_id');

        $this->applyOwnerToBuilder($builder, 'instrument_aspects.user_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_aspects.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->paginate($perPage, $group);
    }
}
