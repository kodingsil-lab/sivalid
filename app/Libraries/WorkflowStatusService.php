<?php

namespace App\Libraries;

use App\Models\InstrumentModel;
use App\Models\ResearchProductModel;

class WorkflowStatusService
{
    protected InstrumentModel $instrumentModel;
    protected ResearchProductModel $productModel;

    public function __construct()
    {
        $this->instrumentModel = new InstrumentModel();
        $this->productModel    = new ResearchProductModel();
    }

    public function markInstrumentInValidation(int $instrumentId): void
    {
        $this->updateInstrumentStatusIfAllowed($instrumentId, [
            'Draft',
            'Aktif',
            'Perlu Revisi',
            'Direvisi',
            'Dalam Validasi Instrumen',
        ], 'Dalam Validasi Instrumen');
    }

    public function markInstrumentNeedRevision(int $instrumentId): void
    {
        $this->updateInstrumentStatusIfAllowed($instrumentId, [
            'Draft',
            'Aktif',
            'Dalam Validasi Instrumen',
            'Direvisi',
            'Layak Ditetapkan Valid',
        ], 'Perlu Revisi');
    }

    public function markInstrumentReadyToSetValid(int $instrumentId): void
    {
        $this->updateInstrumentStatusIfAllowed($instrumentId, [
            'Dalam Validasi Instrumen',
            'Direvisi',
            'Perlu Revisi',
            'Aktif',
            'Draft',
        ], 'Layak Ditetapkan Valid');
    }

    public function markInstrumentRevised(int $instrumentId): void
    {
        $this->updateInstrumentStatusIfAllowed($instrumentId, [
            'Perlu Revisi',
            'Dalam Validasi Instrumen',
            'Layak Ditetapkan Valid',
            'Draft',
            'Aktif',
        ], 'Direvisi');
    }

    public function markInstrumentValid(int $instrumentId): void
    {
        $this->updateInstrumentStatusIfAllowed($instrumentId, [
            'Draft',
            'Aktif',
            'Dalam Validasi Instrumen',
            'Perlu Revisi',
            'Direvisi',
            'Layak Ditetapkan Valid',
            'Valid',
            'Siap Disebar',
        ], 'Valid');
    }

    public function markInstrumentReadyToShare(int $instrumentId): void
    {
        $this->updateInstrumentStatusIfAllowed($instrumentId, [
            'Valid',
            'Siap Disebar',
        ], 'Siap Disebar');
    }

    public function markProductInValidation(int $productId): void
    {
        $this->updateProductStatusIfAllowed($productId, [
            'Draft',
            'Aktif',
            'Perlu Revisi',
            'Dalam Validasi Produk',
            'Draft Produk',
            'Siap Divalidasi',
            'Sedang Divalidasi',
        ], 'Dalam Validasi Produk');
    }

    public function markProductValidated(string $category, int $productId): void
    {
        if (in_array($category, ['Sangat Layak', 'Layak'], true)) {
            $this->updateProductStatusIfAllowed($productId, [
                'Draft',
                'Aktif',
                'Dalam Validasi Produk',
                'Perlu Revisi',
                'Layak',
                'Draft Produk',
                'Siap Divalidasi',
                'Sedang Divalidasi',
            ], 'Layak');

            return;
        }

        $this->updateProductStatusIfAllowed($productId, [
            'Draft',
            'Aktif',
            'Dalam Validasi Produk',
            'Layak',
            'Perlu Revisi',
            'Draft Produk',
            'Siap Divalidasi',
            'Sedang Divalidasi',
        ], 'Perlu Revisi');
    }

    private function updateInstrumentStatusIfAllowed(int $instrumentId, array $allowedCurrentStatuses, string $newStatus): void
    {
        $instrument = $this->instrumentModel->find($instrumentId);

        if (!$instrument) {
            return;
        }

        $currentStatus = $instrument['status'] ?? null;

        if (!in_array($currentStatus, $allowedCurrentStatuses, true)) {
            return;
        }

        $this->instrumentModel->update($instrumentId, [
            'status' => $newStatus,
        ]);
    }

    private function updateProductStatusIfAllowed(int $productId, array $allowedCurrentStatuses, string $newStatus): void
    {
        $product = $this->productModel->find($productId);

        if (!$product) {
            return;
        }

        $currentStatus = $product['status'] ?? null;

        if (!in_array($currentStatus, $allowedCurrentStatuses, true)) {
            return;
        }

        $this->productModel->update($productId, [
            'status' => $newStatus,
        ]);
    }
}
