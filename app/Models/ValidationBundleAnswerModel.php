<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationBundleAnswerModel extends Model
{
    protected $table            = 'validation_bundle_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'session_id',
        'instrument_id',
        'instrument_item_id',
        'skor',
        'jawaban_teks',
        'komentar',
    ];

    protected $useTimestamps = true;

    /**
     * Get all answers for a session + instrument as a lookup: [item_id => answer_row].
     */
    public function getBySessionAndInstrument(int $sessionId, int $instrumentId): array
    {
        $rows = $this->where('session_id', $sessionId)
            ->where('instrument_id', $instrumentId)
            ->findAll();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['instrument_item_id']] = $row;
        }

        return $result;
    }

    /**
     * Replace all answers for a session + instrument in one transaction.
     */
    public function saveForSession(int $sessionId, int $instrumentId, array $answers): void
    {
        $this->where('session_id', $sessionId)
            ->where('instrument_id', $instrumentId)
            ->delete();

        $now = date('Y-m-d H:i:s');

        foreach ($answers as $answer) {
            if (empty($answer['instrument_item_id'])) {
                continue;
            }

            $this->insert([
                'session_id'         => $sessionId,
                'instrument_id'      => $instrumentId,
                'instrument_item_id' => (int) $answer['instrument_item_id'],
                'skor'               => $answer['skor'] !== '' ? (int) $answer['skor'] : null,
                'jawaban_teks'       => $answer['jawaban_teks'] ?? null,
                'komentar'           => $answer['komentar'] ?? null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    /**
     * Get all answers for a session (all instruments), grouped by instrument_id.
     * Returns [instrument_id => [item_id => answer_row]].
     */
    public function getGroupedByInstrument(int $sessionId): array
    {
        $rows = $this->where('session_id', $sessionId)->findAll();

        $result = [];
        foreach ($rows as $row) {
            $instrId = (int) $row['instrument_id'];
            $itemId  = (int) $row['instrument_item_id'];
            $result[$instrId][$itemId] = $row;
        }

        return $result;
    }
}
