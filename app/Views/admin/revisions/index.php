<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Revisi Butir Instrumen</h2>
            <div class="text-muted mt-1">Tinjau rekomendasi revisi dan riwayat perubahan butir instrumen.</div>
        </div>
        <?php if (!empty($instrumentId)): ?>
            <div class="col-auto ms-auto">
                <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                    Lihat Butir
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

<div class="card">
    <form action="<?= base_url('admin/instrument-revisions') ?>" method="get" class="search-form search-form-wide">
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

<div class="card">
    <h3>Butir yang Direkomendasikan untuk Direvisi</h3>
    <p>
        Daftar ini diambil dari hasil analisis per butir dengan rekomendasi
        <strong>Revisi kecil</strong>, <strong>Revisi besar</strong>, atau <strong>Ganti atau hapus</strong>.
    </p>

    <?php if (empty($revisionCandidates)): ?>
        <div class="empty-state">
            Belum ada butir yang direkomendasikan untuk revisi.
        </div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Instrumen</th>
                    <th>Aspek</th>
                    <th>Butir</th>
                    <th style="width: 110px;">Rata-Rata</th>
                    <th style="width: 140px;">Kategori</th>
                    <th style="width: 140px;">Rekomendasi</th>
                    <th style="width: 120px;">Status Butir</th>
                    <th class="table-actions-cell">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($revisionCandidates as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <strong><?= esc($item['kode']) ?></strong><br>
                            <?= esc($item['judul']) ?>
                        </td>
                        <td><?= esc($item['nama_aspek']) ?></td>
                        <td>
                            <strong>Butir <?= esc($item['nomor']) ?></strong><br>
                            <?= nl2br(esc($item['pernyataan'])) ?>
                        </td>
                        <td><?= esc($item['rata_rata']) ?></td>
                        <td><?= esc($item['kategori']) ?></td>
                        <td><strong><?= esc($item['rekomendasi']) ?></strong></td>
                        <td><span class="<?= esc(status_badge_class($item['item_status'] ?? '')) ?>"><?= esc($item['item_status']) ?></span></td>
                        <td class="table-actions-cell">
                            <div class="table-actions">
                                <a
                                    href="<?= base_url('admin/instrument-revisions/new?item_id=' . $item['instrument_item_id'] . '&analysis_result_id=' . $item['analysis_result_id']) ?>"
                                    class="btn btn-primary"
                                >
                                    Revisi
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Riwayat Revisi Butir</h3>

    <?php if (empty($revisions)): ?>
        <div class="empty-state">
            Belum ada riwayat revisi butir.
        </div>
    <?php else: ?>
        <?php
        $currentPage = isset($pager) ? $pager->getCurrentPage($pagerGroup) : 1;
        $perPage = isset($pager) ? $pager->getPerPage($pagerGroup) : 0;
        $total = isset($pager) ? $pager->getTotal($pagerGroup) : count($revisions);
        $offset = $perPage > 0 ? (($currentPage - 1) * $perPage) : 0;
        $firstItem = $total > 0 && $perPage > 0 ? $offset + 1 : 0;
        $lastItem = $total > 0 && $perPage > 0 ? min($currentPage * $perPage, $total) : $total;
        ?>
        <div class="table-responsive">
        <table class="table table-vcenter table-hover table-sm">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Instrumen</th>
                    <th>Butir</th>
                    <th>Pernyataan Lama</th>
                    <th>Pernyataan Baru</th>
                    <th>Alasan Revisi</th>
                    <th style="width: 130px;">Tanggal</th>
                    <th class="table-actions-cell">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($revisions as $index => $revision): ?>
                    <tr>
                        <td><?= $offset + $index + 1 ?></td>
                        <td>
                            <strong><?= esc($revision['kode']) ?></strong><br>
                            <?= esc($revision['judul']) ?>
                        </td>
                        <td>
                            Butir <?= esc($revision['nomor']) ?><br>
                            <span class="<?= esc(status_badge_class($revision['status'] ?? '')) ?>"><?= esc($revision['status']) ?></span>
                        </td>
                        <td><?= nl2br(esc($revision['pernyataan_lama'])) ?></td>
                        <td><?= nl2br(esc($revision['pernyataan_baru'])) ?></td>
                        <td><?= nl2br(esc($revision['alasan_revisi'] ?: '-')) ?></td>
                        <td><?= esc($revision['tanggal_revisi'] ?: '-') ?></td>
                        <td class="table-actions-cell">
                            <div class="table-actions">
                                <form
                                    action="<?= base_url('admin/instrument-revisions/' . $revision['id']) ?>"
                                    method="post"
                                    class="action-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus riwayat revisi ini? Redaksi butir saat ini tidak akan dikembalikan otomatis.')"
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 px-3 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan <?= esc((string) $firstItem) ?> sampai <?= esc((string) $lastItem) ?> dari <?= esc((string) $total) ?> entri
                </div>
                <div><?= $pager->links($pagerGroup, 'default_full') ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
