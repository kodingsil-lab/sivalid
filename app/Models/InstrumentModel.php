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
        'pengantar',
        'petunjuk',
        'skala_min',
        'skala_max',
        'status',
    ];

    protected $useTimestamps = true;

    /**
     * Return instruments sorted by numeric sequence in code (e.g. 01, INS-002, A-10).
     */
    public function getOrderedByCodeSequence(): array
    {
        $rows = $this->findAll();

        usort($rows, static function (array $left, array $right): int {
            $leftCode = trim((string) ($left['kode'] ?? ''));
            $rightCode = trim((string) ($right['kode'] ?? ''));

            $leftNum = self::extractCodeNumber($leftCode);
            $rightNum = self::extractCodeNumber($rightCode);

            if ($leftNum !== $rightNum) {
                return $leftNum <=> $rightNum;
            }

            return strcasecmp($leftCode, $rightCode);
        });

        return $rows;
    }

    private static function extractCodeNumber(string $code): int
    {
        if ($code === '') {
            return PHP_INT_MAX;
        }

        if (preg_match('/(\d+)$/', $code, $matches) === 1) {
            return (int) $matches[1];
        }

        return PHP_INT_MAX;
    }
}
