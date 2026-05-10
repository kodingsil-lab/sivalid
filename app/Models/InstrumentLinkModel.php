<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentLinkModel extends Model
{
    protected $table            = 'instrument_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'instrument_id',
        'product_id',
        'token',
        'mode',
        'judul_link',
        'sasaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'maksimal_respon',
    ];

    protected $useTimestamps = true;

    public function getWithInstrument(?string $mode = null): array
    {
        $builder = $this->select(
            'instrument_links.*,
             instruments.kode,
             instruments.judul,
             instruments.jenis,
             instruments.status AS instrument_status,
             research_products.kode AS product_kode,
             research_products.nama_produk,
             research_products.jenis_produk'
        )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->join('research_products', 'research_products.id = instrument_links.product_id', 'left');

        if ($mode !== null) {
            $builder->where('instrument_links.mode', $mode);
        }

        return $builder
            ->orderBy('instrument_links.id', 'DESC')
            ->findAll();
    }

    public function paginateWithInstrument(?string $mode = null, ?int $perPage = null, string $group = 'instrument_links'): array
    {
        $builder = $this->select(
            'instrument_links.*,
             instruments.kode,
             instruments.judul,
             instruments.jenis,
             instruments.status AS instrument_status,
             research_products.kode AS product_kode,
             research_products.nama_produk,
             research_products.jenis_produk'
        )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->join('research_products', 'research_products.id = instrument_links.product_id', 'left');

        if ($mode !== null) {
            $builder->where('instrument_links.mode', $mode);
        }

        return $builder
            ->orderBy('instrument_links.id', 'DESC')
            ->paginate($perPage, $group);
    }

    public function findByToken(string $token): ?array
    {
        return $this->select(
            'instrument_links.*,
             instruments.kode,
             instruments.judul,
             instruments.jenis,
             instruments.sasaran AS instrument_sasaran,
             instruments.pengantar,
             instruments.petunjuk,
             instruments.skala_min,
             instruments.skala_max,
             instruments.status AS instrument_status,
             research_products.kode AS product_kode,
             research_products.nama_produk,
             research_products.jenis_produk,
             research_products.deskripsi AS product_deskripsi,
             research_products.file_produk,
             research_products.link_produk,
             research_products.status AS product_status'
        )
            ->join('instruments', 'instruments.id = instrument_links.instrument_id')
            ->join('research_products', 'research_products.id = instrument_links.product_id', 'left')
            ->where('instrument_links.token', $token)
            ->first();
    }
}
