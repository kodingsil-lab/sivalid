<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Concerns\BelongsToUser;

class InstrumentItemModel extends Model
{
    use BelongsToUser;

    protected $table            = 'instrument_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'instrument_id',
        'aspect_id',
        'indicator_id',
        'nomor',
        'pernyataan',
        'sumber_dokumen',
        'skor_1_deskripsi',
        'skor_2_deskripsi',
        'skor_3_deskripsi',
        'skor_4_deskripsi',
        'skor_5_deskripsi',
        'tipe_butir',
        'wajib',
        'urutan',
        'status',
    ];

    protected $useTimestamps = true;

    public function usableStatuses(): array
    {
        return ['Aktif', 'Direvisi'];
    }

    public function getWithRelations(?int $instrumentId = null): array
    {
        $builder = $this->select(
            'instrument_items.*,
             instruments.kode,
             instruments.judul,
             instrument_aspects.nama_aspek,
             instrument_indicators.indikator'
        )
            ->join('instruments', 'instruments.id = instrument_items.instrument_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id')
            ->join('instrument_indicators', 'instrument_indicators.id = instrument_items.indicator_id', 'left');

        $this->applyOwnerToBuilder($builder, 'instrument_items.user_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_items.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }

    public function paginateWithRelations(?int $instrumentId = null, ?int $perPage = null, string $group = 'instrument_items'): array
    {
        $builder = $this->select(
            'instrument_items.*,
             instruments.kode,
             instruments.judul,
             instrument_aspects.nama_aspek,
             instrument_indicators.indikator'
        )
            ->join('instruments', 'instruments.id = instrument_items.instrument_id')
            ->join('instrument_aspects', 'instrument_aspects.id = instrument_items.aspect_id')
            ->join('instrument_indicators', 'instrument_indicators.id = instrument_items.indicator_id', 'left');

        $this->applyOwnerToBuilder($builder, 'instrument_items.user_id');

        if ($instrumentId !== null) {
            $builder->where('instrument_items.instrument_id', $instrumentId);
        }

        return $builder
            ->orderBy('instruments.judul', 'ASC')
            ->orderBy('instrument_aspects.urutan', 'ASC')
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->paginate($perPage, $group);
    }
}
