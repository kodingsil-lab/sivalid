<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationBundleInstrumentProgressModel extends Model
{
    protected $table            = 'validation_bundle_instrument_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'session_id',
        'instrument_id',
        'status',
        'kesimpulan',
        'komentar_umum',
        'saved_at',
    ];

    protected $useTimestamps = true;

    public function getBySessionAndInstrument(int $sessionId, int $instrumentId): ?array
    {
        return $this->where('session_id', $sessionId)
            ->where('instrument_id', $instrumentId)
            ->first();
    }

    /**
     * Upsert a progress row for a given session + instrument.
     */
    public function saveProgress(int $sessionId, int $instrumentId, array $data): void
    {
        $existing = $this->where('session_id', $sessionId)
            ->where('instrument_id', $instrumentId)
            ->first();

        $data['saved_at'] = date('Y-m-d H:i:s');

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert(array_merge($data, [
                'session_id'    => $sessionId,
                'instrument_id' => $instrumentId,
            ]));
        }
    }

    /**
     * Get all progress rows for a session, keyed by instrument_id.
     */
    public function getBySession(int $sessionId): array
    {
        $rows = $this->where('session_id', $sessionId)->findAll();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['instrument_id']] = $row;
        }

        return $result;
    }
}
