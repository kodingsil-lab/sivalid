<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$itemLayout = isset($itemLayout) && is_array($itemLayout) ? $itemLayout : instrument_item_entry_layout($selectedInstrument['jenis'] ?? '');
$layoutType = (string) ($itemLayout['type'] ?? 'standard');
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc((string) ($itemLayout['item_label'] ?? 'Butir Pernyataan')) ?> Instrumen</h2>
            <div class="text-muted mt-1">Kelola isian butir sesuai jenis instrumen terpilih.</div>
        </div>
        <?php if (!empty($instrumentId)): ?>
            <div class="col-auto ms-auto">
                <a href="<?= base_url('admin/instrument-items/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
                    + Tambah Butir
                </a>

                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalImportButir">
                    Import Excel
                </button>

                <a href="<?= base_url('admin/instrument-aspects?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                    Lihat Kisi-Kisi
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
    <form action="<?= base_url('admin/instrument-items') ?>" method="get" class="search-form search-form-wide">
        <select name="instrument_id" class="form-control" style="min-width: 420px;">
            <option value="">-- Semua Instrumen --</option>
            <?php foreach ($instruments as $instrument): ?>
                <option value="<?= $instrument['id'] ?>" <?= (int) ($instrumentId ?? 0) === (int) $instrument['id'] ? 'selected' : '' ?>>
                    <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>
    </div>
</div>

<?php if (empty($instrumentId)): ?>
    <div class="empty-state">
        Silakan pilih instrumen terlebih dahulu untuk menampilkan butir pernyataan.
    </div>
<?php elseif (empty($items)): ?>
    <div class="empty-state">
        Belum ada butir pernyataan pada instrumen ini.
        <br><br>
        <a href="<?= base_url('admin/instrument-items/new?instrument_id=' . $instrumentId) ?>" class="btn btn-primary">
            Tambah Butir Pertama
        </a>
    </div>
<?php else: ?>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Daftar <?= esc((string) ($itemLayout['item_label'] ?? 'Butir Pernyataan')) ?></h3>
        </div>
        <div class="card-body p-0">

        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th style="width: 180px;"><?= esc((string) ($itemLayout['aspect_label'] ?? 'Aspek')) ?></th>
                    <th style="width: 240px;"><?= esc((string) ($itemLayout['indicator_label'] ?? 'Indikator')) ?></th>
                    <th><?= esc((string) ($itemLayout['item_label'] ?? 'Butir Pernyataan')) ?></th>
                    <?php if (!empty($itemLayout['show_source_document'])): ?>
                        <th style="width: 140px;">Sumber Dokumen</th>
                    <?php endif; ?>
                    <?php if (!empty($itemLayout['show_rubric_scores'])): ?>
                        <th style="width: 240px;">Deskripsi Skor</th>
                    <?php endif; ?>
                    <th style="width: 110px;">Tipe</th>
                    <th style="width: 90px;">Status</th>
                    <th class="table-actions-cell">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?= esc($item['nomor']) ?>
                        </td>
                        <td>
                            <?= esc($item['nama_aspek']) ?>
                        </td>
                        <td>
                            <?= !empty($item['indikator']) ? nl2br(esc($item['indikator'])) : '<em>-</em>' ?>
                        </td>
                        <td>
                            <?= nl2br(esc($item['pernyataan'])) ?>
                            <br>
                            <small>
                                Urutan: <?= esc($item['urutan']) ?> |
                                <?= (int) $item['wajib'] === 1 ? 'Wajib' : 'Tidak wajib' ?>
                            </small>
                        </td>
                        <?php if (!empty($itemLayout['show_source_document'])): ?>
                            <td><?= esc(document_review_source_label($item['sumber_dokumen'] ?? '')) ?></td>
                        <?php endif; ?>
                        <?php if (!empty($itemLayout['show_rubric_scores'])): ?>
                            <td>
                                <?php for ($score = 1; $score <= 5; $score++): ?>
                                    <?php $field = 'skor_' . $score . '_deskripsi'; ?>
                                    <div><strong>Skor <?= $score ?>:</strong> <?= esc((string) ($item[$field] ?? '-')) ?></div>
                                <?php endfor; ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= esc(title_case_label((string) ($item['tipe_butir'] ?? '-'))) ?>
                        </td>
                        <td>
                            <span class="<?= esc(status_badge_class($item['status'] ?? '')) ?>"><?= esc($item['status']) ?></span>
                        </td>
                        <td class="table-actions-cell">
                            <div class="table-actions">
                                <a href="<?= base_url('admin/instrument-items/' . $item['id'] . '/edit') ?>" class="btn btn-warning">
                                    Edit
                                </a>

                                <form
                                    action="<?= base_url('admin/instrument-items/' . $item['id']) ?>"
                                    method="post"
                                    class="action-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus butir ini?')"
                                >
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <?php if (isset($pager) && !empty($pagerGroup)): ?>
            <?php
            $currentPage = $pager->getCurrentPage($pagerGroup);
            $perPage = $pager->getPerPage($pagerGroup);
            $total = $pager->getTotal($pagerGroup);
            $firstItem = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
            $lastItem = min($currentPage * $perPage, $total);
            ?>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 px-3 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan <?= esc((string) $firstItem) ?> sampai <?= esc((string) $lastItem) ?> dari <?= esc((string) $total) ?> entri
                </div>
                <div><?= $pager->links($pagerGroup, 'default_full') ?></div>
            </div>
        <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($instrumentId)): ?>
    <div class="modal modal-blur fade" id="modalImportButir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Butir dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/instrument-items/import') ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="instrument_id" value="<?= (int) $instrumentId ?>">

                        <div class="mb-3">
                            <label class="form-label">File Excel (.xlsx)</label>
                            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                        </div>

                        <div class="mb-3">
                            <a href="<?= base_url('admin/instrument-items/import-template?instrument_id=' . (int) $instrumentId) ?>" class="btn btn-light">
                                Download Template Excel
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th><?= esc((string) ($itemLayout['aspect_label'] ?? 'Aspek')) ?></th>
                                        <?php if ($layoutType === 'standard' || str_contains($layoutType, 'questionnaire')): ?>
                                            <th>Indikator Kisi-Kisi</th>
                                        <?php endif; ?>
                                        <th><?= esc((string) ($itemLayout['excel_item_header'] ?? $itemLayout['item_label'] ?? 'Butir Pernyataan')) ?></th>
                                        <?php if (!empty($itemLayout['show_source_document'])): ?>
                                            <th>Sumber Dokumen</th>
                                        <?php endif; ?>
                                        <?php if (!empty($itemLayout['show_rubric_scores'])): ?>
                                            <?php for ($score = 1; $score <= 5; $score++): ?>
                                                <th>Skor <?= $score ?></th>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                        <th>Tipe Butir</th>
                                        <th>Wajib</th>
                                        <th>Urutan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><?= !empty($itemLayout['show_rubric_scores']) ? 'Isi Tulisan' : 'Pendahuluan' ?></td>
                                        <?php if ($layoutType === 'standard' || str_contains($layoutType, 'questionnaire')): ?>
                                            <td>Kejelasan latar belakang dan urgensi pengembangan model pembelajaran.</td>
                                        <?php endif; ?>
                                        <td><?= esc((string) ($itemLayout['item_placeholder'] ?? 'Butir contoh.')) ?></td>
                                        <?php if (!empty($itemLayout['show_source_document'])): ?>
                                            <td>RPS</td>
                                        <?php endif; ?>
                                        <?php if (!empty($itemLayout['show_rubric_scores'])): ?>
                                            <?php for ($score = 1; $score <= 5; $score++): ?>
                                                <td>Deskripsi skor <?= $score ?></td>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                        <td>skala</td>
                                        <td>Ya</td>
                                        <td>1</td>
                                        <td>Aktif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-muted small mt-2">
                            Aspek wajib sudah ada pada kisi-kisi. Indikator boleh kosong, tetapi jika diisi harus sama dengan indikator pada aspek tersebut. Butir dengan pernyataan yang sama akan dilewati.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
