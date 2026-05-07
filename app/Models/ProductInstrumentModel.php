<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductInstrumentModel extends Model
{
    protected $table            = 'product_instruments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'product_id',
        'instrument_id',
        'keterangan',
    ];

    protected $useTimestamps = true;

    public function getByProduct(int $productId): array
    {
        return $this->select(
            'product_instruments.*,
             instruments.kode,
             instruments.judul,
             instruments.jenis,
             instruments.status'
        )
            ->join('instruments', 'instruments.id = product_instruments.instrument_id')
            ->where('product_instruments.product_id', $productId)
            ->orderBy('product_instruments.id', 'ASC')
            ->findAll();
    }
}