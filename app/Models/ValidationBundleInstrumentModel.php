<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationBundleInstrumentModel extends Model
{
    protected $table            = 'validation_bundle_instruments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'bundle_id',
        'instrument_id',
        'urutan',
        'pengantar_validasi',
        'petunjuk_validasi',
    ];

    protected $useTimestamps  = false;
    protected $createdField   = 'created_at';

    /**
     * Get all instruments for a bundle, ordered by instrument code, with instrument details.
     */
    public function getByBundle(int $bundleId): array
    {
        return $this->db->table('validation_bundle_instruments')
            ->select(
                'validation_bundle_instruments.*,
                 instruments.kode,
                 instruments.judul,
                 instruments.jenis,
                 instruments.sasaran AS instrument_sasaran,
                 instruments.pengantar,
                 instruments.petunjuk,
                 instruments.skala_min,
                 instruments.skala_max,
                 instruments.skala_labels,
                 instruments.status AS instrument_status'
            )
            ->join('instruments', 'instruments.id = validation_bundle_instruments.instrument_id')
            ->where('validation_bundle_instruments.bundle_id', $bundleId)
            ->orderBy('instruments.kode', 'ASC')
            ->orderBy('validation_bundle_instruments.urutan', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Replace all instruments in a bundle (delete existing, insert new).
     */
    public function syncBundle(int $bundleId, array $instrumentIds, array $validationTexts = []): void
    {
        $this->where('bundle_id', $bundleId)->delete();

        $instrumentIds = array_values(array_unique(array_map('intval', $instrumentIds)));

        if ($instrumentIds === []) {
            return;
        }

        $orderedRows = $this->db->table('instruments')
            ->select('id')
            ->whereIn('id', $instrumentIds)
            ->orderBy('kode', 'ASC')
            ->orderBy('judul', 'ASC')
            ->get()
            ->getResultArray();

        $orderedInstrumentIds = array_map(static fn(array $row): int => (int) $row['id'], $orderedRows);

        foreach ($orderedInstrumentIds as $index => $instrumentId) {
            $instrumentId = (int) $instrumentId;
            $texts = $validationTexts[$instrumentId] ?? [];

            $this->insert([
                'bundle_id'     => $bundleId,
                'instrument_id' => $instrumentId,
                'urutan'        => $index + 1,
                'pengantar_validasi' => trim((string) ($texts['pengantar_validasi'] ?? '')),
                'petunjuk_validasi'  => trim((string) ($texts['petunjuk_validasi'] ?? '')),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
