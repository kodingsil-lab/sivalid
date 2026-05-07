<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentModel extends Model
{
    protected $table            = 'instruments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kode',
        'judul',
        'jenis',
        'sasaran',
        'deskripsi',
        'pengantar',
        'petunjuk',
        'skala_min',
        'skala_max',
        'status',
    ];

    protected $useTimestamps = true;
}