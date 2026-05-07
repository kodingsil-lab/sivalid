<?php

namespace App\Models;

use CodeIgniter\Model;

class ResearchProductModel extends Model
{
    protected $table            = 'research_products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kode',
        'nama_produk',
        'jenis_produk',
        'deskripsi',
        'file_produk',
        'link_produk',
        'status',
    ];

    protected $useTimestamps = true;
}