<?php

namespace App\Models;

use CodeIgniter\Model;

class ResponseAnswerModel extends Model
{
    protected $table            = 'response_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'response_id',
        'instrument_item_id',
        'skor',
        'jawaban_teks',
        'komentar',
    ];

    protected $useTimestamps = true;

    public function getByResponse(int $responseId): array
    {
        return $this->select(
            'response_answers.*,
             instrument_items.nomor,
             instrument_items.pernyataan,
             instrument_items.tipe_butir'
        )
            ->join('instrument_items', 'instrument_items.id = response_answers.instrument_item_id')
            ->where('response_answers.response_id', $responseId)
            ->orderBy('instrument_items.urutan', 'ASC')
            ->orderBy('instrument_items.nomor', 'ASC')
            ->findAll();
    }
}